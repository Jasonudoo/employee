<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2012) ImageCO All Right Reserved.
 * @filesource: Config.inc.php,v$
 * @package:Config
 *
 * @author WengJunFeng <wengjf@imageco.com>
 * @version $Id: v 1.0 2012-06-06 Jason Exp $
 *
 * @abstract:
 */
//===================数据库连接信息=======================//
//数据库主机名
define("DB_HOST", "localhost");
//数据库名
define("DB_NAME", "imgshopdb");
//数据库连接用户名
define("DB_USER", "usr_imgshop");
//数据库连接密码
define("DB_PASS", "imageco123");
//数据库字符集
define("DB_CHARSET", "utf8");

//===================通用配置信息======================//
//COOKIE失效时间，以秒为单位
define('COOKIE_EXPIRED', 86400);
//页面标题格式
define("APP_PAGE_TITLE", "%s -- 我的街奴");
//页面每页显示笔数
define("APP_PAGESIZE", 5);
//页面显示的日期格式
define("APP_DATE_FORMAT", "Y-m-d");
//页面显示的时间格式
define("APP_DATETIME_FORMAT", "Y-m-d H:i:s");
//图片字体设定
define("APP_FONT", "/usr/share/fonts/zh_CN/TrueType/gbsn00lp.ttf");
//网站网址
define("WEB_URL", "http://t.jienu.com");
//网络名称
define("WEB_NAME", "街奴");
//加密密钥
define("APP_PASSWORD_SALT", "92383kfal23312llop233");


//====================接口配置信息===========================//
//街路接口URL
define("WEB_INTERFACE_JIENU", "http://222.44.51.34/jienu/serv/client_serv/serv.php");
//支撑平台2接口URL
define("WEB_INTERFACE_ZCPT", "https://222.44.51.34:18443/iss2.do");
//支持平台2接口使用的密钥
define("WEB_INTERFACE_MACKEY", "pmgw");
//支持平台2接口的平台号
define("WEB_INTERFACE_PLATFORM", "7260");

//====================支付宝基本配置信息=======================//

//支付宝报文加密方式
define("APP_ALIPAY_METHOD", "MD5");
//define("APP_ALIPAY_METHOD", "RSA");
//define("APP_RSA_KEY_PATH", "/var/tmp/key");
define("ALIPAY_DEFAULT_RATE", "0.012");

$Alipay_Partner		= "2088601005854283";					//合作身份者ID，以2088开头的16位纯数字
$Alipay_Key			= "lux4lbedt1zcci4d38k9zyuo1k68gffd";	//安全检验码，以数字和字母组成的32位字符
$Alipay_Seller_Email	= "alipay@imageco.com.cn";				//签约支付宝账号或卖家支付宝帐户

//$Alipay_Partner			= "2088201564809153";					//合作身份者ID，以2088开头的16位纯数字
//$Alipay_Key				= "zpdjh9ywq433ejjnkrbc5pys7ipkosnz";	//安全检验码，以数字和字母组成的32位字符
//$Alipay_Seller_Email	= "alipay-test12@alipay.com";				//签约支付宝账号或卖家支付宝帐户

//$Alipay_Notify_Url	= "http://211.137.201.230:8080/imgshop/Front/notify.php";	//异步返回消息通知页面，用于告知商户订单状态
//$Alipay_Callback_Url	= "http://211.137.201.230:8080/imgshop/Front/return.php";	//同步返回消息通知页面，用于提示商户订单状态
$Alipay_Notify_Url		= "http://116.228.158.210:8084/imgshop/Front/notify.php";	//异步返回消息通知页面，用于告知商户订单状态
$Alipay_Callback_Url	= "http://116.228.158.210:8084/imgshop/Front/return.php";	//同步返回消息通知页面，用于提示商户订单状态
$Alipay_Pay_Expire			= 480;								//交易自动关闭时间,单位:分钟
//以下内容不需要修改,固定参数
$Alipay_Service_Paychannel 	= "mobile.merchant.paychannel";
$Alipay_Service1			= "alipay.wap.trade.create.direct";	//接口1
$Alipay_Service2			= "alipay.wap.auth.authAndExecute";	//接口2
$Alipay_Format				= "xml";							//http传输格式
$Alipay_Security			= "MD5";							//签名方式 不需修改
$Alipay_input_charset		= "utf-8";							//字符编码格式
$Alipay_input_charset_GBK 	= "GBK";
$Alipay_version				= "2.0";							//版本号
