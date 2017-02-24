<?php
//fix
include('includes/config.php');
$rightmd5="902a211a9d76f74d99e2cae960dd5070";
$name = db_iconv("name");
$name == "" ? $name = "匿名土豪" : 1;
$time = db_iconv("time");
$unixtime = strtotime($time);
$bizhong = db_iconv("bizhong");
empty($bizhong) ? $bizhong = "未知" : $bizhong = "$bizhong";
$count = db_iconv("count");
if ($unixtime < 1000) {
    if (isset($_POST['time'])) {
        $shujuda = true;
        $shujudadata = "错误的时间";
    }
} else {
    if (!is_numeric($count)) {
        $shujuda = true;
        $shujudadata = "错误的数字";
    } else {
        $password = db_iconv("password");
        if (md5("d123hihauihdudhffbbsa".$password."bioghuibuigkughugih") == $rightmd5) {//lw
            insert("INSERT INTO `donate_data`(`donate_name`, `donate_time`, `donate_bizhong`, `donate_count`) VALUES ('$name',$unixtime,'$bizhong',$count)");
            $shujuda = true;
            $shujudadata = "添加成功";
        } else {
            $shujuda = true;
            $shujudadata = "错误的密码";
        }
    }
}
?>

<html>
    <head> 
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta property="og:image" content="resources/weiboimg/fbshare.png" />
        <title>战网安全令在线版</title>
        <link rel="shortcut icon" type="image/x-icon" href="resources/img/favicon.ico"> 
    </head>
    <body>
        <?php
        if ($shujuda = true) {
            echo "$shujudadata<br>";
        }
        ?>
        <form action="adddonate.php" method="post">
            土豪昵称：<input name="name" type="text"/><br>
            捐赠时间：<input name="time" type="text"/><br>
            捐赠币种：<input name="bizhong" type="text"><br>
            捐赠数量：<input name="count" type="text"/><br>
            添加密码：<input name="password" type="password"/><br>
            <input type="submit" value="提交"/>
        </form>
    </body>
</html>
<?php closeMysqlConnect();?>
