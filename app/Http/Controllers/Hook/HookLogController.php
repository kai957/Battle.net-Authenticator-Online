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

class HookLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('base.check');
    }

    public function insert(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->getUserHasHookRight()) {
            return response()->json(["code" => 200]);
        }
        $data = $request->input("log");
        $logArray = explode("\n", $data);
        if (count($logArray) < 1) {
            return response()->json(["code" => 200]);
        }
        for ($i = 0; $i < count($logArray); $i++) {
            $info = explode("||||||", $logArray[$i]);
            if (count($info) != 2) {
                continue;
            }
            if (strlen($info[1]) < 1) {
                continue;
            }
            $time = @strtotime($info[0]);
            if ($time < time() - 86400) {
                continue;
            }
            \DBHelper::insertHookLog($user, date("Y-m-d H:i:s", $time), $info[1]);
        }
        return response()->json(["code" => 200]);
    }
}