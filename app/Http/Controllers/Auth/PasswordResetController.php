<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user is OAuth-only (without password)
        if ($user->isOAuthUser() && !$user->password) {
            return back()->withErrors([
                'email' => 'This account uses ' . $user->getOAuthProviderName() . ' login. Please use the "Continue with ' . $user->getOAuthProviderName() . '" button on the login page.',
            ])->onlyInput('email');
        }

        // Generate reset token
        $token = Str::random(64);

        // Delete old tokens for this email
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Insert new token
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Send email
        Mail::to($user->email)->send(new PasswordResetMail($user, $token));

        return back()->with('success', __('messages.password_reset_link_sent'));
    }

    /**
     * Show the reset password form
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Reset password
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Check if token exists and is valid (not older than 1 hour)
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('created_at', '>', now()->subHour())
            ->first();

        if (!$resetRecord) {
            return back()->withErrors([
                'email' => __('messages.password_reset_token_invalid'),
            ])->onlyInput('email');
        }

        // Verify token
        if (!Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors([
                'email' => __('messages.password_reset_token_invalid'),
            ])->onlyInput('email');
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete used token
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Log the user in
        auth()->login($user);

        return redirect()->route('dashboard')->with('success', __('messages.password_reset_success'));
    }
}

