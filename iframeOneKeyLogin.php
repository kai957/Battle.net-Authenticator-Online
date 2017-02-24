<?php

include('includes/config.php');
include('includes/oneKeyLogin/html_toubu_iframe_one_key_login.php');
include('includes/oneKeyLogin/login_check.php');

function getTimeText($time) {
    $nowTime = time();
    if ($nowTime - $time < 10) {
        return "刚刚";
    }
    if ($nowTime - $time < 60) {
        return ($nowTime - $time)."秒前";
    }
    return (ceil(($nowTime - $time) / 60)-1) . "分钟前";
}

if ($logincheck == 0) {
    include ('includes/oneKeyLogin/iframe_onekeylogin_nologin.php');
} elseif ($logincheck == 1) {
    if (!check_data("json", "get")) {
        include ('includes/oneKeyLogin/iframe_onekeylogin_noright.php');
        return;
    }
    $jsonArray = json_decode($_GET['json'], true);
    if (!checkOneKeyCommitJsonArrayVaild($jsonArray)) {
        include ('includes/oneKeyLogin/iframe_onekeylogin_noright.php');
        return;
    }
    $time = time();
    if ($time - $jsonArray['data']['time'] > 180) {
        include ('includes/oneKeyLogin/iframe_onekeylogin_timeout.php');
        return;
    }
    include ('includes/oneKeyLogin/iframe_onekeylogin_normal.php');
}
closeMysqlConnect();
?>


