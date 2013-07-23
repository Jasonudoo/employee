<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2012) ImageCO All Right Reserved.
 * @filesource: User.php,v$
 * @package:Class
 *
 * @author WengJunFeng <wengjf@imageco.com>
 * @version $Id: v 1.0 2012-06-11 Jason Exp $
 *
 * @abstract:
 */

class User extends Base
{
	private $_server_addr = NULL;
	private $_data = NULL;
	
	public static $ACTION_LOGIN = "login";
	public static $ACTION_REGISTER = "register";
	public static $ACTION_VALIDATE = "validate";
	public static $ACTION_SENDVALCODE = "sendvalcode";
	public static $ACTION_USERRESETPWD = "userresetpwd";
	public static $ACTION_RESETNEWPWD = "resetnewpwd";
	public static $ACTION_RESETPASSWORD = "resetpass";
	public static $ACTION_SIGNOUT = "signout";
	public static $ACTION_GETBARCODELIST = "barcodelist";
	public static $ACTION_GETBARCODEDETAIL = "barcodedetail";
	public static $ACTION_UPGRADE_VIP = "upgradevip";
	
	public function __construct()
	{
		parent::__construct();
		$this->setTable("user_info");
	}
	
	/**
	 * 设定街奴接口地址
	 * @param string $url
	 */
	public function setServerUrl($url)
	{
		$this->_server_addr = $url;
	}
	
	/**
	 * 获取街奴接口地址
	 * @return string
	 */
	public function getServerUrl()
	{
		return $this->_server_addr;
	}
	
	/**
	 * 向接口发送请求
	 * @param string $action
	 * @return boolean|mixed
	 */
	public function sendRequest($action)
	{
		$allow_action = array(self::$ACTION_LOGIN, self::$ACTION_SIGNOUT, 
							self::$ACTION_REGISTER, self::$ACTION_GETBARCODEDETAIL,
							self::$ACTION_SENDVALCODE, self::$ACTION_USERRESETPWD,
							self::$ACTION_GETBARCODELIST, self::$ACTION_UPGRADE_VIP, 
							self::$ACTION_VALIDATE, self::$ACTION_RESETPASSWORD,
							self::$ACTION_RESETNEWPWD
							);
		
		if( !in_array($action, $allow_action) )
		{
			return FALSE;
		}
		
		$method_name = "_FUNC_" . $action;
		if( method_exists($this, $method_name) )
		{
			return call_user_method($method_name, $this);
		}
		
		return FALSE;
	}
	
	/**
	 * 登录请求
	 * @return mixed
	 */
	private function _FUNC_login()
	{
		$request['application'] = "UserLogin";
		$request['mobilenumber'] = $this->getData("phone");
		$request['password'] = $this->getData("password");
		
		$post_string = json_encode($request);
		$output = $this->_post_request($post_string);
		return $this->_render_output($output);
	}
	
	/**
	 * 登出请求
	 * @return mixed
	 */
	private function _FUNC_signout()
	{
		$request['application'] = "UserExit";
		$request['session'] = $this->getData('session');
		
		$post_string = json_encode($request);
		$output = $this->_post_request($post_string);
		return $this->_render_output($output);
	}
	
	/**
	 * 注册请求
	 * @return mixed
	 */
	private function _FUNC_register()
	{
		$request['application'] = "UserRegister";
		$request['mobilenumber'] = $this->getData("phone");
		$request['val_code'] = $this->getData("validate_code");
		$request['password'] = $this->getData("password");
		
		$post_string = json_encode($request);
		$output = $this->_post_request($post_string);
		return $this->_render_output($output);
	}
	
	/**
	 * 
	 * @return mixed
	 */
	private function _FUNC_validate()
	{
		$request['application'] = "GetValidate";
		$request['mobilenumber'] = $this->getData("phone");
		
		$post_string = json_encode($request);
		$output = $this->_post_request($post_string);
		return $this->_render_output($output);
	}
	
	/**
	 * 发送验证码请求
	 * @return mixed
	 */
	private function _FUNC_sendvalcode()
	{
		$request['application'] = "Sendvalcode";
		$request['mobilenumber'] = $this->getData("phone");
		
		$post_string = json_encode($request);
		$output = $this->_post_request($post_string);
		return $this->_render_output($output);
	}
	
