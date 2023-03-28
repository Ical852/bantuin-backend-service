<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Mail\AuthMail;
use App\Mail\ResetMail;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserEmailToken;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['email', 'required'],
                'password' => ['required']
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }

            $user = User::where('email', $request->email)->with(['helper', 'user_device'])->first();
            if (!Hash::check($request->password, $user->password)) {
                throw new \Exception('Invalid Credentials');
            }

            if (!$user->email_verified_at) {
                return ResponseFormatter::error([
                    'message' => 'Something went wrong',
                    'error' => 'Email has not been verified'
                ], 'Authentication Failed', 500);
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => ['required', 'string', 'max:255', 'min:3'],
                'username' => ['required', 'string', 'max:255', 'min:3'],
                'phone_number' => ['required', 'string', 'max:255', 'min:10'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' =>  ['required', 'min:8']
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $checkuser = User::where('email', $request->email)->whereNotNull('email_verified_at')->first();
            if ($checkuser) {
                return ResponseFormatter::error([
                    'message' => 'Something Went Wrong',
                    'error' => 'Email is Already Taken!'
                ], 'Sign Up Failed', 500);
            }

            $user = User::create([
                'full_name' => $request->full_name,
                'username' => $request->username,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image' => 'assets/user/dummyusernull.png',
                'role' => 'user',
                'balance' => 0
            ]);

            $user_email_token = [
                'user_id' => $user->id,
                'email' => $user->email,
                'token_type' => 'emailverif',
                'token' => base64_encode(random_bytes(64)),
            ];

            $createdToken = UserEmailToken::create($user_email_token);

            Mail::to($user->email)->send(new AuthMail($createdToken));

            return ResponseFormatter::success([
                'user' => $user,
            ], 'Register Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Register Failed', 500);
        }
    }

    public function update(Request $request)
    {
        $data = $request->all();

        $user = User::firstWhere('id', Auth::user()->id);
        $user->update($data);

        $user = User::where('id', Auth::user()->id)->with(['helper', 'user_device'])->first();

        return ResponseFormatter::success($user, 'Profile Updated');
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => ['required'],
                'new_password' => ['required', 'min:8'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            if (!Hash::check($request->password, Auth::user()->password)) {
                return ResponseFormatter::error([
                    'message' => 'Something Went Wrong',
                    'error' => 'Wrong Current Password'
                ], 'Change Password Failed', 500);
            }

            $new_password = Hash::make($request->new_password);
            $user = User::firstWhere('id', Auth::user()->id);
            $user->update(['password' => $new_password]);

            return ResponseFormatter::success([
                'user' => $user,
            ], 'Change Password Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Change Password Failed', 500);
        }
    }

    public function changeAvatar(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                 'file' => ['required', 'image', 'max:2048']
             ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(
                    ['error' => $validator->errors()],
                    'Update Photo Failed',
                    500
                );
            }

            $currentImage = Auth::user()->image;
            $splited = explode('/storage/', $currentImage);
            if ($splited[1] != 'assets/user/dummyusernull.png') {
                unlink(public_path('storage/'.$splited[1]));
            }

            if ($request->file('file')) {
                $file = $request->file->store('assets/user', 'public');

                $user =  User::firstWhere('id', Auth::user()->id);
                $user->image = $file;
                $user->update();

                $user = User::where('id', Auth::user()->id)->with(['helper', 'user_device'])->first();

                return ResponseFormatter::success([
                    'user' => $user
                ], 'Change Avatar Success');
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Change Avatar Failed', 500);
        }
    }

    public function fetch(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->with(['helper', 'user_device'])->first();
        return ResponseFormatter::success($user, 'Success Get User Data');
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'Something Went Wrong',
                    'error' => 'User Not Found'
                ], 'Request Reset Password Failed', 500);
            }

            if (!$user->email_verified_at) {
                return ResponseFormatter::error([
                    'message' => 'Something went wrong',
                    'error' => 'Email has not been verified'
                ], 'Authentication Failed', 500);
            }

            $user_email_token = [
                'user_id' => $user->id,
                'email' => $user->email,
                'token_type' => 'resetpw',
                'token' => base64_encode(random_bytes(64)),
            ];

            $createdToken = UserEmailToken::create($user_email_token);

            Mail::to($user->email)->send(new ResetMail($createdToken));

            return ResponseFormatter::success([
                'user' => $user,
            ], 'Request Reset Password Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Request Reset Password Failed', 500);
        }
    }

    public function storeUserDeviceId(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => ['required'],
                'device_id' => ['required'],
                'email' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $user = User::where('id', $request->user_id)->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'User Not Found',
                ], 'Store User Device Id Failed', 500);
            }

            $userDevices = UserDevice::where('user_id', $request->user_id)->first();

            if ($userDevices && $user->email_verified_at != null) {
                return ResponseFormatter::error([
                    'message' => 'Device Id Already Exist on This User',
                ], 'Store User Device Id Failed', 500);
            }

            UserDevice::where('user_id', $request->user_id)->delete();

            UserDevice::create([
                'user_id' => $request->user_id,
                'device_id' => $request->device_id,
                'email' => $request->email,
            ]);

            $user = User::where('id', $request->user_id)->with(['helper', 'user_device'])->first();

            return ResponseFormatter::success([
                'user' => $user,
            ], 'Store User Device Id Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Store User Device Id Failed', 500);
        }
    }

    public function updateUserDeviceId(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => ['required'],
                'device_id' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $user = User::where('id', $request->user_id)->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'User Not Found',
                ], 'Update User Device Id Failed', 500);
            }

            $userDevices = UserDevice::where('user_id', $request->user_id)->first();

            if (!$userDevices) {
                return ResponseFormatter::error([
                    'message' => 'User Doesn`t Have Any Device Yet',
                ], 'Update User Device Id Failed', 500);
            }

            $userDevices->update([
                'device_id' => $request->device_id
            ]);

            $user = User::where('id', $request->user_id)->with(['helper', 'user_device'])->first();

            return ResponseFormatter::success([
                'user' => $user,
            ], 'Update User Device Id Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Update User Device Id Failed', 500);
        }
    }

    public function fetchUserDeviceId(Request $request)
    {
        try {
            $user_id = $request->user_id;

            $user = User::where('id', $user_id)->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'User Not Found',
                ], 'Fetch User Device Data Failed', 500);
            }

            $userDevices = UserDevice::where('user_id', $user_id)->first();

            if (!$userDevices) {
                return ResponseFormatter::error([
                    'message' => 'User Device Not Found',
                    'device' => null
                ], 'Fetch User Device Data Failed', 500);
            }

            $user = User::where('id', $user_id)->with(['helper', 'user_device'])->first();

            return ResponseFormatter::success([
                'user' => $user,
            ], 'Fetch User Device Id Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'User Not Found',
                'error' => $error
            ], 'Fetch User Device Data Failed', 500);
        }
    }
}
