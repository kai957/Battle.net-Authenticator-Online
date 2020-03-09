<?php

namespace App;

use Functions;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DBHelper;
use RedisHelper;

class User extends Authenticatable
{
    const TAG = "_USER";
    const USER_BANED = 999;
    const USER_SHARED = 1;
    const USER_NORMAL = 0;
    const USER_BUSINESS_COOPERATION = 9;

    private $isLogin = false;

    private $wechatTokenBean;

    private $userId;
    private $userName;
    private $userPass;
    private $userRight;
    private $userShowAllInIndex;
    private $userPasswordToDownloadCsv;//仅供商业合作用户，这个为空则没事，否则不能查看还原码，且下载csv需要先输入密码
    private $userEmail;
    private $userEmailChecked;
    private $userRegisterIP;
    private $userRegisterTime;
    private $userQuestion;
    private $userAnswer;
    private $userEmailCheckToken;
    private $userEmailFindPasswordToken;
    private $userEmailFindPasswordMode;
    private $userPasswordResetToken;
    private $userPasswordResetTokenUsed;
    private $userLastTimeLoginIP;
    private $userThisTimeLoginIP;
    private $userLastLoginTime;
    private $userThisLoginTime;
    private $lastUsedSessionTime;
    private $userDonated;
    private $lastResetPasswordTime;
    private $wechatOpenID;
    private $userHasHookRight;//是否有控制挂机权限
    private $userHookEnable;//是否控制客户端自动挂机
    private $userHookLastUpdateTime;//客户端上次同步时间


    public function getAccountRightString()
    {
        if ($this->getUserRight() == User::USER_SHARED) {
            return "共享账号";
        }
        if ($this->getUserRight() == User::USER_BUSINESS_COOPERATION) {
            return "商务合作账号";
        }
        if ($this->getUserRight() == User::USER_BANED) {
            return "封禁账号";
        }
        return "普通账号";
    }

    /**
     * @param mixed $userShowAllInIndex
     */
    public function setUserShowAllInIndex($userShowAllInIndex)
    {
        $this->userShowAllInIndex = $userShowAllInIndex;
    }

    /**
     * @param mixed $userPasswordToDownloadCsv
     */
    public function setUserPasswordToDownloadCsv($userPasswordToDownloadCsv)
    {
        $this->userPasswordToDownloadCsv = $userPasswordToDownloadCsv;
    }


    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @param mixed $userPass
     */
    public function setUserPass($userPass)
    {
        $this->userPass = $userPass;
    }

    /**
     * @param mixed $userRight
     */
    public function setUserRight($userRight)
    {
        $this->userRight = $userRight;
    }

