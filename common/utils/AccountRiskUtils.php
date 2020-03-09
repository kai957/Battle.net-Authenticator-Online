<?php
use App\User;
use Illuminate\Http\Request;
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
    const TABLE_LOGIN_LOG = "t_login_log";
    const DANGER_AREA = ["金华", "许昌", "泰州"];

    private static function updateUserRight($id, $risk, $desc)
    {
        DB::table(self::TABLE_USER)->where('user_id', $id)->update([
            "user_risk" => $risk,
            "user_risk_desc" => $desc
        ]);
    }

    public static function insertLoginLog(User $user, $addressArray, $userAgent)
    {
        if (count($addressArray) > 0) {
            if (strlen(@$addressArray['country']) > 0) {
                $country = $addressArray['country'];
            } else {
                $country = "未知";
            }
            if (strlen(@$addressArray['city']) > 0) {
                $location = $addressArray['city'];
            } else {
                $location = "未知";
            }
            if (strlen(@$addressArray['area']) > 0) {
                $area = $addressArray['area'];
            } else {
                $area = "未知";
            }
        } else {
            $country = "未知";
            $location = "未知";
            $area = "未知";
        }
        DB::table(self::TABLE_LOGIN_LOG)->insert([
            "user_id" => $user->getUserId(),
            "login_ip" => $user->getUserThisTimeLoginIP(),
            "time" => date('Y-m-d H:i:s'),
            "country" => $country,
            "location" => $location,
            "area" => $area,
            "ua" => $userAgent,
            "ua_crc_32" => Functions::crc32($userAgent)
        ]);
    }

    public static function checkRisk(User $user, Request $request)
    {
        if ($user->getUserDonated() == 1 || $user->getUserHasHookRight() == 1 || $user->getUserRight() == User::USER_BUSINESS_COOPERATION) {
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
                if (strpos($addressArray['city'], $city) !== false) {
                    $riskInt += 5;
                    $riskDescArray[] = "地理($city)";
                    break;
                }
            }
        }
        $userAgent = $request->header('User-Agent');
        if (strlen($userAgent) > 500) {
            $userAgent = substr($userAgent, 0, 500);
        }
        $sameIpAndUaCount = self::getSameIpAndUACount($user->getUserId(), $user->getUserThisTimeLoginIP(), $userAgent);
        if ($sameIpAndUaCount >= 5) {//同IP数量大于5
            $riskInt += $sameIpAndUaCount;
            $riskDescArray[] = "历史IP($sameIpAndUaCount)";
        } elseif ($sameIpAndUaCount <= -3) {//同IP及UA数量大于3
            $sameIpAndUaCount = abs($sameIpAndUaCount);
            $riskInt += $sameIpAndUaCount * 5;
            $riskDescArray[] = "历史UI($sameIpAndUaCount)";
        }
        if ($user->getUserEmailChecked() == 0) {
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
        AccountRiskUtils::insertLoginLog($user, $addressArray, $userAgent);
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

    private static function getNameLikeCount($userId, $name)
    {
        if (strlen($name) < 1) {
            return 0;
        }
        return DB::table(self::TABLE_USER)->where('user_id', "<>", $userId)->where('user_name', 'like', "$name%")->count();
    }

    private static function getSameIpAndUACount($userId, $ip, $ua)
    {
        if (strlen($ua) < 1) {
            return DB::table(self::TABLE_LOGIN_LOG)->where('user_id', "<>", $userId)->where("login_ip", $ip)->count();
        }
        return -1 * DB::table(self::TABLE_LOGIN_LOG)->where('user_id', "<>", $userId)->where("login_ip", $ip)->where("ua_crc_32", Functions::crc32($ua))->count();
    }
}