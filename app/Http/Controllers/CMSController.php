<?php

namespace App\Http\Controllers;

use App\Mail\AcceptedMail;
use App\Mail\ActivateMail;
use App\Mail\DeniedMail;
use App\Mail\StoppedMail;
use App\Models\Helper;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CMSController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('secret');
        $this->middleware('verified');
    }

    public function index()
    {
        return view('cms.pages.index');
    }

    public function helper()
    {
        $data = Helper::with(['user.user_device'])->get();
        return view('cms.pages.helper.index', [
            'data' => $data
        ]);
    }

    public function accept(Request $request)
    {
        $email = $request->email;
        $user_id = $request->id;
        $device = $request->device;

        Helper::where('user_id', $user_id)->update(['status' => 'active']);

        $pushData = [
            'title' => 'Permintaan Kamu Diterima!',
            'body' => 'Yeay, Admin telah menerima permintann kamu untuk menjadi helper',
            'icon' => '',
            'url' => 'url',
            'device' => $device
        ];

        $user = User::where('id', $user_id)->first();

        Mail::to($email)->send(new AcceptedMail($user->full_name));

        $push = new PushNotificationController();
        $push->sendVerifiedNotif($pushData);

        Notification::create([
            'user_id' => $user_id,
            'title' =>'Permintaan Kamu Diterima!',
            'message' => 'Yeay, Admin telah menerima permintann kamu untuk menjadi helper'
        ]);

        return redirect('/cms/helper');
    }

    public function activate(Request $request)
    {
        $email = $request->email;
        $user_id = $request->id;
        $device = $request->device;

        Helper::where('user_id', $user_id)->update(['status' => 'active']);

        $pushData = [
            'title' => 'Kamu Di Aktivasi!',
            'body' => 'Wah, Admin mengaktivasi kamu untuk menjadi helper',
            'icon' => '',
            'url' => 'url',
            'device' => $device
        ];

        $push = new PushNotificationController();
        $push->sendVerifiedNotif($pushData);

        Notification::create([
            'user_id' => $user_id,
            'title' =>'Kamu Di Aktivasi!',
            'message' => 'Wah, Admin mengaktivasi kamu untuk menjadi helper'
        ]);

        $user = User::where('id', $user_id)->first();

        Mail::to($email)->send(new ActivateMail($user->full_name));

        return redirect('/cms/helper');
    }

    public function deny(Request $request)
    {
        $email = $request->email;
        $user_id = $request->id;
        $device = $request->device;

        Helper::where('user_id', $user_id)->update(['status' => 'denied']);

        $pushData = [
            'title' => 'Permintaan Kamu Ditolak!',
            'body' => 'Yah, Sepertinya kamu tidak memenuhi syarat untuk menjadi helper',
            'icon' => '',
            'url' => 'url',
            'device' => $device
        ];

        $push = new PushNotificationController();
        $push->sendVerifiedNotif($pushData);

        Notification::create([
            'user_id' => $user_id,
            'title' =>'Permintaan Kamu Ditolak!',
            'message' => 'Yah, Sepertinya kamu tidak memenuhi syarat untuk menjadi helper'
        ]);

        $user = User::where('id', $user_id)->first();

        Mail::to($email)->send(new DeniedMail($user->full_name));

        return redirect('/cms/helper');
    }

    public function stop(Request $request)
    {
        $email = $request->email;
        $user_id = $request->id;
        $device = $request->device;

        Helper::where('user_id', $user_id)->update(['status' => 'stopped']);

        $pushData = [
            'title' => 'Status Helper Kamu Dinonaktifkan Admin!',
            'body' => 'Hmm, Kami memutuskan untuk tidak melanjutkan kamu sebagai helper di aplikasi ini',
            'icon' => '',
            'url' => 'url',
            'device' => $device
        ];

        $push = new PushNotificationController();
        $push->sendVerifiedNotif($pushData);

        Notification::create([
            'user_id' => $user_id,
            'title' =>'Status Helper Kamu Dinonaktifkan Admin!',
            'message' => 'Hmm, Kami memutuskan untuk tidak melanjutkan kamu sebagai helper di aplikasi ini'
        ]);

        $user = User::where('id', $user_id)->first();

        Mail::to($email)->send(new StoppedMail($user->full_name));

        return redirect('/cms/helper');
    }

    public function bantuin()
    {
        return view('cms.pages.bantuin.index');
    }
}
