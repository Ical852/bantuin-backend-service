<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function push($title, $body, $icon, $url, $to)
    {
        $postdata = json_encode(
            [
                'notification' =>
                    [
                        'title' => $title,
                        'body' => $body,
                        'icon' => $icon,
                        'click_action' => $url
                    ]
                ,
                'to' => $to
            ]
        );

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/json'."\r\n"
                            .'Authorization: key='.env('FIREBASE_SERVER_KEY')."\r\n",
                'content' => $postdata
            )
        );

        $context  = stream_context_create($opts);

        $result = file_get_contents('https://fcm.googleapis.com/fcm/send', false, $context);
        if ($result) {
            return json_decode($result);
        } else {
            return false;
        }
    }

    public function sendVerifiedNotif($pushData)
    {
        $this->push(
            $pushData['title'], 
            $pushData['body'], 
            $pushData['icon'], 
            $pushData['url'], 
            $pushData['device']
        );
    }
}
