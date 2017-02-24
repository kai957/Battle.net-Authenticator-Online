<?php
defined("ZHANGXUAN") or die("no hacker.");
?>
<script language='javascript' type='text/javascript'>
    var secs = 3; //倒计时的秒数
    var isLoading = false;
    var hasResult = false;
    var commitXHR;
    var postUrl = '<?php echo SITEHOST; ?>oneKeyLogin/commitComfirmInfo.php';
    function Load(msg) {
        for (var i = secs; i >= 0; i--)
        {
            window.setTimeout('doUpdate(' + i + ',"' + msg + '")', (secs - i) * 1000);
        }
    }
    function doUpdate(num, msg)
    {
        document.getElementById('resultInfo').innerHTML = msg + ',在' + num + '秒后自动关闭';
        if (num == 0) {
            closeiframe();
        }
    }

    function commitComfirmInfo(isAgree) {
        if (isLoading || hasResult) {
            return;
        }
        isLoading = true;
        window.ActiveXObject ? commitXHR = new ActiveXObject("Microsoft.XMLHTTP") : window.XMLHttpRequest && (commitXHR = new XMLHttpRequest);
        commitXHR.open("POST", postUrl, !0);
        commitXHR.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
        commitXHR.onreadystatechange = ajaxResultChuLi;
        commitXHR.send("accept=" + isAgree + "&json=" + '<?php echo $_GET['json']; ?>');
        document.getElementById('resultInfo').innerHTML = "提交请求中";
    }
    function ajaxResultChuLi() {
        if (commitXHR.readyState == 4 && commitXHR.status == 200) {
            var textHTML = commitXHR.responseText;
            if (textHTML == "" || textHTML == null) {
                document.getElementById('resultInfo').innerHTML = "请求失败，请重试";
                isLoading = false;
                return;
            }
            jsondata = eval("(" + textHTML + ")");
            if (jsondata.code != 0) {
                document.getElementById('resultInfo').innerHTML = "请求失败，请重试";
                isLoading = false;
                return;
            }
            hasResult = true;
            isLoading = false;
            Load(jsondata.message);
        } else if (commitXHR.readyState == 4) {
            isLoading = false;
            document.getElementById('resultInfo').innerHTML = "请求失败，请重试";
        }
    }

    function autoCloseWhenTimeout()
    {
        setTimeout('closeiframe()',<?php
if (1000 * ($jsonArray['data']['time'] + 120 - time()) <= 0) {
    echo "1";
} else {
    echo 1000 * ($jsonArray['data']['time'] + 120 - time());
}
?>);

    }
    window.onload = autoCloseWhenTimeout;
</script>
<div id="embedded-login">
    <h1>Battle.net</h1>
    <a id="embedded-close" href="javascript:;" onclick="closeiframe()"></a>
    <div id="contentloged" class="login" style="padding-top: 0px;">
        <h2 style="text-align: center;color: #80c024; font-size: 40px;margin-top: 12px;"><?= $jsonArray['data']['request_id']; ?></h2><br>
        <h4 style="text-align: center;color: #d0d6d6; font-size: 24px;margin-top: 8px;"><?= $jsonArray['data']['message']; ?></h4>
        <h4 style="text-align: center;color: #d0d6d6; font-size: 24px;margin-top: 8px;"><?= getTimeText($jsonArray['data']['time']); ?></h4><br><br><br>
        <div>
            <table cellSpacing=0 cellPadding=0 width="90%" align=right border=1 rules=rows frame=hsides style="border-collapse:collapse; width: 100%;"bordercolor="#000000">
                <tr align="center">
                    <td>
                        <a href="javascript:void(0);" id="add-time" class="border-5" style="width: 60%;white-space:nowrap;" onclick="commitComfirmInfo(true);">允许</a>
                    </td>
                    <td>
                        <a href="javascript:void(0);" id="cancel-onekey" class="border-5" style="width: 60%;white-space:nowrap;" onclick="commitComfirmInfo(false);">拒绝</a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <h4 style="text-align: center;color: #d0d6d6; font-size: 18px;margin-top: 24px;" id="resultInfo"></h4>
    <div style="width:100%; position:fixed; left:0; bottom:0;">
        <h5 style="width:100%;text-align: center;font-size: 12px;color: #b2bac7;">本弹窗因您在战网或其客户端上发起了要求验证器允许登录的请求而弹出<br>关闭本弹窗将忽略这次请求,你也可以关闭并直接前去输入安全令8位验证码</h5>
    </div>
    <script>
        $(window.parent.document).find("iframe").load(function () {
            var main = $(window.parent.document).find("iframe");
            var thisheight = $(document).height();
            $(window.parent.document).find("#login-embedded").height(thisheight);
            main.height(thisheight);
        });
        $(function () {
            $('#accountName').focus();
        });
        function closeiframe() {
            window.parent.OneKeyLogin._close(true);
        }
    </script>
</div>
</body>
</html>
