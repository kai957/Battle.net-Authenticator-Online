<?php

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
if (!check_data("json")) {
    closeMysqlConnect();
    die("");
}
$accept = "false";
if (check_data("accept")) {
    $accept = strtolower($_POST['accept']);
    if ($accept != "true") {
        $accept = "false";
    }
}
$jsonArray = json_decode($_POST['json'], true);
if (!checkOneKeyCommitJsonArrayVaild($jsonArray)) {
    closeMysqlConnect();
    die("");
}
$auth_id = $jsonArray['data']['auth_id'];
if (!is_null($user)) {
    $sql = "SELECT `user_id` FROM `users` WHERE `user_name`='$user'";
    $user_id = queryValue($sql);
} else {
    closeMysqlConnect();
    die("");
}
if (!is_null($user_id) && !is_null($auth_id)) {
    $sql = "SELECT * FROM `authdata` WHERE `user_id`='$user_id' AND `auth_id`='$auth_id'";
    $row = queryRow($sql);
}
if ($row) {
    $time = date('Y-m-d H:i:s');
    $region = $row['region'];
    if ($region != "CN" && $region != "EU") {
        $region = "US";
    }
    $sql = "SELECT * FROM `synctime` WHERE `region`='$region'";
    $rowSYNC = queryRow($sql);

    if (strtotime($time) - strtotime($rowSYNC['last_sync']) > 86400) {
        $auth = Authenticator::factory($row['serial'], $row['secret']);
        $sql = "UPDATE `synctime` SET `sync`=\"" . $auth->getsync() . "\" ,`last_sync`=\"$time\" WHERE `region`='$region'";
        update($sql);
    } else {
        $auth = Authenticator::factory($row['serial'], $row['secret'], $rowSYNC['sync']);
    }
    $code = $auth->code();
    $plainSerial = $auth->plain_serial();
    $newUrl = $jsonArray['data']['callback_url'];
    $data = "serial={$plainSerial}&code={$code}&requestId={$jsonArray['data']['request_id']}&accept={$accept}";
    _cpost($newUrl, $data);
    $json['code'] = 0;
    $json['message'] = $accept=="true" ? "已允许" : "已拒绝";
    echo json_encode($json);
}


closeMysqlConnect();
