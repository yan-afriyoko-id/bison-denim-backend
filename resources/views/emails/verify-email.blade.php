@extends('emails.layouts.app', [
    'headerTitle' => 'Verify Your Email',
    'headerSubtitle' => 'Complete your registration'
])

@section('content')
    <h2>Welcome, {{ $notifiable->name }}!</h2>
    
    <p>
        Thank you for registering with <strong>{{ config('app.name') }}</strong>. 
        To get started, please verify your email address.
    </p>
    
    <p style="text-align: center;">
        <a href="{{ $verificationUrl }}" class="btn">
            Verify Email Address
        </a>
    </p>
    
    <div class="info-box">
        <strong>Link expires in 24 hours</strong> from when this email was sent.
    </div>
    
    <p>Or copy and paste this link in your browser:</p>
    
    <div class="link-copy">
        <p><strong>Verification Link:</strong></p>
        <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
    </div>
    
    <hr style="border: none; border-top: 1px solid #e9ecef; margin: 28px 0;">
    
    <h3 style="color: #4c6ef5; font-size: 15px; margin-bottom: 10px; font-weight: 600;">Securing Your Account</h3>
    
    <p style="font-size: 14px;">
        If you did not create this account or have concerns about its security:
    </p>
    
    <ul style="margin-left: 20px; font-size: 14px; color: #4a5568;">
        <li>Do not click on verification links</li>
        <li>Contact our support team immediately</li>
        <li>Ignore this email</li>
    </ul>
    
    <div class="warning-box">
        <strong>Important:</strong> Never share this link with anyone. 
        {{ config('app.name') }} will never ask for your personal information via email.
    </div>
@endsection

