<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WechatController extends Controller
{
    private $wechat;
    private $user_manager;

    public function __construct()
    {
        $this->wechat = app('wechat.official_account');
        $this->user_manager = $this->wechat->user;

    }

    public function test()
    {
        $user_openids = $this->user_manager->list()->data->openid;
        //$this->user_manager->select($user_openids);
        dd($this->user_manager->select($user_openids));
        //->get('oKHQjuN4kbYFTnpA_SQto4BejrYo')
        //dd($this->user_manager->get('oKHQjuN4kbYFTnpA_SQto4BejrYo')->nickname);
    }

    public function userList()
    {
        dd();
    }

    public function testUser()
    {
        //->get('oKHQjuN4kbYFTnpA_SQto4BejrYo')
        $user = Cache::get('user');

        dd(Cache::get('user'));
    }

    public function authUser()
    {
        $user_auth = $this->wechat->oauth->user();
        dd($user_auth);
    }

    public function createMenu()
    {
        $buttons = [
            [
                "type" => "click",
                "name" => "关于我们",
                "key"  => "BUTTON_ABOUT_US"
            ],
            [
                "name"       => "菜单",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "官网",
                        "url"  => "http://www.laravel-tube.com/oauth"
                    ],
                    [
                        "type" => "view",
                        "name" => "视频",
                        "url"  => "http://v.qq.com/"
                    ],
                    [
                        "type" => "click",
                        "name" => "赞一下我们",
                        "key" => "V1001_GOOD"
                    ],
                ],
            ],
        ];
        $this->wechat->menu->create($buttons);
    }

    public function serve()
    {
        Log::info('I am wechat server');

        $wechat = app('wechat.official_account');

        $wechat->server->push(function($message) use($wechat){
            $user = $wechat->user->get($message->FromUserName);
            Cache::put('user',$user,10);
            $responseMsg = '';

            switch ($message->MsgType) {
                case 'event':
                    switch ($message->Event) {
                        case 'subscribe':
                            return '欢迎您关注 Coding10 公众号';

                        case 'CLICK':
                            switch ($message->EventKey) {
                                case 'BUTTON_ABOUT_US':
                                    $user_auth = $wechat->oauth->user();
                                    return $user_auth->nickname.'你点击了关于我们';
                                case 'V1001_GOOD':
                                    return '你点击了赞一下我们';
                            }
                            return '';
                        default:
                            break;
                    }
                    $responseMsg = $user->nickname.'欢迎您关注 Coding10';
                    break;
                case 'text':   // 文本消息
                    $responseMsg = $user->nickname.'我是个不会聊天的人';
                    break;
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

        return $wechat->server->serve();
    }
}
