<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Chat;
use App\Models\Helper;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => ['required'],
                'helper_id' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $user = User::where('id', $request->user_id)->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'User Not Found',
                ], 'Create Chat Failed', 500);
            }

            $helper = Helper::where('id', $request->helper_id)->first();

            if (!$helper) {
                return ResponseFormatter::error([
                    'message' => 'Helper Not Found',
                ], 'Create Chat Failed', 500);
            }

            if ($user->id == $helper->user_id) {
                return ResponseFormatter::error([
                    'message' => 'This is a Same Person',
                ], 'Create Chat Failed', 500);
            }

            $chat = Chat::where('user_id', $user->id)->where('helper_id', $helper->id)->first();

            if (!$chat) {
                $chat = Chat::create([
                    'user_id' => $request->user_id,
                    'helper_id' => $request->helper_id,
                ]);
            }

            return ResponseFormatter::success([
                'chat' => $chat
            ], 'Create Chat Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Create Chat Failed', 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $chat = Chat::where('id', $request->chat_id)->first();

            if (!$chat) {
                return ResponseFormatter::error([
                    'message' => 'Chat Not Found',
                ], 'Delete Chat Failed', 500);
            }

            $chat->delete();

            return ResponseFormatter::success([
                'chat' => $chat
            ], 'Delete Chat Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Delete Chat Failed', 500);
        }
    }

    public function get(Request $request)
    {
        try {
            $helper_id = $request->helper_id;

            if ($helper_id) {
                $chats = Chat::where('helper_id', $helper_id)->get();

                return ResponseFormatter::success([
                    'chat' => $chats
                ], 'Get Chat Success');
            }

            $chats = Chat::where('user_id', Auth::user()->id)->get();

            return ResponseFormatter::success([
                'chat' => $chats
            ], 'Get Chat Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Get Chat Failed', 500);
        }
    }
}
