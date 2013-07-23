<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2012) ImageCO All Right Reserved.
 * @filesource: Weblog.php,v$
 * @package:Class
 *
 * @author WengJunFeng <wengjf@imageco.com>
 * @version $Id: v 1.0 2012-05-30 Jason Exp $
 *
 * @abstract:
 */

class Weblog
{
	private static $_log_path = APP_LOG_PATH;
	private static $_log_file_subfix = NULL;
	private static $_log_file = NULL;
	private static $_log_file_handle = array();
	private static $_log_file_name = array("system", "voucher", "alipay", "jienu", "error");
	
	public static $WEBLOG_SYSTEM = 0;
	public static $WEBLOG_VOUCHER = 1;
	public static $WEBLOG_ALIPAY = 2;
	public static $WEBLOG_JIENU = 3;
	public static $WEBLOG_ERROR = 4;
	
    /**
     * Singleton pattern implementation makes "new" unavailable
     *
     * @return void
     */
    private function __construct(){}

    /**
     * Singleton pattern implementation makes "clone" unavailable
     *
     * @return void
     */
    private function __clone(){}
    
    private function __destruct()
    {
    	self::_close();
    }
    	
	static private function _getLogFileName($fname)
	{
		self::$_log_file_subfix = date("Ymd");
		if( isset(self::$_log_file_name[$fname]) )
		{
			self::$_log_file = self::$_log_path . DS . self::$_log_file_name[$fname] . "_" . self::$_log_file_subfix . ".log";
			return TRUE;
		}
		return FALSE;
	}
	
	//TODO : add the try catch throw error logical
	static public function write($file = 0, $message)
	{
		if( self::_getLogFileName($file) )
		{
			self::_open($file);
			
			$log_message = "[".date('Y-m-d H:i:s'). "] " . $message . "\n";
							
			@fwrite(self::$_log_file_handle[$file], $log_message);
			//self::_close();
			return TRUE;
		}
		return FALSE;
	}
	
	static public function read($file = 0)
	{
		if( self::_getLogFileName($file) )
		{
			self::_open($file, TRUE);
			//TODO: Write the read code
			//self::_close();
		}
	}
	
	static private function _open($file, $readOnly = FALSE)
	{
		$openMode = "a+";
		if( !isset(self::$_log_file_handle[$file]) || is_null(self::$_log_file_handle[$file]) )
		{
			if( $readOnly )
			{
				$openMode = "r";
			}
			self::$_log_file_handle[$file] = @fopen(self::$_log_file, $openMode);
		}
		return self::$_log_file_handle[$file];
	}
	
	static private function _close()
	{
		foreach(self::$_log_file_handle as $key => $handle)
		{
			if( is_null($handle) )
			{
				continue;
			}
			fclose($handle);
		}
		return TRUE;
	}
}