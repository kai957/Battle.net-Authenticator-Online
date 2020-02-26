<?php
use App\User;
use itbdw\Ip\IpLocation;

/**
 * Created by PhpStorm.
 * User: Anh Lai
 * Date: 2020/2/26
 * Time: 下午 15:05
 */
class AccountRiskUtils
{
    const TABLE_USER = "t_user";
    const DANGER_AREA = ["金华", "许昌"];

    private static function updateUserRight($id, $risk, $desc)
    {
        DB::table(self::TABLE_USER)->where('user_id', $id)->update([
            "user_risk" => $risk,
            "user_risk_desc" => $desc
        ]);
    }

    public static function checkRisk(User $user)
    {
        if ($user->getUserDonated() == 1 || $user->getUserRight() == User::USER_BUSINESS_COOPERATION) {
            self::updateUserRight($user->getUserId(), 0, "");
            return;
        }
        $riskInt = 0;
        $riskDescArray = [];
        $noNumName = self::getNoNumName($user->getUserName());
        $sameNameCount = self::getNameLikeCount($user->getUserId(), $noNumName);
        if ($sameNameCount > 3) {
            $riskInt += $sameNameCount * 3;
            $riskDescArray[] = "用户名($noNumName,$sameNameCount)";
        }
        $samePasswordCount = self::getSamePasswordCount($user->getUserId(), $user->getUserPass());
        if ($samePasswordCount > 3) {
            $riskInt += $samePasswordCount * 3;
            $riskDescArray[] = "密码($samePasswordCount)";
        }
        $sameEmailCount = self::getSameEmailCount($user->getUserId(), $user->getUserEmail());
        if ($sameEmailCount > 3) {
            $riskInt += $sameEmailCount * 3;
            $riskDescArray[] = "邮箱($sameEmailCount)";
        }
        $sameIpCount = self::getSameIpCount($user->getUserId(), $user->getUserRegisterIP());
        if ($sameIpCount > 3) {
            $riskInt += $sameIpCount;
            $riskDescArray[] = "注册IP($sameIpCount)";
        }
        $sameIpCount = self::getSameIpCount($user->getUserId(), $user->getUserThisTimeLoginIP());
        if ($sameIpCount > 3) {
            $riskInt += $sameIpCount;
            $riskDescArray[] = "本次IP($sameIpCount)";
        }
        $sameQACount = self::getSameQuestionCount($user->getUserId(), $user->getUserQuestion(), $user->getUserAnswer());
        if ($sameQACount > 10) {
            $riskInt += ceil($sameQACount / 10);
            $riskDescArray[] = "问答($sameQACount)";
        }
        $addressArray = IpLocation::getLocation($user->getUserThisTimeLoginIP(), storage_path("qqwry.dat"));
        if (count($addressArray) > 0 && strlen(@$addressArray['city']) > 0) {
            foreach (self::DANGER_AREA as $city) {
                if (strpos($addressArray['city'], $city) >= 0 || strpos($city, $addressArray['city']) >= 0) {
                    $riskInt += 5;
                    $riskDescArray[] = "地理($city)";
                    break;
                }
            }
        }
        if ($user->getUserEmailChecked() == 1) {
            $riskInt++;
            $riskDescArray[] = "邮箱未验证";
        }
        if (strlen($user->getWechatOpenID()) > 0) {
            $riskInt--;
        }
        if ($riskInt < 0) {
            $riskInt = 0;
        }
        self::updateUserRight($user->getUserId(), $riskInt, implode(",", $riskDescArray));
    }

    private static function getSamePasswordCount($id, $password)
    {
        return DB::table(self::TABLE_USER)->where('user_id', "<>", $id)->where('user_pass', $password)->count();
    }

    private static function getSameEmailCount($id, $email)
    {
        return DB::table(self::TABLE_USER)->where('user_id', "<>", $id)->where('user_email', $email)->count();
    }

    private static function getSameIpCount($id, $ip)
    {
        return DB::table(self::TABLE_USER)
            ->where("user_id", "<>", $id)
            ->where(function ($query) use ($ip) {
                $query->Where('user_last_login_ip', $ip)->orWhere('user_this_login_ip', $ip);
            })
            ->count();
    }

    private static function getSameQuestionCount($id, $questionId, $answer)
    {
        return DB::table(self::TABLE_USER)
            ->where("user_id", "<>", $id)
            ->where("user_question", $questionId)
            ->where("user_answer", $answer)
            ->count();
    }

    private static function getNoNumName($name)
    {
        if (is_numeric($name)) {
            return "";
        }
        while (str_contains(substr($name, strlen($name) - 1),
            ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
                "`", "~", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "-", "=", "+",
                "[", "{", "]", "}", ";", ":", "'", "\"", ",", "<", ".", ">", "/", "?"
            ])) {
            $name = substr($name, 0, strlen($name) - 1);
        }
        if (strlen($name) < 3) {
            return "";
        }
        return $name;
    }

    private static function getNameLikeCount($id, $name)
    {
        if (strlen($name) < 1) {
            return 0;
        }
        return DB::table(self::TABLE_USER)->where('user_id', "<>", $id)->where('user_name', 'like', "$name%")->count();
    }

}