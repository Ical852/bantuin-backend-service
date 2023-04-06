<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Notification;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => ['required'],
                'title' => ['required'],
                'message' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $user = User::where('id', $request->user_id)->with(['user_device'])->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'User Not Found',
                ], 'Create Notification Failed', 500);
            }

            $pushToOwner = [
                'title' => $request->title,
                'body' => $request->message,
                'icon' => '',
                'url' => 'url',
                'device' => $user->user_device->device_id,
                'chat' => 'no',
            ];

            $push = new PushNotificationController();
            $push->sendVerifiedNotif($pushToOwner);

            $notification = Notification::create([
                'user_id' => $request->user_id,
                'title' => $request->title,
                'message' => $request->message,
            ]);

            return ResponseFormatter::success([
                'notification' => $notification
            ], 'Create Notification Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Create Notification Failed', 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'notif_id' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $notification = Notification::where('id', $request->notif_id)->first();

            if (!$notification) {
                return ResponseFormatter::error([
                    'message' => 'Notification Not Found',
                ], 'Delete Notification Failed', 500);
            }

            $notification->delete();

            return ResponseFormatter::success([
                'notification' => $notification
            ], 'Delete Notification Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Delete Notification Failed', 500);
        }
    }

    public function get(Request $request)
    {
        try {
            $id = $request->id;

            if ($id) {
                $notification = Notification::where('id', $id)->first();

                if (!$notification) {
                    return ResponseFormatter::error([
                        'message' => 'Notification Not Found',
                    ], 'Get Notification Failed', 500);
                }

                return ResponseFormatter::success([
                    'notification' => $notification
                ], 'Get Notification Success');
            }

            $notification = Notification::where('user_id', Auth::user()->id)->get();

            return ResponseFormatter::success([
                'notifications' => $notification
            ], 'Get Notification Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Get Notification Failed', 500);
        }
    }

    public function push(Request $request)
    {
        $pushData = [
            'title' => $request->title,
            'body' => $request->body,
            'icon' => '',
            'url' => 'url',
            'device' => $request->device_id,
            'chat' => 'yes',
            'userid' => $request->user_id
        ];
        $push = new PushNotificationController();
        $push->sendVerifiedNotif($pushData);

        return true;
    }
}
