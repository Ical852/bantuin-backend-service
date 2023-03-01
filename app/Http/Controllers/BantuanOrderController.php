<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Bantuan;
use App\Models\BantuanOrder;
use App\Models\Helper;
use App\Models\User;
use App\Models\UserDevice;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BantuanOrderController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => ['required'],
                'bantuan_id' => ['required'],
                'helper_id' => ['required'],
                'status' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $user = User::where('id', $request->user_id)->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'User Not Found',
                ], 'Order Bantuan Failed', 500);
            }

            $bantuan = Bantuan::where('id', $request->bantuan_id)->first();

            if (!$bantuan) {
                return ResponseFormatter::error([
                    'message' => 'Bantuan Not Found',
                ], 'Order Bantuan Failed', 500);
            }

            if ($bantuan->user_id != $user->id) {
                return ResponseFormatter::error([
                    'message' => 'User not the owner of the bantuan',
                ], 'Order Bantuan Failed', 500);
            }

            $helper = Helper::where('id', $request->helper_id)->first();

            if (!$helper) {
                return ResponseFormatter::error([
                    'message' => 'Helper Not Found',
                ], 'Order Bantuan Failed', 500);
            }

            if ($helper->status != 'active') {
                return ResponseFormatter::error([
                    'message' => 'Helper Status Not Activated Yet',
                ], 'Order Bantuan Failed', 500);
            }

            if ($bantuan->user_id == $helper->user_id) {
                return ResponseFormatter::error([
                    'message' => 'This is your Bantuan',
                ], 'Order Bantuan Failed', 500);
            }

            $order = BantuanOrder::where('user_id', $user->id)
                ->where('bantuan_id', $bantuan->id)
                ->where('helper_id', $helper->id)->first();
            
            if ($order) {
                return ResponseFormatter::error([
                    'message' => 'You Already Request to Order this Bantuan',
                ], 'Order Bantuan Failed', 500);
            }

            $order = BantuanOrder::create([
                'user_id' => $request->user_id,
                'bantuan_id' => $request->bantuan_id,
                'helper_id' => $request->helper_id,
                'status' => 'pending',
            ]);

            $owner = User::where('id', $user->id)->with(['user_device'])->first();
            $requester = User::where('id', $helper->user_id)->with(['user_device'])->first();

            $pushToOwner = [
                'title' => 'Ada yang ingin membantu kamu',
                'body' => $requester->full_name . ' ingin membantu kamu untuk ' . $bantuan->title,
                'icon' => '',
                'url' => 'url',
                'device' => $owner->user_device->device_id
            ];

            $pushToRequester = [
                'title' => 'Request bantuan kamu berhasil terkirim',
                'body' => 'Request bantuan kamu untuk ' . $owner->full_name . ' berhasil terkirim',
                'icon' => '',
                'url' => 'url',
                'device' => $requester->user_device->device_id
            ];

            $push = new PushNotificationController();

            $push->sendVerifiedNotif($pushToOwner);
            $push->sendVerifiedNotif($pushToRequester);

            return ResponseFormatter::success([
                'order' => $order
            ], 'Order Bantuan Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Order Bantuan Failed', 500);
        }
    }
}
