@extends('emails.layouts.app')

@section('content')

<table width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff; padding:40px 0; font-family: Arial, sans-serif;">
    <tr>
        <td align="center">

            <!-- Card -->
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="max-width:420px;
                          background:#ffffff;
                          border:1px solid #E6E9F0;
                          border-radius:12px;
                          box-shadow:0 2px 6px rgba(0,0,0,0.04);
                          padding:32px;">

                <tr>
                    <td align="center">

                        <!-- Title -->
                        <h1 style="font-size:24px;
                                   font-weight:600;
                                   color:#1A1919;
                                   margin:0 0 8px 0;">
                            Reset Password
                        </h1>

                        <p style="font-size:14px;
                                  color:#808080;
                                  margin:0 0 24px 0;">
                            Masukkan password baru untuk akun Anda
                        </p>

                        <!-- Greeting -->
                        <p style="font-size:14px;
                                  color:#1A1919;
                                  margin:0 0 16px 0;">
                            Halo {{ $user->name }},
                        </p>

                        <p style="font-size:14px;
                                  color:#808080;
                                  line-height:1.6;
                                  margin:0 0 24px 0;">
                            Kami menerima permintaan untuk mereset password akun {{ $appName }} Anda.
                            Klik tombol di bawah untuk melanjutkan.
                        </p>

                        <!-- Button -->
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center">
                                    <a href="{{ $resetUrl }}"
                                       style="display:inline-block;
                                              width:100%;
                                              background:#E9322B;
                                              color:#ffffff;
                                              text-decoration:none;
                                              padding:12px 0;
                                              border-radius:8px;
                                              font-weight:500;
                                              font-size:14px;">
                                        Reset Password
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- Expire Info -->
                        <p style="font-size:12px;
                                  color:#808080;
                                  margin:20px 0 0 0;">
                            Link ini akan kadaluarsa dalam 1 jam.
                        </p>

                        <hr style="border:none;
                                   border-top:1px solid #E6E9F0;
                                   margin:24px 0;">

                        <!-- Copy Link -->
                        <p style="font-size:12px;
                                  color:#808080;
                                  word-break:break-all;">
                            Jika tombol tidak berfungsi, salin dan tempel link berikut ke browser Anda:
                        </p>

                        <p style="font-size:12px;
                                  color:#E9322B;
                                  word-break:break-all;">
                            {{ $resetUrl }}
                        </p>

                        <hr style="border:none;
                                   border-top:1px solid #E6E9F0;
                                   margin:24px 0;">

                        <!-- Footer Text -->
                        <p style="font-size:12px;
                                  color:#808080;
                                  line-height:1.6;
                                  margin:0;">
                            Terima kasih,<br>
                            {{ $appName }} Team
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

@endsection