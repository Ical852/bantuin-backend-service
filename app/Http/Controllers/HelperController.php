<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Bantuan;
use App\Models\Helper;
use App\Models\HelperRating;
use App\Models\User;
use App\Models\UserDevice;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HelperController extends Controller
{
    public function cuanRequest()
    {
        try {
            $user = User::where('id', Auth::user()->id)->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'User Not Found',
                ], 'Request to Be Helper Failed', 500);
            }

            $helper = Helper::where('user_id', $user->id)->first();

            if ($helper) {
                return ResponseFormatter::error([
                    'message' => 'You Already Requested To Be Helper',
                ], 'Request to Be Helper Failed', 500);
            }

            Helper::create([
                'user_id' => Auth::user()->id,
                'helper_balance' => 0,
                'status' => 'pending'
            ]);

            $userDevices = UserDevice::where('user_id', $user->id)->first();
            $user = User::where('id', $user->id)->with(['helper', 'user_device'])->first();

            $pushData = [
                'title' => 'Permintaan Berhasil Terkirim',
                'body' => 'Permintaan kamu untuk menjadi helper telah berhasil terkirim',
                'icon' => '',
                'url' => 'url',
                'device' => $userDevices->device_id
            ];

            $push = new PushNotificationController();
            $push->sendVerifiedNotif($pushData);

            return ResponseFormatter::success([
                'user' => $user,
            ], 'Request to Be Helper Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Request to Be Helper Failed', 500);
        }
    }

    public function checkAvailibility()
    {
        try {
            $helper = Helper::where('user_id', Auth::user()->id)->first();

            if (!$helper) {
                return ResponseFormatter::error([
                    'message' => 'You Have Not Requested to Be Helper Yet',
                ], 'Check Helper Availibility Failed', 500);
            }

            if ($helper->status != 'active') {
                return ResponseFormatter::error([
                    'message' => 'You Are Not Verified Helper',
                ], 'Check Helper Availibility Failed', 500);
            }

            $user = User::where('id', Auth::user()->id)->with(['helper', 'user_device'])->first();

            return ResponseFormatter::success([
                'message' => 'You Are Verified Helper',
                'user' => $user,
            ], 'Check Helper Availibility Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Check Helper Availibility Failed', 500);
        }
    }

    public function fetch()
    {
        try {
            $helper = Helper::where('user_id', Auth::user()->id)->first();

            if (!$helper) {
                return ResponseFormatter::error([
                    'message' => 'No Helper Data',
                ], 'Fetch Helper Data Failed', 500);
            }

            $user = User::where('id', Auth::user()->id)->with(['helper.helper_rating', 'user_device'])->first();

            return ResponseFormatter::success([
                'user' => $user,
            ], 'Fetch Helper Data Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Fetch Helper Data Failed', 500);
        }
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $helper = Helper::where('user_id', Auth::user()->id)->first();

        $helper->update($data);

        $user = User::where('id', Auth::user()->id)->with(['helper', 'user_device'])->first();

        return ResponseFormatter::success([
            'user' => $user,
        ], 'Helper Data Updated');
    }

    public function rateHelper(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => ['required'],
                'bantuan_id' => ['required'],
                'helper_id' => ['required'],
                'rating' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $user = User::where('id', $request->user_id)->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'User Not Found',
                ], 'Give Rate To Helper Failed', 500);
            }

            $bantuan = Bantuan::where('id', $request->bantuan_id)->first();

            if (!$bantuan) {
                return ResponseFormatter::error([
                    'message' => 'Bantuan Not Found',
                ], 'Give Rate To Helper Failed', 500);
            }

            $helper = Helper::where('user_id', $request->helper_id)->first();

            if (!$helper) {
                return ResponseFormatter::error([
                    'message' => 'Helper Not Found',
                ], 'Give Rate To Helper Failed', 500);
            }

            if ($request->user_id == $helper->user_id) {
                return ResponseFormatter::error([
                    'message' => 'You Can`t Give Rate to Yourself',
                ], 'Give Rate To Helper Failed', 500);
            }

            $rate = HelperRating::create([
                'user_id' => $request->user_id,
                'bantuan_id' => $request->bantuan_id,
                'helper_id' => $request->helper_id,
                'rating' => $request->rating,
            ]);

            $rate = HelperRating::where('id', $rate->id)->with(['user', 'bantuan', 'helper.user.user_device'])->first();
            
            return ResponseFormatter::success([
                'rate' => $rate,
            ], 'Give Rate To Helper Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Give Rate To Helper Failed', 500);
        }
    }

    public function getAllRate()
    {
        try {
            $helper = Helper::where('user_id', Auth::user()->id)->first();

            if (!$helper) {
                return ResponseFormatter::error([
                    'message' => 'Helper Not Found',
                ], 'Get All Helper Rate Failed', 500);
            }

            $rate = HelperRating::where('helper_id', $helper->id)->with(['user', 'bantuan', 'helper.user.user_device'])->get();

            return ResponseFormatter::success([
                'rate' => $rate,
            ], 'Get All Helper Rate Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get All Helper Rate Failed', 500);
        }
    }
}
