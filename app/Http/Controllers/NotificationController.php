<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use OneSignal;
use Validator;
use Auth;
class NotificationController extends Controller
{
    public function sendPush()
    {
        // $fields['include_plater_ids'] = ['e97654fc-5e7a-11ec-a316-fea818f90762'];
        // $fields['included_segments'] = ['Subscribed Users'];
        $fields['filters'] = array(array("field" => "tag", "key" => "class_3", "relation"=> "=", "value"=> "class_3"));
        $message = 'This is simple message.';

        $response = OneSignal::sendPush($fields, $message);

        dd($response);
    }

    public function getNotification(){
        dd(OneSignal::getNotifications());
    }

    public function getNotifications($id = NULL){
        dd(OneSignal::getNotification('dff4ef8c-f9be-4bcb-9da4-5304e69143c4'));
    }
}
