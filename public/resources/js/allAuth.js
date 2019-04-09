function processbarload(e) {
    i = 0, i = 100 * e / 30, s = 0, showload = setInterval("processbarsetload()", delaytime)
}

function processbarsetload() {
    i += .1, s += .1, i >= 100 && clearInterval(showload), s >= 6 && (document.getElementById("rightajaxzhuangtai").innerHTML = ""), document.getElementById("authprogressbar").style.width = i + "%"
}

function createXHR() {
    window.ActiveXObject ? XHR = new ActiveXObject("Microsoft.XMLHTTP") : window.XMLHttpRequest && (XHR = new XMLHttpRequest)
}

function getauthjsondata() {
    isAuthCodeLoading = true, createXHR(), document.getElementById("rightajaxzhuangtai").innerHTML = "获取下一组令牌中&nbsp;<img class='getCodeStateImage' src='/resources/img/waiting.gif'/>", XHR.open("GET", geturladd, !0), XHR.onreadystatechange = jsonchuli, XHR.send(null)
}

function jsonchuli() {
    isAuthCodeLoading = false;
    clearInterval(showload);
    if (XHR.readyState === 4 && XHR.status === 200) {
        var textHTML = XHR.responseText, jsondata = eval("(" + textHTML + ")");
        if (jsondata.success) {
            document.getElementById("rightajaxzhuangtai").innerHTML = "获取成功&nbsp;<img class='getCodeStateImage' src='/resources/img/success.png'/>"
            timedelay = setTimeout("getauthjsondata()", (delaytime - jsondata.time) * 1e3)
            processbarload(jsondata.time)
            html = "";
            for (tempI = 0; tempI < jsondata.authList.length; tempI++) {
                html += getTableTrHtml(jsondata.authList[tempI]);
            }
            document.getElementById("auth-tbody").innerHTML += html;
            bindClickCopy(jsondata.authList);
            return;
        } else {
            processbarload(0)
            if (jsondata.message.length > 0) {
                document.getElementById("rightajaxzhuangtai").innerHTML = jsondata.message + "&nbsp;<img class='getCodeStateImage' src='/resources/img/warning2.png'/>"
            } else {
                document.getElementById("rightajaxzhuangtai").innerHTML = "获取失败,请重试&nbsp;<img class='getCodeStateImage' src='/resources/img/warning2.png'/>"
            }
            document.getElementById("auth-tbody").innerHTML = ""
        }
    } else {
        processbarload(0);
        document.getElementById("rightajaxzhuangtai").innerHTML = "获取失败,请重试&nbsp;<img class='getCodeStateImage' src='/resources/img/warning2.png'/>"
        document.getElementById("auth-tbody").innerHTML = ""
        return;
    }
}

function bindClickCopy(authList) {
    ZeroClipboard.setMoviePath("/resources/swf/ZeroClipboard.swf");
    for (tempI = 0; tempI < authList.length; tempI++) {
        authItem = authList[tempI];
        if (Clipboard.isSupported()) {
            clipboardArray[authItem.authId] = new Clipboard(document.getElementById('creation-submit-' + authItem.authId));
            clipboardArray[authItem.authId].on('success', function (e) {
                document.getElementById("copydatamode").innerHTML = "已复制到剪切板&nbsp;<img class='getCodeStateImage' src='/resources/img/success.png'/>", setTimeout("document.getElementById('copydatamode').innerHTML=''", 2e3)
            });
            clipboardArray[authItem.authId].on('error', function (e) {
                document.getElementById("copydatamode").innerHTML = "复制到剪切板失败&nbsp;<img class='getCodeStateImage' src='/resources/img/warning2.png'/>", setTimeout("document.getElementById('copydatamode').innerHTML=''", 2e3)
            });
            $("#creation-submit-" + authItem.authId).click(function () {
                errorText = document.getElementById("rightajaxzhuangtai").innerHTML;
                if (((errorText.indexOf("获取下一组令牌中") >= 0 && errorText.indexOf("/resources/img/waiting.gif") > 0)
                        || (errorText.indexOf("正在刷新令牌验证码") >= 0 && errorText.indexOf("/resources/img/waiting.gif") > 0)
                        ||
                        (
                            (errorText === "LOADNOW") && !(errorText.indexOf("获取失败,请重试") >= 0 && errorText.indexOf("/resources/img/warning2.png") > 0)
                        ))) {
                    document.getElementById("copydatamode").innerHTML = "请等待验证码刷新&nbsp;<img class='getCodeStateImage' src='/resources/img/warning2.png'/>", setTimeout("document.getElementById('copydatamode').innerHTML=''", 2e3)
                    return;
                }
                if (errorText.indexOf("获取失败,请重试") >= 0 && errorText.indexOf("/resources/img/warning2.png") > 0) {
                    document.getElementById("copydatamode").innerHTML = "请刷新验证码&nbsp;<img class='getCodeStateImage' src='/resources/img/warning2.png'/>", setTimeout("document.getElementById('copydatamode').innerHTML=''", 2e3)
                }
            });
            continue;
        }
        clipArray[authItem.authId] = new ZeroClipboard.Client,
            clipArray[authItem.authId].setHandCursor(!0), clipArray[authItem.authId].addEventListener("mouseOver", my_mouse_over),
            clipArray[authItem.authId].glue('creation-submit-' + authItem.authId), clipArray[authItem.authId].addEventListener("mouseUp", my_mouse_up)
        clipArray[authItem.authId].setText(authItem.authCode);
    }
}

