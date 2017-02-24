<?php
defined("ZHANGXUAN") or die("no hacker.");
?>
<script language='javascript' type='text/javascript'>
    var secs =3; //倒计时的秒数
    var URL ;
    function Load(url){
        URL =url;
        for(var i=secs;i>=0;i--)
        {
            window.setTimeout('doUpdate(' + i + ')', (secs-i) * 1000);
        }
    }
    function doUpdate(num)
    {
        document.getElementById('ShowDiv').innerHTML = '<h3>将在'+num+'秒后自动关闭</h3>' ;
        if(num == 0) { 
            closeiframe();
        }
    }
</script>
<div id="embedded-login">
    <h1>Battle.net</h1>
    <div id="contentloged" class="login">
        <h2 style="text-align: center;"><br><br>你需要登录后才能使用.</h2><br>
        <script language="javascript">   
            Load("<?php echo SITEHOST ?>"); //要跳转到的页面   
        </script>    
    </div>
    <div id="ShowDiv"></div>
    <script> 
        $(window.parent.document).find("iframe").load(function(){
            var main = $(window.parent.document).find("iframe");
            var thisheight = $(document).height();
            $(window.parent.document).find("#login-embedded").height(thisheight);
            main.height(thisheight);
            parent.ifnotloginiframecanchangethisvalue=true;
        });
        
        function closeiframe() { 
            window.parent.OneKeyLogin._close(true);
        }
    </script>
</div>
</body>
</html>