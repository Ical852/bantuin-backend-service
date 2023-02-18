<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserEmailToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserWebServerSideController extends Controller
{
    private function verifSuccess()
    {   
        return view('redirect.verification.verifsuccess');
    }

    private function verifFailed()
    {
        return view('redirect.verification.veriffailed');
    }

    private function resetSuccess()
    {
        return view('redirect.resetpassword.verifsuccess');
    }

    private function resetFailed()
    {
        return view('redirect.resetpassword.veriffailed');
    }

    public function error404()
    {
        return view('error.404');
    }

    public function verification(Request $request)
    {   
        $user_id = $request->query('user');
        $token_type = $request->query('ttype');
        $token = str_replace(' ', '+', $request->query('vcode'));

        $user_email_token = 
        UserEmailToken::where('user_id', $user_id)
            ->where('token_type', $token_type)
            ->where('token', $token)->first();

        if ($user_email_token) {
            $user = User::where('id', $user_id)->first();
            $user->update(['email_verified_at' => now()]);

            $user->where('email', $user->email)->whereNull('email_verified_at')->delete();

            UserEmailToken::where('user_id', $user_id)
            ->where('token_type', $token_type)->delete();

            return $this->verifSuccess();
        } else {
            return $this->verifFailed();
        }
    }

    public function submitNewPassword(Request $request)
    {
        $user_id = $request->user_id;
        $token_type = $request->token_type;
        $token = str_replace(' ', '+', $request->token);

        $user_email_token = 
        UserEmailToken::where('user_id', $user_id)
            ->where('token_type', $token_type)
            ->where('token', $token)->first();

        if (!$user_email_token) {
            return $this->resetFailed();
        }

        $validatedData = $request->validate([
            'password' => ['required', 'min:8', 'max:255']
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        User::where('id', $user_id)->update(['password' => $validatedData['password']]);

        $user_email_token->delete();

        return $this->resetSuccess();
    }

    private function resetNewPassword(Request $request)
    {
        $user_id = $request->query('user');
        $token_type = $request->query('ttype');
        $token = str_replace(' ', '+', $request->query('vcode'));

        $user_email_token = 
        UserEmailToken::where('user_id', $user_id)
            ->where('token_type', $token_type)
            ->where('token', $token)->first();

        if (!$user_email_token) {
            return $this->resetFailed();
        }

        return view('auth.reset', [
            'user_id' => $user_id,
            'token_type' => $token_type,
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $user_id = $request->query('user');
        $token_type = $request->query('ttype');
        $token = str_replace(' ', '+', $request->query('vcode'));

        $user_email_token = 
        UserEmailToken::where('user_id', $user_id)
            ->where('token_type', $token_type)
            ->where('token', $token)->first();

        if ($user_email_token) {
            return $this->resetNewPassword($request);
        } else {
            return $this->resetFailed();
        }
    }
}
