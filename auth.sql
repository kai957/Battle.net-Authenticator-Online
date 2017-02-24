/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`auth` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `auth`;

/*Table structure for table `authdata` */

CREATE TABLE `authdata` (
  `auth_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `auth_moren` int(1) NOT NULL,
  `auth_name` varchar(80) CHARACTER SET utf8 NOT NULL COMMENT '安全令备注',
  `serial` varchar(20) CHARACTER SET utf8 NOT NULL,
  `region` varchar(10) CHARACTER SET utf8 NOT NULL,
  `secret` varchar(60) CHARACTER SET utf8 NOT NULL,
  `restore_code` varchar(20) NOT NULL,
  `auth_img` int(1) NOT NULL,
  PRIMARY KEY (`auth_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9143 DEFAULT CHARSET=latin1;

/*Table structure for table `cookiedata` */

CREATE TABLE `cookiedata` (
  `cookie_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(60) CHARACTER SET utf8 NOT NULL,
  `user_cookie` varchar(40) CHARACTER SET utf8 NOT NULL,
  `login_time` datetime NOT NULL,
  `user_login_ip` varchar(192) NOT NULL,
  PRIMARY KEY (`cookie_id`),
  KEY `cookie_id` (`cookie_id`)
) ENGINE=MyISAM AUTO_INCREMENT=41670 DEFAULT CHARSET=latin1;

/*Table structure for table `deleted_auth_data` */

CREATE TABLE `deleted_auth_data` (
  `auth_id` bigint(20) NOT NULL DEFAULT '0',
  `user_id` bigint(20) NOT NULL,
  `auth_moren` int(1) NOT NULL,
  `auth_name` varchar(80) NOT NULL COMMENT '安全令备注',
  `serial` varchar(20) NOT NULL,
  `region` varchar(10) NOT NULL,
  `secret` varchar(60) NOT NULL,
  `restore_code` varchar(20) CHARACTER SET latin1 NOT NULL,
  `auth_img` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `donate_data` */

CREATE TABLE `donate_data` (
  `donate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `donate_name` varchar(256) CHARACTER SET utf8 NOT NULL DEFAULT '匿名土豪',
  `donate_time` int(11) unsigned NOT NULL,
  `donate_bizhong` char(64) CHARACTER SET utf8 NOT NULL DEFAULT '软妹币' COMMENT '币种',
  `donate_count` float unsigned NOT NULL COMMENT '数额',
  PRIMARY KEY (`donate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

/*Table structure for table `synctime` */

CREATE TABLE `synctime` (
  `region` char(4) NOT NULL,
  `sync` bigint(20) NOT NULL,
  `last_sync` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `users` */

CREATE TABLE `users` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `user_name` varchar(60) CHARACTER SET utf8 NOT NULL COMMENT '用户名',
  `user_pass` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '用户密码',
  `user_right` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '999为封禁用户',
  `user_email` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '用户邮箱',
  `user_email_checked` int(1) NOT NULL,
  `user_registered` datetime NOT NULL COMMENT '用户注册时间',
  `user_question` bigint(20) NOT NULL,
  `user_answer` varchar(40) CHARACTER SET utf8 NOT NULL,
  `user_email_checkid` varchar(60) CHARACTER SET utf8 NOT NULL,
  `user_email_find_code` varchar(60) CHARACTER SET utf8 NOT NULL,
  `user_email_find_mode` int(1) NOT NULL,
  `user_psd_reset_token` varchar(80) NOT NULL,
  `user_psd_reset_token_used` int(1) NOT NULL,
  `user_lastlogin_ip` varchar(192) NOT NULL,
  `user_thistimelogin_ip` varchar(192) NOT NULL,
  `user_lastlogin_time` datetime NOT NULL,
  `user_thislogin_time` datetime NOT NULL,
  `lastused_session_time` int(11) unsigned NOT NULL DEFAULT '0',
  `user_donated` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6398 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
