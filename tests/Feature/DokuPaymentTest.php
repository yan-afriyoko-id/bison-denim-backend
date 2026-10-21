<?php

use App\Models\Order;
use App\Models\PaymentGroup;
use App\Models\User;
use App\Services\Payment\DokuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

const DOKU_CLIENT_ID = 'BRN-0001-1234567890';
const DOKU_SECRET_KEY = 'SK-test-secret';

beforeEach(function () {
    config([
        'settings' => [
            'doku_is_active' => 'true',
            'doku_client_id' => DOKU_CLIENT_ID,
            'doku_secret_key' => DOKU_SECRET_KEY,
            'doku_is_production' => 'false',
            'doku_payment_due_minutes' => '90',
        ],
        'services.doku.notification_url' => null,
    ]);
});

function makeDokuOrder(array $attributes = []): Order
{
    return Order::forceCreate(array_merge([
        'uuid' => (string) Str::uuid(),
        'fk_user_id' => User::factory()->create()->id,
        'order_number' => 'ORD-20261021-0001',
        'contact_email' => 'buyer@example.com',
        'contact_phone' => '081234567890',
        'shipping_first_name' => 'Budi',
        'shipping_last_name' => 'Santoso',
        'shipping_address' => 'Jl. Merdeka 1',
        'shipping_city' => 'Jakarta',
        'shipping_province' => 'DKI Jakarta',
        'shipping_postal_code' => '10110',
        'total_amount' => 150000,
        'payment_status' => 'PENDING',
        'status' => 'PENDING',
        'payment_method' => 'doku',
        'payment_snap_token' => 'https://sandbox.doku.com/checkout-link-v2/token123',
        'payment_reference_code' => 'token123',
    ], $attributes));
}

function signedDokuNotification(array $payload, string $path = '/api/doku/notification'): array
{
    $body = json_encode($payload);
    $requestId = (string) Str::uuid();
    $timestamp = now('UTC')->format('Y-m-d\TH:i:s\Z');

    return [$body, [
        'Client-Id' => DOKU_CLIENT_ID,
        'Request-Id' => $requestId,
        'Request-Timestamp' => $timestamp,
        'Signature' => DokuService::generateSignature(DOKU_CLIENT_ID, DOKU_SECRET_KEY, $requestId, $timestamp, $path, $body),
        'Content-Type' => 'application/json',
    ]];
}

function postDokuNotification($test, array $payload, ?array $headers = null)
{
    [$body, $signedHeaders] = signedDokuNotification($payload);

    return $test->call('POST', '/api/doku/notification', [], [], [], collect($headers ?? $signedHeaders)
        ->mapWithKeys(fn ($value, $key) => ['HTTP_' . strtoupper(str_replace('-', '_', $key)) => $value])
        ->put('CONTENT_TYPE', 'application/json')
        ->all(), $body);
}

it('generates the signature exactly like the official DOKU PHP sample', function () {
    $body = json_encode(['order' => ['amount' => 20000, 'invoice_number' => 'INV-1']]);
    $digest = base64_encode(hash('sha256', $body, true));
    $component = "Client-Id:cid\nRequest-Id:rid\nRequest-Timestamp:2020-09-22T01:51:00Z\nRequest-Target:/checkout/v1/payment\nDigest:" . $digest;
    $expected = 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $component, 'secret', true));

    expect(DokuService::generateSignature('cid', 'secret', 'rid', '2020-09-22T01:51:00Z', '/checkout/v1/payment', $body))
        ->toBe($expected);

    // GET requests carry no Digest component
    $getComponent = "Client-Id:cid\nRequest-Id:rid\nRequest-Timestamp:2020-09-22T01:51:00Z\nRequest-Target:/orders/v1/status/INV-1";
    expect(DokuService::generateSignature('cid', 'secret', 'rid', '2020-09-22T01:51:00Z', '/orders/v1/status/INV-1'))
        ->toBe('HMACSHA256=' . base64_encode(hash_hmac('sha256', $getComponent, 'secret', true)));
});

it('creates a checkout with a digest that matches the sent body', function () {
    Http::fake([
        'api-sandbox.doku.com/checkout/v1/payment' => Http::response([
            'message' => ['SUCCESS'],
            'response' => [
                'payment' => [
                    'token_id' => 'tok-1',
                    'url' => 'https://sandbox.doku.com/checkout-link-v2/tok-1',
                ],
            ],
        ]),
    ]);

    $result = DokuService::createCheckout([
        'order' => ['amount' => 150000, 'invoice_number' => 'ORD-20261021-0001'],
        'payment' => ['payment_due_date' => DokuService::paymentDueMinutes()],
    ]);

    expect($result['payment']['url'])->toBe('https://sandbox.doku.com/checkout-link-v2/tok-1');

    Http::assertSent(function ($request) {
        $body = $request->body();
        $expected = DokuService::generateSignature(
            DOKU_CLIENT_ID,
            DOKU_SECRET_KEY,
            $request->header('Request-Id')[0],
            $request->header('Request-Timestamp')[0],
            DokuService::CHECKOUT_PATH,
            $body
        );

        return $request->header('Signature')[0] === $expected
            && json_decode($body, true)['payment']['payment_due_date'] === 90;
    });
});

