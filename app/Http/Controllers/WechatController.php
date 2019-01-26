<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WechatController extends Controller
{
    private $app;
    private $user_manager;

    public function __construct()
    {
        $this->app = app('wechat.official_account');
        $this->user_manager = $this->app->user;

    }

    public function test()
    {
        dd($this->user_manager->get('oKHQjuN4kbYFTnpA_SQto4BejrYo'));
    }

    public function serve()
    {
        Log::info('I am wechat server');

        $app = app('wechat.official_account');

        $app->server->push(function($message) use($app){
            $userManager  = $app->user;

//            $user = $userManager->get($message->FromUserName);
            Cache::put('user',$userManager,2);


            $responseMsg = '';
            switch ($message->MsgType) {
                case 'subscribe': // 关注事件
                    $responseMsg = '欢迎您关注 Coding10';
                    break;
                case 'text':   // 文本消息
                    $responseMsg = '我是个不会聊天的人';
                    break;
                case 'video':
                    $responseMsg = '我非常喜欢做视频';
                    break;
                case 'voice':
                    $responseMsg = '我非常喜欢做音频';
                    break;
                default:
                    $responseMsg = '我是没有个性的默认恢复消息';
                    break;
            }
            return $responseMsg;
        });

        return $app->server->serve();
    }
}
