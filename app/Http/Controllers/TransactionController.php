<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Bantuan;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => ['required'],
                'gross_amount' => ['required'],
                'transaction_type' => ['required'],
                'payment_method' => ['required'],
                'bantuan_id' => ['required'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(null, $validator->errors());
            }

            $user = User::where('id', Auth::user()->id)->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'message' => 'User Not Found',
                ], 'Transaction Failed', 500);
            }

            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
            Config::$isProduction = false;
            Config::$isSanitized = false;
            Config::$is3ds = false;

            $params = [
                'transaction_details' => [
                    'order_id' => $request->order_id,
                    'gross_amount' => $request->gross_amount,
                ],
                'customer_details' => [
                    'first_name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone_number,
                ]
            ];

            if ($request->transaction_type == 'create_bantuan') {
                $bantuan = Bantuan::where('id', $request->bantuan_id)->first();
    
                if (!$bantuan) {
                    return ResponseFormatter::error([
                        'message' => 'Bantuan Not Found',
                    ], 'Transaction Failed', 500);
                }

                if ($request->payment_method == 'cash') {
                    $transaction = Transaction::create([
                        'order_id' => $request->order_id,
                        'gross_amount' => $request->gross_amount,
                        'payment_url' => 'cash',
                        'transaction_type' => $request->transaction_type,
                        'payment_method' => $request->payment_method,
                        'user_id' => Auth::user()->id,
                        'bantuan_id' => $request->bantuan_id,
                        'status' => 'success',
                    ]);

                    return ResponseFormatter::success([
                        'transaction' => $transaction
                    ], 'Transaction Success');
                }

                if ($request->payment_method == 'bmoney') {
                    if ($request->gross_amount > $user->balance) {
                        return ResponseFormatter::error([
                            'message' => 'Not Enough Bantuan Money',
                        ], 'Transaction Failed', 500);
                    }

                    $user->balance = $user->balance - $request->gross_amount;
                    $user->update();

                    $transaction = Transaction::create([
                        'order_id' => $request->order_id,
                        'gross_amount' => $request->gross_amount,
                        'payment_url' => 'bmoney',
                        'transaction_type' => $request->transaction_type,
                        'payment_method' => $request->payment_method,
                        'user_id' => Auth::user()->id,
                        'bantuan_id' => $request->bantuan_id,
                        'status' => 'success',
                    ]);

                    return ResponseFormatter::success([
                        'transaction' => $transaction
                    ], 'Transaction Success');
                }

                if ($request->payment_method == 'midtrans') {
                    $snap = Snap::createTransaction($params)->redirect_url;

                    $transaction = Transaction::create([
                        'order_id' => $request->order_id,
                        'gross_amount' => $request->gross_amount,
                        'payment_url' => $snap,
                        'transaction_type' => $request->transaction_type,
                        'payment_method' => $request->payment_method,
                        'user_id' => Auth::user()->id,
                        'bantuan_id' => $request->bantuan_id,
                        'status' => 'success',
                    ]);

                    return ResponseFormatter::success([
                        'transaction' => $transaction
                    ], 'Transaction Success');
                }
            }

            if ($request->transaction_type == 'topup') {
                if ($request->payment_method != 'midtrans') {
                    return ResponseFormatter::error([
                        'message' => 'Unkown Payment Method ',
                    ], 'Transaction Failed', 500);
                }

                $snap = Snap::createTransaction($params)->redirect_url;

                $user->balance = $user->balance + $request->gross_amount;
                $user->update();

                $transaction = Transaction::create([
                    'order_id' => $request->order_id,
                    'gross_amount' => $request->gross_amount,
                    'payment_url' => $snap,
                    'transaction_type' => $request->transaction_type,
                    'payment_method' => $request->payment_method,
                    'user_id' => Auth::user()->id,
                    'bantuan_id' => null,
                    'status' => 'success',
                ]);

                return ResponseFormatter::success([
                    'transaction' => $transaction
                ], 'Transaction Success');
            }

            return ResponseFormatter::error([
                'message' => 'Unknown Transaction Type',
            ], 'Transaction Failed', 500);
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Transaction Failed', 500);
        }
    }

    public function get(Request $request)
    {
        try {
            $id = $request->id;

            if ($id) {
                $transaction = Transaction::where('id', $id)->first();

                return ResponseFormatter::success([
                    'transaction' => $transaction
                ], 'Transaction Success');
            }

            $transactions = Transaction::where('user_id', Auth::user()->id)->get();

            return ResponseFormatter::success([
                'transactions' => $transactions
            ], 'Transaction Success');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Get Transaction Failed', 500);
        }
    }
}
