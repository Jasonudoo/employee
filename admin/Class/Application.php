<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2012) ImageCO All Right Reserved.
 * @filesource: Application.php,v$
 * @package:Class
 *
 * @author WengJunFeng <wengjf@imageco.com>
 * @version $Id: v 1.0 2012-06-06 Jason Exp $
 *
 * @abstract: The base class for all page action.
 */

class Application
{
	/*
	 * 视图类的实例名
	 * @access private
	 * @var object
	 */
	private $_view = NULL;

	/*
	 * 支持的WAP视图
	 * @access private
	 * @var Array
	 */
	private $_support_wapview = NULL;
	
	/*
	 * 缺省的WAP视图
	 * @access private
	 * @var int 
	 */
	private $_default_wapview = NULL;
	
	/*
	 * 页面的标题
	 * @access private
	 * @var string
	 */
	private $_title = NULL;
	
	/*
	 * SESSION ID
	 * WAP网站的SESSION ID
	 * @access private
	 * @var string 
	 */
	private $_sid = NULL;

	/*
	 * Token ID
	 * 街路登录回传过来的session id
	 * @access private
	 * @var string
	 */
	private $_token = NULL;
	
	/*
	 * 登录状态
	 * @access private
	 * @var bool
	 */
	private $_login = FALSE;
	
	/*
	 * 页面提交或传递的值,即_POST和_GET值
	 * @access private
	 * @var mixed 
	 */
	private $_VARS = array();
	
	/*
	 * 页面错误显示信息
	 * @access private
	 * @var Array
	 */
	private $_ERRORS = array();
	
	/*
	 * 页面成功显示信息
	 * @access private
	 * @var Array
	 */
	private $_SUCCESS = NULL;
	
	/*
	 * Construct Function
	 * 初始化缺省Wap视图
	 * 过滤输入和提交的参数
	 * session初始化
	 * 检查登录状态
	 * 页面过程初始化
	 */
	public function __construct()
	{
		//$this->_default_wapview = Wap::$HTML;
		$this->_default_wapview = Wap::$WAP2;
		$this->_support_wapview = array(Wap::$HTML, Wap::$HTML5, Wap::$WAP1, Wap::$WAP2);
		
		$this->_filter();
		$this->_session_init();
		$this->_checkLoginStatus();
		$this->_before_init();
		$this->_init();
		$this->_after_init();
	}
    
	static public function autoload()
    {
		spl_autoload_register(array("Application", "loadClass"));	
    }
    
    static public function loadClass($class)
    {
    	if( class_exists($class) )
    	{
    		return TRUE;
    	}
    	
    	$class_path = array(APP_CLASS_PATH);
    	$class_name = ucfirst(strtolower($class));
    	
    	$findMe = FALSE;
    	for($i = 0; $i < sizeof($class_path); $i++)
    	{
    		$class_filename = $class_path[$i] . DS . $class_name . ".php"; 
    		if( file_exists($class_filename) )
    		{
    			include_once($class_filename);
    			$findMe = TRUE;
    			break;		
    		}
    	}
    	return $findMe;
    }
	
	public function getView()
	{
		if( is_null($this->_view) )
		{
			$this->_view = new Viewport();
		}
		return $this->_view;
	}
	
	public function getDefaultWapView()
	{
		return $this->_default_wapview;
	}
	
	public function setDefaultWapView($view)
	{
		if( in_array($view, $this->_support_wapview) )
		{
			$this->_default_wapview = $view;
			return TRUE;
		}
		return FALSE;
	}
	
	public function setVar($key, $value)
	{
		$this->_VARS[$key] = urldecode($value);
	}
	
	public function getVar($key)
	{
		if( isset($this->_VARS[$key]) )
		{
			return $this->_VARS[$key];
		}
		return NULL;
	}
	
	public function getPageTitle()
	{
		return sprintf(APP_PAGE_TITLE, htmlspecialchars($this->_title) );
	}
	
	public function setPageTitle($title)
	{
		$this->_title = $title;
	}
	
