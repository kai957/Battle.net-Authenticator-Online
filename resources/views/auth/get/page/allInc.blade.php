<div id="layout-middle">
    <div id="homewrapper">
        <div id="content">
            <div id="page-header">
                <h2 class="subcategory">
                    {{config('app.blizzard_auth_name')}}
                </h2>
                <div class="headerdiv">
                    <h3 class="headline"> 全部安全令令牌验证码 </h3>
                    <div id="rightajaxzhuangtai" class="rightajaxzhuangtai"></div>
                </div>
                <div class="processbardiv">
                    <strong id="authprogressbar" class="authprogress" style="width:0%;"></strong>
                </div>
                <div id="youshangfangtianjiaABC" class="youshangfangtianjia">
                    <div id="copydatamode" class="copydatamode"></div>
                    @if(
                    ($_USER->getUserDonated()== 1 && $authUtils->getAuthCount()<config('app.auth_max_count_donated_user'))
                    ||
                    ($authUtils->getAuthCount()<config('app.auth_max_count_standard_user'))
                    ||
                    $_USER->getUserRight() == $_USER::USER_BUSINESS_COOPERATION
                    )
                        <a class="ui-button button1" href="/addAuth" style="margin-right: 20px;">
                            <span
                                    class="button-left"><span class="button-right">
                                    添加一个安全令
                                </span>
                            </span>
                        </a>
                    @endif
                    <a class="ui-button button1" href="javascript:void(0);" onclick="refreshcodegeas();">
                            <span
                                    class="button-left"><span class="button-right">
                                    刷新令牌验证码
                                </span>
                            </span>
                    </a>
                </div>
            </div>
        </div>
        <div id="page-content" class="page-content">
            <div id="order-history">
                <div class="data-container type-table">
                    <table>
                        <thead>
                        <tr class="">
                            <th align="left"><span class="arrow">
                                        安全令编号</span>
                            </th>
                            <th align="left"><span class="arrow">
                                        名称
                                    </span></th>
                            <th align="left">
                                    <span class="arrow">
                                        令牌验证码
                                    </span>
                            </th>
                            <th align="center">
                                    <span class="arrow">
                                        复制
                                    </span>
                            </th>
                            <th align="center"><span class="arrow">
                                        查看详情
                                    </span></th>
                            <th align="center"><span class="arrow">
                                        删除
                                    </span></th>
                        </tr>
                        </thead>
                        <tbody id="auth-tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>