    /**
     * @param mixed $userEmail
     */
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;
    }

    /**
     * @param mixed $userEmailChecked
     */
    public function setUserEmailChecked($userEmailChecked)
    {
        $this->userEmailChecked = $userEmailChecked;
    }

    /**
     * @param mixed $userRegisterTime
     */
    public function setUserRegisterTime($userRegisterTime)
    {
        $this->userRegisterTime = $userRegisterTime;
    }

    /**
     * @param mixed $userQuestion
     */
    public function setUserQuestion($userQuestion)
    {
        $this->userQuestion = $userQuestion;
    }

    /**
     * @param mixed $userAnswer
     */
    public function setUserAnswer($userAnswer)
    {
        $this->userAnswer = $userAnswer;
    }

    /**
     * @param $userEmailCheckToken
     */
    public function setUserEmailCheckToken($userEmailCheckToken)
    {
        $this->userEmailCheckToken = $userEmailCheckToken;
    }

    /**
     * @param $userEmailFindPasswordToken
     */
    public function setUserEmailFindPasswordToken($userEmailFindPasswordToken)
    {
        $this->userEmailFindPasswordToken = $userEmailFindPasswordToken;
    }

    /**
     * @param $userEmailFindPasswordMode
     */
    public function setUserEmailFindPasswordMode($userEmailFindPasswordMode)
    {
        $this->userEmailFindPasswordMode = $userEmailFindPasswordMode;
    }

    /**
     * @param mixed $userPasswordResetToken
     */
    public function setUserPasswordResetToken($userPasswordResetToken)
    {
        $this->userPasswordResetToken = $userPasswordResetToken;
    }

    /**
     * @param mixed $userPasswordResetTokenUsed
     */
    public function setUserPasswordResetTokenUsed($userPasswordResetTokenUsed)
    {
        $this->userPasswordResetTokenUsed = $userPasswordResetTokenUsed;
    }

    /**
     * @param mixed $userDonated
     */
    public function setUserDonated($userDonated)
    {
        $this->userDonated = $userDonated;
    }

    /**
     * @param mixed $wechatOpenID
     */
    public function setWechatOpenID($wechatOpenID)
    {
        $this->wechatOpenID = $wechatOpenID;
    }


    /**
     * @param mixed $userRegisterIP
     */
    public function setUserRegisterIP($userRegisterIP)
    {
        $this->userRegisterIP = $userRegisterIP;
    }

    /**
     * @param mixed $userLastTimeLoginIP
     */
    public function setUserLastTimeLoginIP($userLastTimeLoginIP)
    {
        $this->userLastTimeLoginIP = $userLastTimeLoginIP;
    }

    /**
     * @param mixed $userThisTimeLoginIP
     */
    public function setUserThisTimeLoginIP($userThisTimeLoginIP)
    {
        $this->userThisTimeLoginIP = $userThisTimeLoginIP;
    }

    /**
     * @param mixed $userLastLoginTime
     */
    public function setUserLastLoginTime($userLastLoginTime)
    {
        $this->userLastLoginTime = $userLastLoginTime;
    }

    /**
     * @param mixed $userThisLoginTime
     */
    public function setUserThisLoginTime($userThisLoginTime)
    {
        $this->userThisLoginTime = $userThisLoginTime;
    }

    /**
     * @param mixed $lastUsedSessionTime
     */
    public function setLastUsedSessionTime($lastUsedSessionTime)
    {
        $this->lastUsedSessionTime = $lastUsedSessionTime;
    }

    /**
     * @param bool $isLogin
     */
    public function setIsLogin($isLogin)
    {
        $this->isLogin = $isLogin;
    }

    /**
     * @param mixed $userHasHookRight
     */
    public function setUserHasHookRight($userHasHookRight)
    {
        $this->userHasHookRight = $userHasHookRight;
    }

    /**
     * @param mixed $userHookEnable
     */
    public function setUserHookEnable($userHookEnable)
    {
        $this->userHookEnable = $userHookEnable;
    }
 
    /**
     * @param mixed $userHookLastUpdateTime
     */
    public function setUserHookLastUpdateTime($userHookLastUpdateTime)
    {
        $this->userHookLastUpdateTime = $userHookLastUpdateTime;
    }

    public function initUserByNameAndPassword($userName, $userPass)
    {
        if (empty($userName) || empty($userPass)) {
            return;
        }
        $user = DBHelper::getUserByNameAndPassword($userName, $userPass);
        self::writeUser($user);
    }

    public function initUserByUserNameAndUserId($userName, $userId)
    {
        if (empty($userName) || empty($userId) || !Functions::isInt($userId)) {
            return;
        }
        $user = DBHelper::getUserByUserNameAndUserId($userName, $userId);
        self::writeUser($user);
    }


    public function initUserByUserId($userId)
    {
        if (empty($userId) || !Functions::isInt($userId)) {
            return;
        }
        $user = DBHelper::getUserByUserId($userId);
        self::writeUser($user);
    }


    public function initUserByWechatOpenId($openId)
    {
        if (empty($openId)) {
            return;
        }
        $user = DBHelper::getUserByWechatOpenId($openId);
        self::writeUser($user);
    }

    public function initUserByUserName($userName)
    {
        if (empty($userName)) {
            return;
        }
        $user = DBHelper::getUserByUserName($userName);
        self::writeUser($user);
    }


    private function writeUser($user)
    {
        if (empty($user->user_id)) {
            return;
        }
        $this->userId = $user->user_id;
        $this->userName = $user->user_name;
        $this->userPass = $user->user_pass;
        $this->userRight = $user->user_right;
        $this->userShowAllInIndex = $user->user_show_all_in_index;
        $this->userPasswordToDownloadCsv = $user->user_auth_pass;
        $this->userEmail = $user->user_email;
        $this->userEmailChecked = $user->user_email_checked;
        $this->userRegisterIP = $user->user_register_ip;
        $this->userRegisterTime = $user->user_register_time;
        $this->userQuestion = $user->user_question;
        $this->userAnswer = $user->user_answer;
        $this->userEmailCheckToken = $user->user_email_check_token;
        $this->userEmailFindPasswordToken = $user->user_email_find_password_token;
        $this->userEmailFindPasswordMode = $user->user_email_find_password_mode;
        $this->userPasswordResetToken = $user->user_password_reset_token;
        $this->userPasswordResetTokenUsed = $user->user_password_reset_token_used;
        $this->userLastTimeLoginIP = $user->user_last_login_ip;
        $this->userThisTimeLoginIP = $user->user_this_login_ip;
        $this->userLastLoginTime = $user->user_last_login_time;
        $this->userThisLoginTime = $user->user_this_login_time;
        $this->lastUsedSessionTime = $user->user_last_used_session_time;
        $this->userDonated = $user->user_donated;
        $this->wechatOpenID = $user->user_wechat_openid;
        $this->lastResetPasswordTime = $user->user_last_reset_password_time;
        $this->userHasHookRight = $user->user_hook == 1;
        $this->userHookEnable = $user->user_hook_mode == 1;
        $this->userHookLastUpdateTime = $user->user_hook_last_update;
    }

    /**
     * @return mixed
     */
    public function getLastResetPasswordTime()
    {
        return $this->lastResetPasswordTime;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return mixed
     */
    public function getUserPass()
    {
        return $this->userPass;
    }

    /**
     * @return mixed
     */
    public function getUserRight()
    {
        return $this->userRight;
    }

    /**
     * @return mixed
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * @return mixed
     */
    public function getUserEmailChecked()
    {
        return $this->userEmailChecked;
    }

    /**
     * @return mixed
     */
    public function getUserRegisterTime()
    {
        return $this->userRegisterTime;
    }

    /**
     * @return mixed
     */
    public function getUserQuestion()
    {
        return $this->userQuestion;
    }

    /**
     * @return mixed
     */
    public function getUserAnswer()
    {
        return $this->userAnswer;
    }

    /**
     * @return mixed
     */
    public function getUserEmailCheckToken()
    {
        return $this->userEmailCheckToken;
    }

    /**
     * @return mixed
     */
    public function getUserEmailFindPasswordToken()
    {
        return $this->userEmailFindPasswordToken;
    }

    /**
     * @return mixed
     */
    public function getUserEmailFindPasswordMode()
    {
        return $this->userEmailFindPasswordMode;
    }

    /**
     * @return mixed
     */
    public function getUserPasswordResetToken()
    {
        return $this->userPasswordResetToken;
    }

    /**
     * @return mixed
     */
    public function getUserPasswordResetTokenUsed()
    {
        return $this->userPasswordResetTokenUsed;
    }

    /**
     * @return mixed
     */
    public function getUserRegisterIP()
    {
        return $this->userRegisterIP;
    }

    /**
     * @return mixed
     */
    public function getUserLastTimeLoginIP()
    {
        return $this->userLastTimeLoginIP;
    }

    /**
     * @return mixed
     */
    public function getUserThisTimeLoginIP()
    {
        return $this->userThisTimeLoginIP;
    }

    /**
     * @return mixed
     */
    public function getUserLastLoginTime()
    {
        return $this->userLastLoginTime;
    }

    /**
     * @return mixed
     */
    public function getUserThisLoginTime()
    {
        return $this->userThisLoginTime;
    }

    /**
     * @return mixed
     */
    public function getLastUsedSessionTime()
    {
        return $this->lastUsedSessionTime;
    }

    /**
     * @return mixed
     */
    public function getUserDonated()
    {
        return $this->userDonated;
    }

    /**
     * @return mixed
     */
    public function getWechatOpenID()
    {
        return $this->wechatOpenID;
    }


    /**
     * @return mixed
     */
    public function getIsLogin()
    {
        return $this->isLogin;
    }

    /**
     * @return mixed
     */
    public function getWechatTokenBean()
    {
        return $this->wechatTokenBean;
    }

    /**
     * @param mixed $wechatTokenBean
     */
    public function setWechatTokenBean($wechatTokenBean)
    {
        $this->wechatTokenBean = $wechatTokenBean;
    }

    /**
     * @return mixed
     */
    public function getUserShowAllInIndex()
    {
        return $this->userShowAllInIndex;
    }


    /**
     * @return string
     */
    public function getUserPasswordToDownloadCsv()
    {
        return $this->userPasswordToDownloadCsv;
    }

    /**
     * @return mixed
     */
    public function getUserHasHookRight()
    {
        return $this->userHasHookRight;
    }

    /**
     * @return mixed
     */
    public function getUserHookEnable()
    {
        return $this->userHookEnable;
    }


    /**
     * @return mixed
     */
    public function getUserHookLastUpdateTime()
    {
        return $this->userHookLastUpdateTime;
    }

}
