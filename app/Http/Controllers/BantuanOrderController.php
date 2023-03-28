<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Bantuan;
use App\Models\BantuanOrder;
use App\Models\Helper;
use App\Models\Notification;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            Notification::create([
                'user_id' => $owner->id,
                'title' =>'Ada yang ingin membantu kamu',
                'message' => $requester->full_name . ' ingin membantu kamu untuk ' . $bantuan->title,
            ]);

            Notification::create([
                'user_id' => $requester->id,
                'title' =>'Request bantuan kamu berhasil terkirim',
                'message' => 'Request bantuan kamu untuk ' . $owner->full_name . ' berhasil terkirim',
            ]);

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

    public function accept(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $order = BantuanOrder::where('id', $request->order_id)->first();

            if (!$order) {
                return ResponseFormatter::error([
                    'message' => 'Order Not Found',
                ], 'Accpet Order Bantuan Failed', 500);
            }

            if ($order->user_id != Auth::user()->id) {
                return ResponseFormatter::error([
                    'message' => 'You are not the bantuan owner',
                ], 'Accpet Order Bantuan Failed', 500);
            }

            if ($order->status == 'process') {
                return ResponseFormatter::error([
                    'message' => 'The Order is Already on Process',
                ], 'Accpet Order Bantuan Failed', 500);
            }

            if ($order->status == 'denied') {
                return ResponseFormatter::error([
                    'message' => 'The Order is Already Denied',
                ], 'Accpet Order Bantuan Failed', 500);
            }

            if ($order->status == 'cancelled') {
                return ResponseFormatter::error([
                    'message' => 'The Order is Already Cancelled',
                ], 'Accpet Order Bantuan Failed', 500);
            }

            if ($order->status == 'done') {
                return ResponseFormatter::error([
                    'message' => 'The Order is Already Done',
                ], 'Accpet Order Bantuan Failed', 500);
            }

            $helper = Helper::where('id', $order->helper_id)->first();
            $requester = User::where('id', $helper->user_id)->with(['user_device'])->first();

            $owner = User::where('id', $order->user_id)->first();

            $bantuan = Bantuan::where('id', $order->bantuan_id)->first();

            if ($bantuan->status == 'process') {
                return ResponseFormatter::error([
                    'message' => 'Bantuan Status is Already on Process',
                ], 'Accpet Order Bantuan Failed', 500);
            }

            if ($bantuan->status == 'done') {
                return ResponseFormatter::error([
                    'message' => 'Bantuan Status is Already Done',
                ], 'Accpet Order Bantuan Failed', 500);
            }

            Bantuan::where('id', $order->bantuan_id)->update([
                'status' => 'process'
            ]);
            $order->status = 'process';
            $order->update();

            $pushToRequester = [
                'title' => 'Request Bantuan Kamu Diterima',
                'body' => 'Request bantuan kamu kepada ' . $owner->full_name . ' untuk ' . $bantuan->title . ' telah diterima',
                'icon' => '',
                'url' => 'url',
                'device' => $requester->user_device->device_id
            ];

            $push = new PushNotificationController();
            $push->sendVerifiedNotif($pushToRequester);

            Notification::create([
                'user_id' => $requester->id,
                'title' =>'Request Bantuan Kamu Diterima',
                'message' => 'Request bantuan kamu kepada ' . $owner->full_name . ' untuk ' . $bantuan->title . ' telah diterima',
            ]);

            return ResponseFormatter::success([
                'order' => $order
            ], 'Accpet Order Bantuan Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Accpet Order Bantuan Failed', 500);
        }
    }

    public function deny(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $order = BantuanOrder::where('id', $request->order_id)->first();

            if (!$order) {
                return ResponseFormatter::error([
                    'message' => 'Order Not Found',
                ], 'Deny Order Bantuan Failed', 500);
            }

            if ($order->user_id != Auth::user()->id) {
                return ResponseFormatter::error([
                    'message' => 'You are not the bantuan owner',
                ], 'Deny Order Bantuan Failed', 500);
            }

            if ($order->status == 'process') {
                return ResponseFormatter::error([
                    'message' => 'The Order is Already on Process',
                ], 'Deny Order Bantuan Failed', 500);
            }

            if ($order->status == 'denied') {
                return ResponseFormatter::error([
                    'message' => 'The Order is Already Denied',
                ], 'Deny Order Bantuan Failed', 500);
            }

            if ($order->status == 'cancelled') {
                return ResponseFormatter::error([
                    'message' => 'The Order is Already Cancelled',
                ], 'Deny Order Bantuan Failed', 500);
            }

            if ($order->status == 'done') {
                return ResponseFormatter::error([
                    'message' => 'The Order is Already Done',
                ], 'Deny Order Bantuan Failed', 500);
            }

            $helper = Helper::where('id', $order->helper_id)->first();
            $requester = User::where('id', $helper->user_id)->with(['user_device'])->first();

            $owner = User::where('id', $order->user_id)->first();

            $bantuan = Bantuan::where('id', $order->bantuan_id)->first();

            if ($bantuan->status == 'process') {
                return ResponseFormatter::error([
                    'message' => 'Bantuan Status is Already on Process',
                ], 'Deny Order Bantuan Failed', 500);
            }

            if ($bantuan->status == 'done') {
                return ResponseFormatter::error([
                    'message' => 'Bantuan Status is Already Done',
                ], 'Deny Order Bantuan Failed', 500);
            }

            Bantuan::where('id', $order->bantuan_id)->update([
                'status' => 'active'
            ]);
            $order->status = 'denied';
            $order->update();

            $pushToRequester = [
                'title' => 'Yahhh!, Request Bantuan Kamu Ditolak',
                'body' => 'Mohon Maaf, Request bantuan kamu kepada ' . $owner->full_name . ' untuk ' . $bantuan->title . ' telah ditolak',
                'icon' => '',
                'url' => 'url',
                'device' => $requester->user_device->device_id
            ];

            $push = new PushNotificationController();
            $push->sendVerifiedNotif($pushToRequester);

            Notification::create([
                'user_id' => $requester->id,
                'title' =>'Yahhh!, Request Bantuan Kamu Ditolak',
                'message' => 'Mohon Maaf, Request bantuan kamu kepada ' . $owner->full_name . ' untuk ' . $bantuan->title . ' telah ditolak',
            ]);

            return ResponseFormatter::success([
                'order' => $order
            ], 'Deny Order Bantuan Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Deny Order Bantuan Failed', 500);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => ['required'],
                'reason' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $order = BantuanOrder::where('id', $request->order_id)->first();

            if (!$order) {
                return ResponseFormatter::error([
                    'message' => 'Order Not Found',
                ], 'Cancel Order Bantuan Failed', 500);
            }

            if ($order->user_id != Auth::user()->id) {
                return ResponseFormatter::error([
                    'message' => 'You are not the bantuan owner',
                ], 'Cancel Order Bantuan Failed', 500);
            }

            if ($order->status != 'process') {
                return ResponseFormatter::error([
                    'message' => 'The Order is not on Process',
                ], 'Cancel Order Bantuan Failed', 500);
            }

            $helper = Helper::where('id', $order->helper_id)->first();
            $requester = User::where('id', $helper->user_id)->with(['user_device'])->first();

            $owner = User::where('id', $order->user_id)->with(['user_device'])->first();

            $bantuan = Bantuan::where('id', $order->bantuan_id)->first();

            if ($bantuan->status != 'process') {
                return ResponseFormatter::error([
                    'message' => 'Bantuan Status is not on Process',
                ], 'Cancel Order Bantuan Failed', 500);
            }

            Bantuan::where('id', $order->bantuan_id)->update([
                'status' => 'active'
            ]);
            $order->status = 'cancelled';
            $order->reason = $request->reason;
            $order->update();

            $pushToRequester = [
                'title' => 'Yahhh!, Bantuan Kamu Ddibatalkan',
                'body' => 'Mohon Maaf, Request bantuan kamu telah dibatalkan oleh ' . $owner->full_name . ' karena ' . $request->reason,
                'icon' => '',
                'url' => 'url',
                'device' => $requester->user_device->device_id
            ];

            $pushToOwner = [
                'title' => 'Permintann Bantuan Telah Berhasil Dibatalakan',
                'body' => 'Bantuan dari ' . $requester->full_name . ' untuk ' . $bantuan->title . ' telah dibatalkan karena ' . $request->reason,
                'icon' => '',
                'url' => 'url',
                'device' => $owner->user_device->device_id
            ];

            $push = new PushNotificationController();
            $push->sendVerifiedNotif($pushToRequester);
            $push->sendVerifiedNotif($pushToOwner);

            Notification::create([
                'user_id' => $requester->id,
                'title' =>'Yahhh!, Bantuan Kamu Ddibatalkan',
                'message' => 'Mohon Maaf, Request bantuan kamu telah dibatalkan oleh ' . $owner->full_name . ' karena ' . $request->reason,
            ]);

            Notification::create([
                'user_id' => $owner->id,
                'title' =>'Permintann Bantuan Telah Berhasil Dibatalakan',
                'message' => 'Bantuan dari ' . $requester->full_name . ' untuk ' . $bantuan->title . ' telah dibatalkan karena ' . $request->reason,
            ]);

            return ResponseFormatter::success([
                'order' => $order
            ], 'Cancel Order Bantuan Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Cancel Order Bantuan Failed', 500);
        }
    }

    public function done(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $order = BantuanOrder::where('id', $request->order_id)->first();

            if (!$order) {
                return ResponseFormatter::error([
                    'message' => 'Order Not Found',
                ], 'Done Order Bantuan Failed', 500);
            }

            if ($order->user_id != Auth::user()->id) {
                return ResponseFormatter::error([
                    'message' => 'You are not the bantuan owner',
                ], 'Done Order Bantuan Failed', 500);
            }

            if ($order->status != 'process') {
                return ResponseFormatter::error([
                    'message' => 'The Order is not on Process',
                ], 'Done Order Bantuan Failed', 500);
            }

            $helper = Helper::where('id', $order->helper_id)->first();
            $requester = User::where('id', $helper->user_id)->with(['user_device'])->first();

            $owner = User::where('id', $order->user_id)->with(['user_device'])->first();

            $bantuan = Bantuan::where('id', $order->bantuan_id)->first();

            if ($bantuan->status != 'process') {
                return ResponseFormatter::error([
                    'message' => 'Bantuan Status is not on Process',
                ], 'Done Order Bantuan Failed', 500);
            }

            Bantuan::where('id', $order->bantuan_id)->update([
                'status' => 'done'
            ]);
            $order->status = 'done';
            $order->reason = null;
            $order->update();

            if ($bantuan->pay_type != 'cash') {
                $helper->helper_balance = $helper->helper_balance + $bantuan->price;
                $helper->update();
            }

            $pushToRequester = [
                'title' => 'Yeayy!, Bantuan Kamu Telah Selesai',
                'body' => 'Selamat, Bantuan Kamu ke ' . $owner->full_name . ' untuk ' . $bantuan->title . ' telah selesai, silakan terima uang kamu',
                'icon' => '',
                'url' => 'url',
                'device' => $requester->user_device->device_id
            ];

            $pushToOwner = [
                'title' => 'Horeee!, Bantuan Kamu Telah Selesai Dibantu',
                'body' => 'Horeee, selamat, bantuan kamu telah selesai dibantu oleh ' . $requester->full_name . ', Selesaikan pembayaran kamu jika belum selesai',
                'icon' => '',
                'url' => 'url',
                'device' => $owner->user_device->device_id
            ];

            $push = new PushNotificationController();
            $push->sendVerifiedNotif($pushToRequester);
            $push->sendVerifiedNotif($pushToOwner);

            Notification::create([
                'user_id' => $requester->id,
                'title' =>'Permintaan Kamu Diterima!',
                'message' => 'Yeay, Admin telah menerima permintann kamu untuk menjadi helper'
            ]);

            Notification::create([
                'user_id' => $owner->id,
                'title' =>'Horeee!, Bantuan Kamu Telah Selesai Dibantu',
                'message' => 'Horeee, selamat, bantuan kamu telah selesai dibantu oleh ' . $requester->full_name . ', Selesaikan pembayaran kamu jika belum selesai',
            ]);

            return ResponseFormatter::success([
                'order' => $order
            ], 'Done Order Bantuan Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Done Order Bantuan Failed', 500);
        }
    }

    public function get(Request $request)
    {
        try {
            $helper_id = $request->helper_id;
            $user_id = $request->user_id;
            $bantuan_id = $request->bantuan_id;
            $status = $request->status;

            if ($helper_id) {

                $order = BantuanOrder::query()->with(['helper.user.user_device', 'bantuan.bantuan_category', 'bantuan.user.user_device']);
                
                if ($status) {
                    $order->where('status', $status);
                }

                $order->where('helper_id', $helper_id);

                if ($order) {
                    return ResponseFormatter::success($order->get(), "Success Get Bantuan Order Data");
                } else {
                    return ResponseFormatter::error(null, "Bantuan Order Data Not Found", 404);
                }
            }

            if ($user_id) {

                $order = BantuanOrder::query()->with(['helper.user.user_device', 'bantuan.bantuan_category', 'bantuan.user.user_device']);
                
                if ($status) {
                    $order->where('status', $status);
                }

                $order->where('user_id', $user_id);

                if ($order) {
                    return ResponseFormatter::success($order->get(), "Success Get Bantuan Order Data");
                } else {
                    return ResponseFormatter::error(null, "Bantuan Order Data Not Found", 404);
                }
            }

            if ($bantuan_id) {

                $order = BantuanOrder::query()->with(['helper.user.user_device', 'bantuan.bantuan_category', 'bantuan.user.user_device']);
                
                if ($status) {
                    $order->where('status', $status);
                }

                $order->where('bantuan_id', $bantuan_id);

                if ($order) {
                    return ResponseFormatter::success($order->get(), "Success Get Bantuan Order Data");
                } else {
                    return ResponseFormatter::error(null, "Bantuan Order Data Not Found", 404);
                }
            }

            $order = BantuanOrder::query()->with(['helper.user.user_device', 'bantuan.bantuan_category', 'bantuan.user.user_device']);

            if ($status) {
                $order->where('status', $status);
            }
            
            return ResponseFormatter::success($order->get(), 'Success Get Bantuan Order Data');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed Get Bantuan Order Data', 500);
        }
    }
}
