<?php

namespace App\Http\Controllers\Wechat;


use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserInfoController extends Controller
{

    function __construct()
    {
        $this->middleware("wechat.check");
    }

    public function userInfo(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $data = [
            'userId' => $user->getUserId(),
            'userName' => $user->getUserName(),
            'userEmail' => $user->getUserEmail(),
            'userEmailChecked' => $user->getUserEmailChecked() == 1,//邮箱是否确认成功(邮件点击链接)
            'userRight' => $user->getUserRight(),//0：普通，1：共享，9：商务合作，999：封禁
            'userRegisterTime' => $user->getUserRegisterTime(),
            'userLastLoginTime' => $user->getUserLastLoginTime(),
            'userThisLoginTime' => $user->getUserThisLoginTime(),
            'userLastLoginIp' => $user->getUserLastTimeLoginIP(),
            'userThisLoginIp' => $user->getUserThisTimeLoginIP(),
            'userDonated' => $user->getUserDonated() == 1
        ];
        $json = ['code' => 200, 'message' => "获取用户信息成功",
            'data' => $data];
        return response()->json($json);
    }

}