	public function getCurrentUrl($https = FALSE)
	{
		if( $https )
		{
			return "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		else
		{
			return "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
	}
	
	public function output($value)
	{
		return $this->_arrayToObject($value);
	}
	
	public function getSessionID()
	{
		return $this->_sid;
	}
	
	public function getLoginStatus()
	{
		return $this->_login;
	}
	
	public function setLoginStatus($status)
	{
		$this->_login = $status;
	}
	
	public function getUrl()
	{
		$url = "";
		$port = "";
		if( trim($_SERVER['SERVER_PORT']) !== "80" )
		{
			$port = ":" . trim($_SERVER['SERVER_PORT']);
		}
		$url = "http://" . $_SERVER['SERVER_NAME'] . $port .$_SERVER['REQUEST_URI'];
		return $url;
	}
	
	public function setError($value)
	{
		$this->_ERRORS[] = $value;
	}
	
	public function getError()
	{
		return $this->_ERRORS;
	}
	
	public function clearError()
	{
		unset($this->_ERRORS);
		$this->_ERRORS = array();
	}
	
	public function setSuccess($value)
	{
		$this->_SUCCESS = $value;
	}
	
	public function getSuccess()
	{
		return $this->_SUCCESS;
	}
	
	public function is_phone($phone)
	{
		if( !preg_match('/^(13[0-9]|15[0-9]|18[0-9]|147)\d{8}$/', $phone))
		{
			return FALSE;
		}
		return TRUE;
	}
	
	public function getFromUrl()
	{
		return urlencode($_SERVER['PHP_SELF'].$_SERVER['REQUEST_URI']);
	}
	
	public function redirect($url)
	{
		header("Location:" . $url);
	}
	
	public function formatDate($date, $format)
	{
		$time = strtotime($date);
		if( !$time || $time < 0 )
		{
			return FALSE;
		}
		return date($format, $time);
	}
	
	/*
	 * The function overwritten by child
	 */
	protected function _after_init(){}
	
	/*
	 * The function overwritten by child
	 */
	protected function _before_init(){}
	
	protected function _session_init($id = NULL)
	{
		session_name("IMGCOLOGIN");
		//if( !isset($_SESSION['sid']) )
		//{
			session_start();
			$this->_login = FALSE;
		//}
		$this->_sid = session_id();
		return $this->_sid;
	}
	
	protected function _isPostCallback()
	{
		if(isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
		{
			return TRUE;
		}
		return FALSE;
    }
	
	private function _filter($str = NULL, $flag = TRUE)
	{
		$in = array($_GET, $_POST);
		for($i = 0; $i < sizeof($in); $i++)
		{
			$var = $in[$i];
			foreach($var as $key => $value)
			{
				$key = strtolower($key);
				$value = $this->_renderslashes($value);
				$this->setVar($key, $value);
			}
		}
	}
	
	private function _renderslashes($str, $force = FALSE, $rend = TRUE)
	{
		!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());

		if (!MAGIC_QUOTES_GPC || $force) 
		{
			if ( is_array($str) ) 
			{
				foreach ($str as $key=>$val) 
				{
					$str[$key] = $this->_renderslashes($val, $force, $rend);
				}
			}
			else 
			{
				if( $rend ) $str = addslashes($str);
				else $str = stripslashes($str);
			}
		}
		return $str;		
	}
	
	private function _init()
	{
		$wapview = $this->getDefaultWapView();
		if( Wap::checkUserAgent() )
		{
			$wapview = Wap::isSupportWap();
		}
		$this->getView()->setViewport($wapview);
	}
	
	protected function compareTimeToNow($time)
	{
		$compare = $this->_getTimestamp($time);
		if( $compare > time())
		{
			return 1;
		}
		return 0;
	}
	
	protected function compareTime($time1, $time2)
	{
		$time1 = $this->_getTimestamp($time1);
		$time2 = $this->_getTimestamp($time2);

		if( $time1 > $time2 )
		{
			return 1;
		}
		
		return 0;
	}
	
	private function _getTimestamp($time)
	{
		$year = substr($time, 0, 4);
		$month = substr($time, 4, 2);
		$day = substr($time, 6, 2);
		$hour = substr($time, 8, 2);
		$minute = substr($time, 10, 2);
		$second = substr($time, 12, 2);
		
		$time = mktime($hour, $minute, $second, $month, $day, $year);
		
		return $time;
	}
	
	private function _arrayToObject($arr)
	{
		if( !is_array($arr) )
		{
			return $arr;
		}
		if( is_array($arr) && count($arr) > 0 )
		{
			foreach($arr as $key => $value)
			{
				$this->$key = $value;
			}
		}
		return $this;
	}
	
	private function _checkLoginStatus()
	{
		if( isset($_COOKIE['sid']) && isset($_SESSION['sid']) && $_COOKIE['sid'] = $_SESSION['sid'])
		{
			$this->_login = TRUE;
			return;
		}
		//print_r($_SESSION);
		//echo "session : " . $_SESSION['sid'] . "=======" . "SID " . $this->getVar("sid");
		
		if( isset($_SESSION['sid']) && $_SESSION['sid'] = $this->getVar('sid'))
		{
			$this->_login = TRUE;
			return;
		}
		
		$this->_login = FALSE;
	}
	
	public function renderMessage($result)
	{
		if(!$result)
		{
			$error['ERROR'] = TRUE;
			$error['MSG'] = "系统忙，请稍候再试!";
			$this->setError($error);
			return FALSE;
		}
		
		if( !isset($result->respcode) || $result->respcode !== "0000")
		{
			$error['ERROR'] = TRUE;
			$error['MSG'] = $result->respdesc;
			$this->setError($error);
			return FALSE;
		}
			
		return TRUE;
	}
	
	public function encodeString($decrypted, $salt="12345678")
	{
		// Build a 256-bit $key which is a SHA256 hash of $salt and $password.
		$key = hash('SHA256', $salt, true);
		
		// Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)
		srand(); 
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
		if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) return false;
		
		// Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
		
		// We're done!
		return $iv_base64 . $encrypted;		
	}
	
	public function decodeString($encrypted, $salt="12345678")
	{
		// Build a 256-bit $key which is a SHA256 hash of $salt and $password.
		$key = hash('SHA256', $salt, true);
		
		// Retrieve $iv which is the first 22 characters plus ==, base64_decoded.
		$iv = base64_decode(substr($encrypted, 0, 22) . '==');
		
		// Remove $iv from $encrypted.
		$encrypted = substr($encrypted, 22);
		// Decrypt the data.  rtrim won't corrupt the data because the last 32 characters are the md5 hash; thus any \0 character has to be padding.
		$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
		// Retrieve $hash which is the last 32 characters of $decrypted.
		$hash = substr($decrypted, -32);
		// Remove the last 32 characters from $decrypted.
		$decrypted = substr($decrypted, 0, -32);
		// Integrity check.  If this fails, either the data is corrupted, or the password/salt was incorrect.
		if (md5($decrypted) != $hash) return false;
		
		// Yay!
		return $decrypted;		
	}
}