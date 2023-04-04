<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\SearchHistory;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HistorySearchController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'search_text' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $user = User::where('id', Auth::user()->id)->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'User Not Found',
                ], 'Create Search History Failed', 500);
            }

            $search = SearchHistory::create([
                'user_id' => $user->id,
                'search_text' => $request->search_text,
            ]);

            return ResponseFormatter::success([
                'search' => $search
            ], 'Create Search History Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Create Search History Failed', 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $user = User::where('id', Auth::user()->id)->first();
            
            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'User Not Found',
                ], 'Delete Search History Failed', 500);
            }

            $id = $request->id;

            if ($id) {
                $search = SearchHistory::where('id', $id)->first();

                if (!$search) {
                    return ResponseFormatter::error([
                        'message' => 'Search History Not Found',
                    ], 'Delete Single Search History Failed', 500);
                }

                $search->delete();

                return ResponseFormatter::success(null, 'Delete Single Search History Success');
            } else {
                SearchHistory::where('user_id', Auth::user()->id)->delete();
    
                return ResponseFormatter::success(null, 'Delete Search History Success');
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Delete Search History Failed', 500);
        }
    }

    public function get()
    {
        try {
            $user = User::where('id', Auth::user()->id)->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'User Not Found',
                ], 'Get Search History Failed', 500);
            }

            $search = SearchHistory::where('user_id', Auth::user()->id)->take(5)->get();

            return ResponseFormatter::success([
                'search' => $search
            ], 'Get Search History Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Get Search History Failed', 500);
        }
    }
}
