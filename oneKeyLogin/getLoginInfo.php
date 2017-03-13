<?php

set_time_limit(10);
session_start();
include('../includes/config.php');
include('../classes/Authenticator.php');
if (isset($_SESSION['loginuser']) && !empty($_SESSION['loginuser'])) {
    $user = mysqli_real_escape_string($dbconnect, htmlspecialchars($_SESSION['loginuser']));
    $logincheck = 1;
} else if (isset($_COOKIE['loginname']) && isset($_COOKIE['loginid']) && $_COOKIE['loginname'] != "" && $_COOKIE['loginid'] != "") {
    $usertmp = mysqli_real_escape_string($dbconnect, htmlspecialchars($_COOKIE['loginname']));
    $cookievalue = mysqli_real_escape_string($dbconnect, htmlspecialchars($_COOKIE['loginid'], ENT_QUOTES));
    $sql = "SELECT * FROM `cookiedata` WHERE `user_name`='$usertmp' AND `user_cookie` ='$cookievalue'";
    $result = queryRow($sql);
    if ($result) {
        $rowtemp = $result;
        $timedifference = time() - strtotime($rowtemp['login_time']);
        if ($timedifference <= 30 * 24 * 60 * 60) {
            $user = $usertmp;
            $sql = "SELECT * FROM `users` WHERE `user_name`='$user'";
            $rowtemp = queryRow($sql);
            $user_thistimelogin_ip = $rowtemp['user_thistimelogin_ip'];
            $user_thislogin_time = $rowtemp['user_thislogin_time'];
            $user_right = $rowtemp['user_right'];
            if ($user_right == 1) {
                if ($timedifference > 1800) {
                    $sql = "DELETE FROM `cookiedata` WHERE `user_name`='$user' AND `user_cookie` ='$cookievalue'";
                    delete($sql);
                    setcookie("loginname", "", time() - 3600, "/", null, false, true);
                    setcookie("loginid", "", time() - 3600, "/", null, false, true);
                    $logincheck = 0;
                } else {
                    $logincheck = 1;
                    $userip = getIP();
                    $sql = "UPDATE `cookiedata` SET `user_login_ip`='$userip' WHERE `user_name`='$user' AND `user_cookie` ='$cookievalue'";
                    update($sql);
                }
            } else {
                $_SESSION['loginuser'] = $user;
                $logincheck = 1;
                $userip = getIP();
                $date = date('Y-m-d H:i:s');
                $sql = "UPDATE `cookiedata` SET `user_login_ip`='$userip' WHERE `user_name`='$user' AND `user_cookie` ='$cookievalue'";
                update($sql);
                $sql = "UPDATE `users` SET `user_lastlogin_ip`='$user_thistimelogin_ip',`user_thistimelogin_ip`='$userip',`user_lastlogin_time`='$user_thislogin_time', `user_thislogin_time`='$date' WHERE `user_name`='$user'";
                update($sql);
            }
        } else {
            $sql = "DELETE FROM `cookiedata` WHERE `user_name`='$usertmp' AND `user_cookie` ='$cookievalue'";
            delete($sql);
            setcookie("loginname", "", time() - 3600, "/", null, false, true);
            setcookie("loginid", "", time() - 3600, "/", null, false, true);
            $logincheck = 0;
        }
    }
} else {
    closeMysqlConnect();
    die("");
}
session_write_close();
if ($logincheck != 1) {
    closeMysqlConnect();
    die("");
}
if (!is_null($user)) {
    $sql = "SELECT `user_id` FROM `users` WHERE `user_name`='$user'";
    $user_id = queryValue($sql);
}
if (check_data('authid', 'get') && ctype_digit($_GET['authid'])) {
    $authid = $_GET['authid'];
}
if (!is_null($user_id) && !is_null($authid)) {
    $sql = "SELECT * FROM `authdata` WHERE `user_id`='$user_id' AND `auth_id`='$authid'";
    $row = queryRow($sql);
}
if ($row) {
    $time = date('Y-m-d H:i:s');
    $region = $row['region'];
    if ($region != "CN" && $region != "EU") {
        $region = "US";
    }
    switch ($region) {
        case "CN":
            $oneKeyLoginRequestUrl = rand(0, 1) == 1 ? "https://cn.battle.net/login/authenticator/pba" : "https://www.battlenet.com.cn/login/authenticator/pba";
            break;
        case "EU":
            $oneKeyLoginRequestUrl = "https://eu.battle.net/login/authenticator/pba";
            break;
        default :
            $oneKeyLoginRequestUrl = "https://us.battle.net/login/authenticator/pba";
            break;
    }
    $sql = "SELECT * FROM `synctime` WHERE `region`='$region'";
    $rowSYNC = queryRow($sql);

//    if (strtotime($time) - strtotime($rowSYNC['last_sync']) > 86400) {//降低数据库压力，很明显，能请求之前必然不会大于86400了
//        $auth = Authenticator::factory($row['serial'], $row['secret']);
//        $sql = "UPDATE `synctime` SET `sync`=\"" . $auth->getsync() . "\" ,`last_sync`=\"$time\" WHERE `region`='$region'";
//        update($sql);
//    }
    $auth = Authenticator::factory($row['serial'], $row['secret'], $rowSYNC['sync']);
//    $proxyUrl = array("", "", "");
//    $selectPosition = rand(0, 2);
//    while (true) {//降低服务器压力
    $code = $auth->code();
    $plainSerial = $auth->plain_serial();
    $newUrl = $oneKeyLoginRequestUrl . "?serial={$plainSerial}&code={$code}";
//    $requestJson = _cget($newUrl, $proxyUrl[$selectPosition], "", "caonima", "caonima");
    $requestJson = _cget($newUrl);
    if ($requestJson != "") {
        $requestJsonArray = json_decode($requestJson, true);
        if ($requestJsonArray['callback_url'] != null && $requestJsonArray['session']['request_id'] != null && ($time - $requestJsonArray['session']['time_created_millis'] / 1000) < 180) {
            $responseJson['code'] = 0;
            $data['callback_url'] = $requestJsonArray['callback_url'];
            $data['request_id'] = $requestJsonArray['session']['request_id'];
            $data['auth_id'] = $authid;
            $data['message'] = $requestJsonArray['message'];
            $data['time'] = ceil($requestJsonArray['session']['time_created_millis'] / 1000);
            $responseJson['data'] = $data;
            echo json_encode($responseJson);
        }
    }
    closeMysqlConnect();
//    if ($region == "CN") {
//        $oneKeyLoginRequestUrl = ($oneKeyLoginRequestUrl == "https://www.battlenet.com.cn/login/authenticator/pba") ? "https://cn.battle.net/login/authenticator/pba" : "https://www.battlenet.com.cn/login/authenticator/pba";
//    }
//    sleep(3);
//    }
}

