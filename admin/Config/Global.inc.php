<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2012) ImageCO All Right Reserved.
 * @filesource: Global.inc.php,v$
 * @package:Config
 *
 * @author WengJunFeng <wengjf@imageco.com>
 * @version $Id: v 1.0 2012-06-06 Jason Exp $
 *
 * @abstract:
 */

define("APP_ROOT", dirname(dirname(__FILE__)) );
define("DS", DIRECTORY_SEPARATOR);
define("APP_CONFIG_PATH", APP_ROOT . DS . "Config");
define("APP_CLASS_PATH", APP_ROOT . DS . "Class");
define("APP_LIB_PATH", APP_ROOT . DS ."Libs");
define("APP_VIEW_PATH", APP_ROOT . DS . "View");
define("APP_ACTION_PATH", APP_ROOT .DS . "Action");
define("APP_LOG_PATH", APP_ROOT . DS . "Logs");

define("APP_STYLE_PATH", APP_ROOT . DS ."Static" . DS ."Style");
define("APP_IMAGE_PATH", APP_ROOT . DS ."Static" . DS ."Images");
define("APP_JS_PATH", APP_ROOT .DS . "Static" . DS . "Js");

define("WEB_ROOT_PATH", DS . "www");
define("WEB_BASE_PATH", "imgshop" . DS . "Front". DS );
if( isset($_SERVER['HTTP_HOST']) )
{
	define("WEB_ROOT_URL", $_SERVER['HTTP_HOST'] . DS . WEB_BASE_PATH);
}
define("WEB_IMAGE_URL", DS . WEB_BASE_PATH . "Static" . DS . "Images" . DS);
define("WEB_STYLE_URL", DS . WEB_BASE_PATH . "Static" . DS . "Style" . DS);
define("WEB_JS_URL", DS . WEB_BASE_PATH . "Static" . DS . "Js" . DS);
define("WEB_GOODS_IMAGE_URL", DS . "imgshop" .DS . "Shopping" . DS . "Upload" . DS ."Goods" . DS);
define("WEB_GOODS_IMAGE_PATH", WEB_ROOT_PATH . WEB_GOODS_IMAGE_URL);

require_once APP_CONFIG_PATH . DS . "Config.inc.php";
require_once APP_CLASS_PATH . DS . "Application.php";
Application::autoload();