function createXHR() {
    window.ActiveXObject ? XHR = new ActiveXObject("Microsoft.XMLHTTP") : window.XMLHttpRequest && (XHR = new XMLHttpRequest)
}
function checkyanzhenma(e) {
    e != "" && e != null ? (createXHR(), document.getElementById("checkyanzhenmaajax").innerHTML = "<img src='/resources/img/waiting.gif' alt=''>", XHR.open("GET", "/api/check/checkCaptcha?code=" + e, !0), XHR.onreadystatechange = bbcheck, XHR.send(null)) : document.getElementById("checkyanzhenmaajax").innerHTML = ""
}
function bbcheck() {
    if (XHR.readyState == 4 && XHR.status == 200) {
        var e = XHR.responseText;
        e == "true" ? document.getElementById("checkyanzhenmaajax").innerHTML = "<img src='/resources/img/success.png' alt=''>" : e == "false" ? document.getElementById("checkyanzhenmaajax").innerHTML = "<img src='/resources/img/warning2.png' alt=''>" : document.getElementById("checkyanzhenmaajax").innerHTML = ""
    }
}
var XHR