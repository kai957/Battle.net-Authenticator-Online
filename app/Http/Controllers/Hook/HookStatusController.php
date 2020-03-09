<?php
/**
 * Created by PhpStorm.
 * User: Anh Lai
 * Date: 2017/5/12 0012
 * Time: 下午 22:00
 */


namespace App\Http\Controllers\Hook;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HookStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('base.check');
    }

    public function get(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->getIsLogin()) {
            return response("noLogin");
        }
        if (!$user->getUserHasHookRight()) {
            return response("null");
        }
        return response($user->getUserHookEnable() ? "true" : "false");
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->getUserHasHookRight()) {
            return response("2");
        }
        $enable = $request->input("enable") == "true";
        \DBHelper::updateUserHookMode($user, $enable);
        \DBHelper::insertHookLog($user, date("Y-m-d H:i:s"), $enable ? "手动设置启用" : "手动设置禁用");
        return response($enable ? "1" : "0");
    }
}