	/**
	 * 忘记密码请求
	 * @return mixed
	 */
	private function _FUNC_userresetpwd()
	{
		$request['application'] = "UserResetPwd";
		$request['mobilenumber'] = $this->getData("phone");
		//$request['varcode'] = $this->getData("varcode");
		$request['newpwd'] = $this->getData("newpassword");
		
		$post_string = json_encode($request);
		$output = $this->_post_request($post_string);
		return $this->_render_output($output);
	}
	
	/**
	 * 忘记密码请求（NEW)
	 */
	private function _FUNC_resetnewpwd()
	{
		$request['application'] = "ResetNewPwd";
		$request['mobilenumber'] = $this->getData("phone");
		$request['varcode'] = $this->getData("varcode");
		$request['newpwd'] = $this->getData("newpassword");
		
		$post_string = json_encode($request);
		$output = $this->_post_request($post_string);
		return $this->_render_output($output);
	}
	
	/**
	 * 修改密码请求
	 * @return mixed
	 */
	private function _FUNC_resetpass()
	{
		$request['application'] = "UserUpdatePwd";
		$request['mobilenumber'] = $this->getData("phone");
		$request['oldpassword'] = $this->getData("oldpasswd");
		$request['newpassword'] = $this->getData("newpasswd");
		$request['session'] = $this->getData("session");

		$post_string = json_encode($request);
		$output = $this->_post_request($post_string);
		return $this->_render_output($output);
	}
	
	private function _FUNC_barcodelist()
	{
		$request['application'] = "BarcodeList";
		$request['mobilenumber'] = $this->getData("phone");
		$request['session'] = $this->getData("session");

		$post_string = json_encode($request);
		$output = $this->_post_request($post_string);
		return $output;
	}
	
	private function _FUNC_barcodedetail()
	{
		$request['application'] = "BarcodeDetail";
		$request['id'] = $this->getData("code_id");
		$request['session'] = $this->getData("session");
		//$request['org_times'] = $this->getData("org_times");
		//$request['org_amt'] = $this->getData("ort_amt");
		//$request['remain_times'] = $this->getData("remain_times");
		//$request['remain_amt'] = $this->getData("remain_amt");

		$post_string = json_encode($request);
		$output = $this->_post_request($post_string);
		return $this->_render_output($output);
	}
	
	/**
	 * 升级VIP请求
	 * @return mixed
	 */
	private function _FUNC_upgradevip()
	{
		$request['application'] = "User_update";
		$request['session'] = $this->getData("session");
		$request['mobilenumber'] = $this->getData("phone");
		
		$post_string = json_encode($request);
		$output = $this->_post_request($post_string);
		return $this->_render_output($output);
	}
	
