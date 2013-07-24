<?php
if( !defined('PROJECT_START') || !PROJECT_START) die("Access Denied");
/**
 * @copyright Copyright(2012) NetWebX All Right Reserved.
 * @filesource: User.php,v$
 * @package:Class
 *
 * @author WengJunFeng <jason@netwebx.com>
 * @version $Id: v 1.0 2012-06-11 Jason Exp $
 *
 * @abstract:
 */

class User extends Base
{
	public function __construct()
	{
		parent::__construct();
		$this->setTable("tbl_user");
	}
	
}