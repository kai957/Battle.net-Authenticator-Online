{{HTML::style('/resources/css/accountbody.css')}}
{{HTML::script('/resources/js/lobby.js')}}
{{HTML::script('/resources/js/account.js')}}
<script src="http{{Functions::isHTTPS()?"s":""}}://ip.ws.126.net/ipquery?ip={{$_USER->getUserLastTimeLoginIP()}}"></script>
<script>
    $(document).ready(function () {
        if (localAddress !== null) {
            ipLocation = localAddress['province'] + localAddress['city'];
        }
        $("#lastLoginIpAddress").html($("#lastLoginIpAddress").html().trim() + " (" + ipLocation + ")");
    });
</script>
@if($_USER->getUserRight() == $_USER::USER_BUSINESS_COOPERATION && !empty($_USER->getUserPasswordToDownloadCsv()))
<script>
    function downloadCsvByPassword() {
        csvPass = prompt("请输入您设置的合作账号下载备份CSV专用密码\n输入错误的密码将下载到空白CSV文件");
        if (csvPass === null || csvPass.length < 1) {
            alert("请输入密码");
            return;
        }
        window.location.href = "/MyAuthList.csv?pass="+csvPass;
    }
</script>
@endif