it('rejects a notification with an invalid signature', function () {
    $order = makeDokuOrder();
    [, $headers] = signedDokuNotification(['tampered' => true]);

    postDokuNotification($this, [
        'order' => ['invoice_number' => $order->order_number, 'amount' => 150000],
        'transaction' => ['status' => 'SUCCESS'],
    ], $headers)->assertStatus(401);

    expect($order->fresh()->payment_status)->toBe('PENDING');
});

it('marks the order paid on a SUCCESS notification and stays idempotent', function () {
    $order = makeDokuOrder();
    $payload = [
        'order' => ['invoice_number' => $order->order_number, 'amount' => 150000],
        'transaction' => ['status' => 'SUCCESS', 'original_request_id' => 'req-123'],
        'channel' => ['id' => 'VIRTUAL_ACCOUNT_BCA'],
    ];

    postDokuNotification($this, $payload)->assertOk();
    postDokuNotification($this, $payload)->assertOk();

    $order->refresh();
    expect($order->payment_status)->toBe('PAID')
        ->and($order->status)->toBe('PACKING')
        ->and($order->payment_reference_code)->toBe('req-123')
        ->and($order->payment_type)->toBe('VIRTUAL_ACCOUNT_BCA');
});

it('ignores FAILED notifications and amount mismatches', function () {
    $order = makeDokuOrder();

    postDokuNotification($this, [
        'order' => ['invoice_number' => $order->order_number, 'amount' => 150000],
        'transaction' => ['status' => 'FAILED'],
    ])->assertOk();

    postDokuNotification($this, [
        'order' => ['invoice_number' => $order->order_number, 'amount' => 1000],
        'transaction' => ['status' => 'SUCCESS'],
    ])->assertOk();

    expect($order->fresh()->payment_status)->toBe('PENDING');
});

it('cancels the order on an EXPIRED notification', function () {
    $order = makeDokuOrder();

    postDokuNotification($this, [
        'order' => ['invoice_number' => $order->order_number, 'amount' => 150000],
        'transaction' => ['status' => 'EXPIRED'],
    ])->assertOk();

    $order->refresh();
    expect($order->payment_status)->toBe('FAILED')
        ->and($order->status)->toBe('CANCELLED');
});

it('marks every order of a payment group paid', function () {
    $first = makeDokuOrder(['payment_snap_token' => null]);
    $second = makeDokuOrder([
        'order_number' => 'ORD-20261021-0002',
        'total_amount' => 50000,
        'payment_snap_token' => null,
    ]);

    $group = PaymentGroup::create([
        'uuid' => (string) Str::uuid(),
        'group_number' => 'PG-261021101010-ABCDEFGH',
        'gross_amount' => 200000,
        'payment_snap_token' => 'https://sandbox.doku.com/checkout-link-v2/tok-g',
        'status' => 'PENDING',
    ]);
    $group->orders()->attach([$first->id, $second->id]);

    postDokuNotification($this, [
        'order' => ['invoice_number' => $group->group_number, 'amount' => 200000],
        'transaction' => ['status' => 'SUCCESS'],
    ])->assertOk();

    expect($group->fresh()->status)->toBe('PAID')
        ->and($first->fresh()->payment_status)->toBe('PAID')
        ->and($second->fresh()->payment_status)->toBe('PAID');
});

it('exposes only active gateway names publicly', function () {
    config(['settings' => [
        'midtrans_is_active' => 'false',
        'xendit_is_active' => 'true',
        'xendit_secret_key' => 'xnd_secret',
        'doku_is_active' => 'true',
        'doku_secret_key' => DOKU_SECRET_KEY,
    ]]);

    $this->getJson('/api/payment/gateways')
        ->assertOk()
        ->assertExactJson(['gateways' => ['xendit', 'doku']]);
});

it('rejects Xendit webhooks without a valid callback token', function () {
    $order = makeDokuOrder(['payment_method' => 'xendit']);
    config(['settings' => ['xendit_callback_token' => 'cb-token']]);

    $payload = ['external_id' => $order->order_number, 'status' => 'PAID', 'id' => 'inv-1'];

    $this->postJson('/api/xendit/webhook', $payload)->assertStatus(401);
    $this->postJson('/api/xendit/webhook', $payload, ['x-callback-token' => 'wrong'])->assertStatus(401);
    expect($order->fresh()->payment_status)->toBe('PENDING');

    $this->postJson('/api/xendit/webhook', $payload, ['x-callback-token' => 'cb-token'])->assertOk();
    expect($order->fresh()->payment_status)->toBe('PAID');
});
