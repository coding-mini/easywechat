<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WechatController extends Controller
{
    public function serve()
    {
        Log::info('I am wechat server');

        $app = app('wechat.official_account');

        $app->server->push(function($message){
            return "欢迎关注 coding10！";
        });

        return $app->server->serve();
    }
}
