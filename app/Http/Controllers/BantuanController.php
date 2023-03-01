<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Bantuan;
use App\Models\BantuanCategory;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'user_id' => ['required'],
                'location' => ['required'],
                'pay_type' => ['required'],
                'category_id' => ['required'],
                'status' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $category = BantuanCategory::where('id', $request->category_id)->first();

            if (!$category) {
                return ResponseFormatter::error([
                    'message' => 'Category Not Found',
                ], 'Create Bantuan Failed', 500);
            }

            $user = User::where('id', $request->user_id)->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'User Not Found',
                ], 'Create Bantuan Failed', 500);
            }

            if ($request->file('image')) {
                $image = $request->image->store('assets/bantuan', 'public');

                $bantuan = Bantuan::create([
                    'image' => $image,
                    'title' => $request->title,
                    'price' => $request->price,
                    'desc' => $request->desc,
                    'user_id' => $request->user_id,
                    'location' => $request->location,
                    'pay_type' => $request->pay_type,
                    'category_id' => $request->category_id,
                    'status' => $request->status,
                ]);

                return ResponseFormatter::success([
                    'bantuan' => $bantuan
                ], 'Create Bantuan Success');
            }
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Create Bantuan Failed', 500);
        }
    }

    public function update(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'bantuan_id' => ['required'],
                'status' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $bantuan = Bantuan::where('id', $request->bantuan_id)->first();

            if (!$bantuan) {
                return ResponseFormatter::error([
                    'message' => 'Bantuan Not Found',
                ], 'Update Bantuan Failed', 500);
            }

            $bantuan->status = $request->status;
            $bantuan->update();

            return ResponseFormatter::success([
                'bantuan' => $bantuan
            ], 'Update Bantuan Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Update Bantuan Failed', 500);
        }
    }

    public function delete(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'bantuan_id' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $bantuan = Bantuan::where('id', $request->bantuan_id)->first();

            if (!$bantuan) {
                return ResponseFormatter::error([
                    'message' => 'Bantuan Not Found',
                ], 'Delete Bantuan Failed', 500);
            }

            $splited = explode('/storage/', $bantuan->image);
            unlink(public_path('storage/'.$splited[1]));

            $bantuan->delete();

            return ResponseFormatter::success([
                'bantuan' => $bantuan
            ], 'Delete Bantuan Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Delete Bantuan Failed', 500);
        }
    }

    public function get(Request $request) {

        try {
            $id = $request->id;
            $user_id = $request->user_id;
            $category_id = $request->category_id;
            $price = $request->price;
            $title = $request->title;
            $desc = $request->desc;
            $location = $request->location;
            $date = $request->date;

            if ($id) {
                $bantuan = Bantuan::find($id);

                if ($bantuan) {
                    return ResponseFormatter::success($bantuan, "Success Get Bantuan Data");
                } else {
                    return ResponseFormatter::error(null, "Bantuan Data Not Found", 404);
                }
            }

            if ($category_id) {
                $bantuan = Bantuan::where('category_id', $category_id)->get();

                if ($bantuan) {
                    return ResponseFormatter::success($bantuan, "Success Get Bantuan Data");
                } else {
                    return ResponseFormatter::error(null, "Bantuan Data Not Found", 404);
                }
            }

            if ($user_id) {
                $bantuan = Bantuan::where('user_id', $user_id)->get();

                if ($bantuan) {
                    return ResponseFormatter::success($bantuan, "Success Get Bantuan Data");
                } else {
                    return ResponseFormatter::error(null, "Bantuan Data Not Found", 404);
                }
            }

            $bantuan = Bantuan::query();

            $bantuan->where('user_id', '!=', Auth::user()->id);

            if ($price == 'low') {
                $bantuan->orderBy('price', 'ASC');
            }

            if ($price == 'high') {
                $bantuan->orderBy('price', 'DESC');
            }

            if ($date == 'new') {
                $bantuan->orderBy('created_at', 'DESC');
            }

            if ($date == 'old') {
                $bantuan->orderBy('created_at', 'ASC');
            }

            if ($title) {
                $bantuan->where('title', 'like', '%'.$title.'%');
            }

            if ($desc) {
                $bantuan->where('desc', 'like', '%'.$desc.'%');
            }

            if ($location) {
                $bantuan->where('location', 'like', '%'.$location.'%');
            }

            return ResponseFormatter::success(
                $bantuan->get(),
                'Success Get Bantuan Data'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed to Get Bantuan Data', 500);
        }
    }

    public function category(Request $request)
    {
        $id = $request->id;

        try {
            if ($id) {
                $category = BantuanCategory::find($id);
                
                if ($category) {
                    return ResponseFormatter::success($category, "Success Get Bantuan Category Data");
                } else {
                    return ResponseFormatter::error(null, "Bantuan Category Data Not Found", 404);
                }
            }

            $category = BantuanCategory::all();

            return ResponseFormatter::success(
                $category,
                'Success Get Bantuan Category Data'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed to Get Bantuan Category Data', 500);
        }
    }
}