function getTableTrHtml(authItem) {
    html = '<tr class="parent-row" id="tr_auth_' + authItem.authId + '">\n' +
        '<td class="authbianhao" valign="top">\n' +
        '    <img class="tdimgauth"\n' +
        '         src="' + authItem.authImage + '" alt="">' + authItem.authId + '\n' +
        '</td>\n' +
        '<td class="authmincheng" valign="top">\n' +
        '            <span>\n';
    if (authItem.isDefault) {
        html += '  <img class=\'morenauthleftpic\' src=\'/resources/img/moren.png\'/>\n';
    }
    html += '</span>\n' +
        '    <span>' + authItem.authName + '</span>\n' +
        '</td>\n' +
        '<td class="authcode" id="auth_code_' + authItem.authId + '" valign="top">\n' +
        '            <span>\n' + authItem.authCode +
        '            </span>\n' +
        '</td>\n' +
        '<td class="align-center" valign="top">\n' +
        '            <span>\n' +
        ' <button class="ui-button button1" id="creation-submit-' + authItem.authId + '" tabindex="1"\n' +
        '         data-clipboard-text="' + authItem.authCode + '">\n' +
        '    <span class="button-left"><span class="button-right">复制验证码</span></span>\n' +
        '</button>\n' +
        '            </span>\n' +
        '</td>\n' +
        '<td valign="top" class="align-center">\n' +
        '    <button class="ui-button button1" onclick="window.open(\'/auth?authId=' + authItem.authId + '\')">\n' +
        '        <span class="button-left">\n' +
        '            <span\n' +
        '      class="button-right">查看安全令</span>\n' +
        '        </span>\n' +
        '    </button>\n' +
        '</td>\n' +
        '<td valign="top" class="align-center">\n' +
        '    <button class="ui-button button1"\n' +
        '            onclick="if(confirm(\'该操作将删除这枚安全令，确定吗？\'))\n' +
        '      location.href=\'/deleteAuth?authId='+authItem.authId+'\';else return false;">\n' +
        '            <span class="button-left">\n' +
        '  <span\n' +
        '          class="button-right">确认删除</span>\n' +
        '            </span>\n' +
        '    </button>\n' +
        '</td>\n' +
        '          </tr>';
    return html;
}


function refreshcodegeas() {
    if (isAuthCodeLoading) {
        return;
    }
    isAuthCodeLoading = true;
    clearInterval(showload), clearTimeout(timedelay), createXHR(), document.getElementById("rightajaxzhuangtai").innerHTML = "正在刷新令牌验证码&nbsp;<img class='getCodeStateImage' src='/resources/img/waiting.gif'/>", XHR.open("GET", geturladd, !0), XHR.onreadystatechange = jsonchuli, XHR.send(null)
}

function my_mouse_over(e) {
    errorText = document.getElementById("rightajaxzhuangtai").innerHTML;
    if ((errorText.indexOf("获取失败,请重试") >= 0 && errorText.indexOf("/resources/img/warning2.png") > 0)
        || (errorText.indexOf("获取下一组令牌中") >= 0 && errorText.indexOf("/resources/img/waiting.gif") > 0)
        || (errorText.indexOf("正在刷新令牌验证码") >= 0 && errorText.indexOf("/resources/img/waiting.gif") > 0)
        || (errorText === "LOADNOW")) {
        return;
    }
    if (document.getElementById("authcode").innerHTML.length > 0) {
        clip.setText(document.getElementById("authcode").innerHTML)
    }
}

function my_mouse_up(e) {
    errorText = document.getElementById("rightajaxzhuangtai").innerHTML;
    if (((errorText.indexOf("获取下一组令牌中") >= 0 && errorText.indexOf("/resources/img/waiting.gif") > 0)
            || (errorText.indexOf("正在刷新令牌验证码") >= 0 && errorText.indexOf("/resources/img/waiting.gif") > 0)
            ||
            (
                (errorText === "LOADNOW") && !(errorText.indexOf("获取失败,请重试") >= 0 && errorText.indexOf("/resources/img/warning2.png") > 0)
            ))) {
        document.getElementById("copydatamode").innerHTML = "请等待验证码刷新&nbsp;<img class='getCodeStateImage' src='/resources/img/warning2.png'/>", setTimeout("document.getElementById('copydatamode').innerHTML=''", 2e3)
        return;
    }
    if (errorText.indexOf("获取失败,请重试") >= 0 && errorText.indexOf("/resources/img/warning2.png") > 0) {
        document.getElementById("copydatamode").innerHTML = "请刷新验证码&nbsp;<img class='getCodeStateImage' src='/resources/img/warning2.png'/>", setTimeout("document.getElementById('copydatamode').innerHTML=''", 2e3)
        return;
    }
    if (document.getElementById("authcode").innerHTML.length > 0) {
        document.getElementById("copydatamode").innerHTML = "已复制到剪切板&nbsp;<img class='getCodeStateImage' src='/resources/img/success.png'/>", setTimeout("document.getElementById('copydatamode').innerHTML=''", 2e3)
    }
}

var i = 0, s = 0, delaytime = 30.5, timedelay, XHR, isAuthCodeLoading;
var clipArray = [], clipboardArray = [];
$(window).load(function () {
    showload = setInterval("processbarsetload()", 1e7), getauthjsondata()
});