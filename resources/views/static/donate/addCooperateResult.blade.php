@include('universal.header.header_normal')
@include('static.donate.source.JsJumpAddDonateResult')
@include('universal.top.layoutTop')
<div id="layout-middle">
    <div id="homewrapper">
        <div id="content">
            <div id="page-content">
                <div id="breadcrumb">
                    <ol class="ui-breadcrumb">
                        <li><a href="/">首页</a></li>
                        <li class="last"><a href="/addCooperateDonate">添加捐赠</a></li>
                    </ol>
                </div>
            </div>
            <div id="contentloged" class="login">
                <h2 style="text-align: center;"><br><br>{{$errorString}}<br><br></h2>
                <script language="javascript">
                    Load("{{config('app.url')}}donate"); //要跳转到的页面
                </script>
            </div>
            <div id="ShowDiv"></div>

        </div>
    </div>
</div>
@include('universal.footer.bottom')