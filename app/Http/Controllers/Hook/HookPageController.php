<?php
/**
 * Created by PhpStorm.
 * User: Anh Lai
 * Date: 2017/5/11 0011
 * Time: 下午 12:11
 */

namespace App\Http\Controllers\Hook;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HookPageController extends Controller
{


    public function __construct()
    {
        $this->middleware('base.check');
    }

    function get(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->getIsLogin()) {
            $encodeUrl = urlencode(base64_encode("hookLog"));
            $encodeName = urlencode(base64_encode("挂机日志"));
            return redirect("login?from=$encodeUrl&fromName=$encodeName");
        }
        if (!$user->getUserHasHookRight()) {
            return redirect("");
        }
        $hookLog = \DBHelper::getHookLog($user);
        return view('hook.index')->with('_USER', $user)->with("topNavValueText", "挂机日志")->with("hookLog", $hookLog);
    }
}