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
        //->get('oKHQjuN4kbYFTnpA_SQto4BejrYo')
        dd($this->user_manager->get('oKHQjuN4kbYFTnpA_SQto4BejrYo')->nickname);
    }

    public function testUser()
    {
        //->get('oKHQjuN4kbYFTnpA_SQto4BejrYo')
        $user = Cache::get('user');

        dd(Cache::get('user'));
    }

    public function serve()
    {
        Log::info('I am wechat server');

        $app = app('wechat.official_account');

        $app->server->push(function($message) use($app){
            $user = $app->user->get($message->FromUserName);
            Cache::put('user',$user,10);
            $responseMsg = '';

            switch ($message->MsgType) {
                case 'subscribe': // 关注事件
                    $responseMsg = $user->nickname.'欢迎您关注 Coding10';
                    break;
                case 'text':   // 文本消息
                    $responseMsg = $user->nickname.'我是个不会聊天的人';
                case 'image':
                    $responseMsg = $user->nickname.'收到图片消息';
                    break;
                case 'video':
                    $responseMsg = $user->nickname.'我非常喜欢做视频';
                    break;
                case 'voice':
                    $responseMsg = $user->nickname.'我非常喜欢做音频';
                    break;
                default:
                    $responseMsg = $user->nickname.'我是没有个性的默认恢复消息';
                    break;
            }
            return $responseMsg;
        });

        return $app->server->serve();
    }
}
