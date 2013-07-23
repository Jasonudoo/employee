<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2012) NetWebX All Right Reserved.
 * @filesource: Weblog.php,v$
 * @package:Class
 *
 * @author WengJunFeng <jason@netwebx.com>
 * @version $Id: v 1.0 2012-05-30 Jason Exp $
 *
 * @abstract:
 */

class Weblog
{
    /**
     * The log file path
     * @var string
     * @access private
     */
	private static $_log_path = APP_LOG_PATH;
	
	/**
	 * The log file subfix
	 * @var string
	 * @access private
	 */
	private static $_log_file_subfix = NULL;
	
	/**
	 * The log file full name included the path
	 * @var string
	 * @access private
	 */
	private static $_log_file = NULL;
	
	/**
	 * The log file handle
	 * @var object or null
	 */
	private static $_log_file_handle = array();
	
	/**
	 * The log file type
	 * @var array
	 */
	private static $_log_file_type = array("system", "error");
	
	/**
	 * The log file type index for system
	 * @var integer
	 */
	public static $WEBLOG_SYSTEM = 0;
	
	/**
	 * The log file type index for error
	 * @var integer
	 */
	public static $WEBLOG_ERROR = 1;
	
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
    
    /**
     * Get the log file full name
     * The file name is combined by the log file path, log file type and log file subfix
     *
     * @param $ftype integer the log file type
     * @return TRUE or FALSE
     */
	static private function _getLogFileName($ftype)
	{
		self::$_log_file_subfix = date("Ymd");
		if( isset(self::$_log_file_type[$ftype]) )
		{
			self::$_log_file = self::$_log_path . DS . self::$_log_file_type[$ftype] . "_" . self::$_log_file_subfix . ".log";
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
	
	/**
	 * Read the log file
	 *
	 * @param integer $file log file type
	 */
	static public function read($file = 0)
	{
		if( self::_getLogFileName($file) )
		{
			self::_open($file, TRUE);
			//TODO: Write the read code
			//self::_close();
		}
	}
	
	/**
	 * Open the log file
	 * @param integer $file log file type
	 * @param boolean $readOnly
	 * @return object
	 */
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
	
	/**
	 * Close the log file
	 * @return boolean
	 */
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