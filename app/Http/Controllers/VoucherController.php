<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\TaxoList;
use App\Models\Voucher;
use App\Services\Voucher\VoucherValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    protected VoucherValidationService $voucherValidationService;

    public function __construct(VoucherValidationService $voucherValidationService)
    {
        $this->voucherValidationService = $voucherValidationService;
    }
    /**
     * Public list (ACTIVE only)
     */
    public function index()
    {
        $vouchers = Voucher::with('categories')
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'vouchers' => $vouchers,
                'pagination' => null
            ]
        ]);
    }

    /**
     * Public show by code
     */
    public function show($id)
    {
        $voucher = Voucher::with('categories')->findOrFail($id);

        if ($voucher->start_date && $voucher->start_date > now()) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher belum berlaku'
            ], 422);
        }

        if ($voucher->end_date && $voucher->end_date < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher sudah kadaluarsa'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $voucher
        ]);
    }

    /**
     * Admin: list all vouchers
     */
    public function all()
    {
        Voucher::updateStatus();

        $vouchers = Voucher::with('categories')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'vouchers' => $vouchers,
                'pagination' => null
            ]
        ]);
    }

    /**
     * Admin: show vouchers
     */
    public function adminShow($id)
    {
        $voucher = Voucher::with('categories')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $voucher
        ]);
    }

    /**
     * Admin: create voucher
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:vouchers,code',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'limit_user' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date_format:Y-m-d\TH:i',
            'end_date' => [
                'nullable',
                'date_format:Y-m-d\TH:i',
                function ($attribute, $value, $fail) use ($request) {
                    $startDate = $request->input('start_date');

                    if ($startDate && $value < $startDate) {
                        $fail('End date must be after or equal to start date.');
                    }
                }
            ],
            'min_purchase' => 'nullable|integer|min:0',
            'fk_category_id' => 'nullable|array',
            'fk_category_id.*' => 'exists:taxo_lists,id',
            'discount_type' => ['required', Rule::in(['PERCENTAGE', 'FIXED'])],
            'discount_value' => 'required|numeric|min:0',
        ]);

        $voucher = Voucher::create($data);

        if (!empty($data['fk_category_id'])) {
            $voucher->categories()->sync($data['fk_category_id']);
        }

        return response()->json([
            'message' => 'Voucher created successfully',
            'data' => $voucher->load('categories')
        ], 201);
    }

    /**
     * Admin: update voucher
     */
    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('vouchers', 'code')->ignore($voucher->id),
            ],
            'description' => 'nullable|string',
            'status' => 'sometimes|required|string',
            'limit_user' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date_format:Y-m-d\TH:i',
            'end_date' => [
                'nullable',
                'date_format:Y-m-d\TH:i',
                function ($attribute, $value, $fail) use ($request, $voucher) {
                    $startDate = $request->input('start_date') ?? $voucher->start_date;

                    if ($startDate && $value < $startDate) {
                        $fail('End date must be after or equal to start date.');
                    }
                }
            ],
            'min_purchase' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'fk_category_id' => 'nullable|array',
            'fk_category_id.*' => 'exists:taxo_lists,id',
            'discount_type' => ['sometimes', Rule::in(['PERCENTAGE', 'FIXED'])],
            'discount_value' => 'sometimes|required|numeric|min:0',
        ]);

        $voucher->update($data);

        if (isset($data['fk_category_id'])) {
            $voucher->categories()->sync($data['fk_category_id']);
        }

        return response()->json([
            'message' => 'Voucher updated successfully',
            'data' => $voucher->load('categories')
        ]);
    }

    /**
     * Admin: delete voucher
     */
    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Voucher deleted successfully'
        ]);
    }

    public function topCategories()
    {
        $categories = TaxoList::whereNull('parent')
            ->where('taxonomy_status', 'ACTIVE')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function applicable(Request $request)
    {
        $data = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'sub_total' => 'required|integer|min:0',
        ]);

        $user = auth('sanctum')->user();

        $vouchers = Voucher::availableForFrontend()->get();

        $validVouchers = $vouchers->filter(
            function (Voucher $voucher) use ($user, $data) {

                $result = $this->voucherValidationService->validate(
                    $voucher,
                    $user,
                    $data['product_ids'],
                    $data['sub_total']
                );

                return $result['valid'];
            }
        )->values();

        return response()->json([
            'success' => true,
            'data' => $validVouchers
        ]);
    }

    public function validateVoucher(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'sub_total' => 'required|integer|min:0',
        ]);

        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $voucher = Voucher::with('categories')
            ->where('code', $data['code'])
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak ditemukan'
            ], 404);
        }

        $result = $this->voucherValidationService->validate(
            $voucher,
            $user,
            $data['product_ids'],
            $data['sub_total']
        );

        if (!$result['valid']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'voucher' => $voucher,
                'discount_amount' => $result['discount'],
            ],
        ]);
    }
}
