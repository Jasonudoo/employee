<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2012) NetWebX All Right Reserved.
 * @filesource: Global.inc.php,v$
 * @package:Config
 *
 * @author WengJunFeng <jason@netwebx.com>
 * @version $Id: v 1.0 2012-06-06 Jason Exp $
 *
 * @abstract:
 */

define("APP_ROOT", dirname(dirname(dirname(__FILE__))) );
define("DS", DIRECTORY_SEPARATOR);
define("APP_ADMIN_PATH", APP_ROOT . DS . "admin");
define("APP_CONFIG_PATH", APP_ADMIN_PATH . DS . "Config");
define("APP_CLASS_PATH", APP_ADMIN_PATH . DS . "Class");
define("APP_LIB_PATH", APP_ADMIN_PATH . DS ."Library");
define("APP_VIEW_PATH", APP_ADMIN_PATH . DS . "View");
define("APP_ACTION_PATH", APP_ADMIN_PATH .DS . "Action");
define("APP_LOG_PATH", APP_ADMIN_PATH . DS . "Logs");

define("APP_STYLE_PATH", APP_ROOT . DS . "css");
define("APP_IMAGE_PATH", APP_ROOT . DS . "images");
define("APP_JS_PATH", APP_ROOT . DS . "js");

define("WEB_BASE_PATH", "" );
if( isset($_SERVER['HTTP_HOST']) )
{
	define("WEB_ROOT_URL", $_SERVER['HTTP_HOST'] . DS . WEB_BASE_PATH);
}
define("WEB_IMAGE_URL", WEB_ROOT_URL . "images" . DS);
define("WEB_STYLE_URL", WEB_ROOT_URL . "css" . DS);
define("WEB_JS_URL", WEB_ROOT_URL . "js" . DS);
define("WEB_EMPLOYEE_PHOTO_URL", WEB_ROOT_URL . "uploads" . DS ."photo" . DS);
define("WEB_EMPLOYEE_PHOTO_PATH", APP_ROOT . "uploads" . DS . "photo" . DS);
define("WEB_EMPLOYEE_FILE_PATH", APP_ROOT . "uploads" . DS . "files" . DS);

require_once APP_CONFIG_PATH . DS . "Config.inc.php";
require_once APP_ADMIN_PATH . DS . "Function" . DS . "common.inc.php";
require_once APP_CLASS_PATH . DS . "Application.php";
Application::autoload();