<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2012) NetWebX All Right Reserved.
 * @filesource: Viewport.php,v$
 * @package:Class
 *
 * @author WengJunFeng <jason@netwebx.com>
 * @version $Id: v 1.0 2012-06-06 Jason Exp $
 *
 * @abstract:
 */

class Viewport
{
    public static $WAP2 = 1;
    public static $WAP1 = 2;
    public static $HTML5 = 3;
    public static $HTML = 4;
        
	private $_view_root_path = NULL;
	
	public function __construct()
	{
	}
	
	public function setViewport($view)
	{
		switch($view)
		{
			case self::$HTML5 :
				$this->_view_root_path = APP_VIEW_PATH . DS . "Html5";
				break;
			case self::$WAP1 :
				$this->_view_root_path = APP_VIEW_PATH . DS . "Wap1.0";
				break;
			case self::$WAP2 :
				$this->_view_root_path = APP_VIEW_PATH . DS . "Wap2.0";
				break;
			default:
				$this->_view_root_path = APP_VIEW_PATH . DS . "Html";
				break;
		}
	}
	
	public function loadView($body, $header = NULL, $footer = NULL, $include_header = TRUE, $include_footer = TRUE)
	{
		//include header file
		if( $include_header )
		{
			$default_header = $this->_view_root_path . DS . "Common" . DS . "header.inc.php";
			$header_file = $default_header;
			if( !is_null($header) )
			{
				$view_head_file = $this->_view_root_path . DS . $header;
				if( file_exists($view_head_file) )
				{
					$header_file = $view_head_file;
				}
			}
			include_once $header_file;
			unset($header_file);
			unset($default_header);
			unset($view_head_file);
		}
		
		//include body file
		$view_body_file = $this->_view_root_path . DS . $body;
		$default_body = $this->_view_root_path . DS . "Common" . DS . "body.inc.php";
		$body_file = $default_body;
		if( file_exists($view_body_file) )
		{
			$body_file = $view_body_file;
		}
		include_once $body_file;
		unset($body_file);
		unset($default_body);
		unset($view_body_file);
		
		//include footer file
		if( $include_footer )
		{
			$default_footer = $this->_view_root_path . DS . "Common" . DS . "footer.inc.php";
			$footer_file = $default_footer;
			if( !is_null($footer) )
			{
				$view_foot_file = $this->_view_root_path . DS .$footer;
				if( file_exists($view_foot_file) )
				{
					$footer_file = $view_foot_file;
				}
			}
			include_once $footer_file;
			unset($footer_file);
			unset($default_footer);
			unset($view_foot_file);
		}
		
		return TRUE;
	}
}