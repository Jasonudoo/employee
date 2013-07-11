<?php
/**
 * @copyright Copyright(2013) All Right Reserved.
 * @filesource: config.inc.php,v$
 * @package: include
 *
 * @author Jason Williams <jasonudoo@gmail.com>
 * @version $Id: v 1.0 2013-06-08 Jason Exp $
 *
 * @abstract:
 */

if( !defined('PROJECT_START') )
{
	define('PROJECT_START', TRUE);
}

define('APP_ROOT_PATH', dirname(dirname(__FILE__)) );
define('DS', DIRECTORY_SEPARATOR);

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'www_employeedb');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'wT$@_156');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

if(PHP_VERSION < '4.1.0'){
	$_GET = &$HTTP_GET_VARS;
	$_POST = &$HTTP_POST_VARS;
	$_COOKIE = &$HTTP_COOKIE_VARS;
	$_SERVER = &$HTTP_SERVER_VARS;
	$_ENV = &$HTTP_ENV_VARS;
	$_FILES = &$HTTP_POST_FILES;
}

?>