<?php
/**
 * Created by PhpStorm.
 * User: Anh Lai
 * Date: 2017/5/11 0011
 * Time: 下午 12:11
 */

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\User;
use FindPasswordMailCheckUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class FindPasswordInMailLinkPageController extends Controller
{


    public function __construct()
    {
        $this->middleware('base.check');
    }

    function get(Request $request)
    {
        $findPasswordMailCheckUtils = new FindPasswordMailCheckUtils($request);
        if ($findPasswordMailCheckUtils->getFindPasswordMailCheckErrorCode() != 0) {
            /** @var User $user */
            $user = Auth::user();
            return view('account.findPasswordInMailLink.error')->with("_USER", $user)->with("topNavValueText", "重置密码校验")
                ->with("result", $findPasswordMailCheckUtils->getErrorString($findPasswordMailCheckUtils->getFindPasswordMailCheckErrorCode()));
        }
        $token = $findPasswordMailCheckUtils->getCreatedResetToken();
        $userId = $findPasswordMailCheckUtils->getUserId();
        return redirect("/resetPassword?userId=$userId&token=$token");
    }
}