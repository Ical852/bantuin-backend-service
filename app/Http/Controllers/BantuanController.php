<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BantuanController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => ['required', 'image', 'max:2048'],
                'title' => ['required'],
                'price' => ['required'],
                'desc' => ['required'],
                'location' => ['required'],
                'pay_type' => ['required'],
                'category_id' => ['required'],
                'status' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            return ResponseFormatter::success([
            ], 'Change Password Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Change Password Failed', 500);
        }
    }
}
