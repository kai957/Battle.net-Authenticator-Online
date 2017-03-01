# Myauth.us
Battle.net Authenticator Online/战网安全令在线版

基于<a href="https://github.com/krtek4/php-bma">php-bma</a>开发 / Based on <a href="https://github.com/krtek4/php-bma">php-bma</a>

本玩具现已支持战网一键登录功能，并重新开源，Dva爱你哦，起飞！

## 安装步骤
	①修改/includes/config.php，各项注释都已写明
	②建立auth数据库，导入auth.sql数据库结构
	③注意根据不同的服务器程序使用不同的htaccess文件，Apache和nginx都分别给出了，复制后使用，nginx需要在网站server配置文件中include<br>
	④修改代码中的信息，生成并导入自己的key文件用于非对称校验，随便改，但是记得遵照协议，自己建好站了请别把我的名字都删了
	⑤注意在php7.0下，需要安装php7.0-gd，否则验证码将无法生成

## 项目信息
<a href="https://myauth.us/">https://myauth.us/</a> 是基于本项目的作者自建站<br>
The site <a href="https://myauth.us/">https://myauth.us/</a> based on this project.<br>
如欲捐赠，请参访 <a href="https://myauth.us/donate.php">https://myauth.us/donate.php</a> 页面 