	/**
	 * POST提交方法
	 * @param string $post_string
	 * @return Ambigous <string, mixed>
	 */
	private function _post_request($post_string)
	{
		if (function_exists('curl_init')) 
		{
			// Use CURL if installed...
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->getServerUrl());
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Jienu API PHP5 Client 1.0 (curl) ' . phpversion());
			$result = curl_exec($ch);
			curl_close($ch);
		} 
		else 
		{
			// Non-CURL based version...
			$context =
			array('http' =>
				array('method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
						'User-Agent: JieNu API PHP5 Client 1.0 (non-curl) '.phpversion()."\r\n".
						'Content-length: ' . strlen($post_string),
					'content' => $post_string));
			$contextid = stream_context_create($context);
			$sock = fopen($this->getServerUrl(), 'r', false, $contextid);
			if ($sock) 
			{
				$result = '';
				while (!feof($sock))
					$result .= fgets($sock, 4096);
				fclose($sock);
			}
		}
		return $result;
	}
	
	/**
	 * 处理请求输出
	 * @param unknown_type $output
	 * @return mixed
	 */
	private function _render_output($output)
	{
		$output = rawurldecode($output);
		$result = json_decode($output);
		
		if($result instanceof stdClass)
		{
			return $result;
		}
		
		return FALSE;
		
	}
	
	/**
	 * 新增数据
	 * @param string $phone
	 * @param string $is_vip
	 * @return int $user_id
	 */
	public function insert($phone, $is_vip = "0")
	{
		$now = date("Y-m-d H:i:s");
		$data['user_name'] = $phone;
		$data['phone_number'] = $phone;
		$data['user_vip'] = $is_vip;
		$data['register_time'] = $now;
		$data['last_login_time'] = $now;
			
		$user_id = $this->_insert($data);
		return $user_id;
	}
	
	/**
	 * 更新注册和登录时间
	 * @param string $phone
	 */
	public function update($phone)
	{
		$sql = "UPDATE " . $this->getTable() . " SET last_login_time = now(), register_time = now() WHERE user_name = '" . $phone . "' LIMIT 1";
		$qry = $this->_db->query($sql);
		
		return $this->_db->affected_rows();
	}
	
	/**
	 * 根据电话号码取得用户信息
	 * @param string $phone
	 * @return mixed
	 */
	public function query($phone)
	{
		$sql = "SELECT user_id, user_name, user_vip, user_credit, phone_number, last_login_time, register_time 
				FROM " . $this->getTable() . " WHERE user_name = '" . $phone . "' LIMIT 1";
		$qry = $this->_db->query($sql);
		$result = $this->_db->fetch_array($qry);
		
		return $result;
	}
	
	/**
	 * 根据用户ID取得用户信息
	 * @param int $uid 用户编号
	 * @return mixed
	 */
	public function getUserInfoById($uid)
	{
		$sql = "SELECT user_id, user_name, user_vip, user_credit, phone_number, last_login_time, register_time FROM " . $this->getTable() . " WHERE user_id = %d LIMIT 1";
		$sql = sprintf($sql, $uid);
		$qry = $this->_db->query($sql);
		$result = $this->_db->fetch_array($qry);
		
		return $result;
	}	

	public function isExists($uid)
	{
		$sql = sprintf("SELECT count(1) AS CNT FROM " . $this->getTable() . " WHERE user_id = %d", $uid);
		$qry = $this->_db->query($sql);
		$result = $this->_db->fetch_result($qry);
		
		return $result;
	}
	
	/**
	 * 登入操作
	 * @param string $phone 电话号码
	 * @param string $sid sesion id
	 * @param object $resp 回传数据
	 * @return boolean
	 */
	public function login($phone, $sid, $resp)
	{
		$userInfo = $this->query($phone);

		if( !$userInfo )
		{
			$userInfo['USER_ID'] = $this->insert($phone, $resp->is_vip);
		}
		else
		{
			$this->update($phone);
		}
		
		//set login information
		$_SESSION['sid'] = $sid;
		$_SESSION['sseid'] = $resp->session;
		$_SESSION['phone'] = $phone;
		$_SESSION['uid'] = $userInfo['USER_ID'];
		@setcookie("sid", $sid, time() + COOKIE_EXPIRED, "/");
		@setcookie("sseid", $resp->session, time() + COOKIE_EXPIRED, "/");
		@setcookie("phone", $phone, time() + COOKIE_EXPIRED, "/");
		@setcookie("uid", $userInfo['USER_ID'], time() + COOKIE_EXPIRED, "/");

		return TRUE;
	}
	
	/**
	 * 登出操作
	 * @return boolean
	 */
	public function signout()
	{
		if( isset($_SESSION['sseid']) )
		{
			$this->setServerUrl(WEB_INTERFACE_JIENU);
			$this->setData("session", $_SESSION['sseid']);
			$this->sendRequest(self::$ACTION_SIGNOUT);
		}
		
		unset($_SESSION['sid']);
		unset($_SESSION['sseid']);
		unset($_SESSION['phone']);
		unset($_SESSION['uid']);
		unset($_SESSION['uutoken']);
		unset($_SESSION['order_seq']);
		session_destroy();
		@setcookie("sid", "", 0);
		@setcookie("sseid", "", 0);
		if( !isset($_COOKIE['remphone']) || $_COOKIE['remphone'] == 0)
		{
			@setcookie("phone", "", 0);
		}
		@setcookie("uid", "", 0);

		return TRUE;
	}
	
	/**
	 * 升级为VIP
	 * @param int $uid
	 */
	public function upgradeToVip($uid)
	{
		$sql = "UPDATE " . $this->getTable() . " SET user_vip = 1 WHERE user_id = %d LIMIT 1";
		$sql = sprintf($sql, $uid);
		$this->_db->query($sql);
	}
}