<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2012) NetWebX All Right Reserved.
 * @filesource: Config.inc.php,v$
 * @package:Config
 *
 * @author WengJunFeng <jason@netwebx.com>
 * @version $Id: v 1.0 2012-06-06 Jason Exp $
 *
 * @abstract:
 */
//===================Database Connection Parameters=======================//
//DB Host
define("DB_HOST", "localhost");
//DB Name
define("DB_NAME", "www_employee");
//DB User Name
define("DB_USER", "usr_employee");
//DB Password
define("DB_PASS", "a6evVypDA){ohZL]");
//DB Charset
define("DB_CHARSET", "utf8");

//===================Default System Parameters======================//
//COOKIE Expired Timeout
define('COOKIE_EXPIRED', 86400);
//SESSION Expired Timeout
define('SESSION_EXPIRED', 86400);
//SESSION LOCK Expired
define('SESSION_LOCK_EXPIRED', 86400 * 2);
//Page Title Format
define("APP_PAGE_TITLE", "%s -- Website Name");
//Search Result Pagesize
define("APP_PAGESIZE", 5);
//Date Format
define("APP_DATE_FORMAT", "Y-m-d");
//Datetime Format
define("APP_DATETIME_FORMAT", "Y-m-d H:i:s");
//The password seed
define("APP_PASSWORD_SALT", "92383kfal23312llop233");
//Website Url
define("WEB_URL", "http://dev.netwebx.com");
