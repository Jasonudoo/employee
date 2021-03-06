<?php
/**
 * @copyright Copyright(2013) All Right Reserved.
 * @filesource: index.php,v$
 * @package: admin
 *
 * @author Jason Williams <jasonudoo@gmail.com>
 * @version $Id: v 1.0 2013-06-08 Jason Exp $
 *
 * @abstract:
 */

ini_set("display_errors", 1);
error_reporting(E_ALL);
define("PROJECT_START", TRUE);

require_once "Config" . DIRECTORY_SEPARATOR . "Global.inc.php";

final class AdminView extends Application
{
    public static $App;
    
	protected function _before_init()
	{
	}
	
	protected function _after_init()
	{
    
	}
	public static function Run()
	{
	    self::$App = parent::getInstance();
	    
		if( self::_isAuthorization() )
		{
			self::_doDashboard();
		}
		else
		{
			self::_doAuthorization();
		}
	}
	
	private static function _isAuthorization()
	{
	    $sess = new Session();
	    $sess->verify();
	    return $sess->Login;
	}
	
	private static function _doAuthorization()
	{
		if( self::_isPostCallback() && $_POST['action'] == "login")
		{
			$return = self::_doLogin();
			echo json_encode($return);
			exit;
		}
		else
		{
			self::_getHtmlHeader();
			self::_getLoginHtmlBody();
			self::_getFooter();
		}
	}
		
	private static function _getHtmlHeader($show_html = TRUE)
	{
		$html = <<<EON
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>The Toolbox For The Employee Information Website</title>
<link rel="shortcut icon" href="../images/favicon.png"/>
<link rel="stylesheet" type="text/css" href="../js/jquery-ui/css/ui-darkness/jquery-ui-1.8.13.custom.css" />
<link rel="stylesheet" type="text/css" href="../js/jquery-ui/css/flexigrid.pack.css" />
<script type="text/javascript" src="../js/jquery-ui/js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="../js/jquery.form.js"></script>
<script type="text/javascript" src="../js/jquery-ui/js/jquery-ui-1.8.13.custom.min.js"></script>
<style type="text/css">
body {font-size: 62.5%;}
body {font-family: "Trebuchet MS", "Helvetica", "Arial",  "Verdana", "sans-serif";}
</style>
</head>
<body>
EON;
		if( $show_html) echo $html;
		return;
	}
	
	private static function _getLoginHtmlBody($show_html = TRUE)
	{
		$html = <<<EOD
<style type="text/css">
.form_label{
	text-align:left;
	padding-top:7px;
	float:left;
}
.input{
	padding-top:5px;
	padding-bottom:5px;
	padding-left:100px;
}
.input input{
	width:200px;
}
.clear{
	clear:both;
}
</style>
<script type="text/javascript">
$(function(){
	$("<form id='loginFrm' method='post'></form>").appendTo('#content');
	$("<div><label class='form_label' for='username'>User Name : </label><div class='input'><input type='text' name='username' id='username' value='' /></div><div class='clear'></div></div>").appendTo('#loginFrm');
	$("<div><label class='form_label' for='passwd'>Password : </label><div class='input'><input type='password' name='passwd' id='passwd' value='' /></div><div class='clear'></div></div>").appendTo('#loginFrm');
	$("<input type='hidden' name='action' value='login' />").appendTo('#loginFrm');
	$("#content").dialog({
			title: "User Login",
			height: 150,
			width: 400,
			autoOpen: true,
			modal: true,
			buttons: {"Login" : function(){
				var frm_opt = {
					url : 'index.php',
					type : 'post',
					dataType : 'json',
					closeOnEscape : false,
					beforeSubmit : check_error_status,
					success : showResponse
				};
				$('#loginFrm').ajaxSubmit(frm_opt);
			}},
			close: function(event, ui){ $(this).dialog("open");}
	});
});

function check_error_status(formData, jqForm, options){
	var err = false;
	var f = ['username', 'passwd'];
	$.each(f, function(k,v){
		var e = '#' + v;
		if( $.trim($(e).val()) == "" ) {
			err = true;
			return false;
		}
	});
	if( err ){
		showErrorMessageBox('Please input the User Name and Password!');
		return false;
	}
	return true;
}

function showResponse(responseText, statusText, xhr, \$form){
	var d = responseText;
	if (d.error) {
		showErrorMessageBox(d.message);
	}
	else {
		window.location.href = d.message;
	}
}

function showErrorMessageBox(m){
	$("<div id='message' title='Ooop Error Message'><p>" + m + "</p></div>").dialog({
		modal: true,
		buttons: {Ok: function() {
			$(this).dialog( "destroy" );
		}}
	});
}
</script>
<div id="content"></div>
EOD;
		if( $show_html ) echo $html;
		return $html;
	}
	
	private static function _getFooter($show_html = TRUE)
	{
		$html = <<<EOFO
</body>
</html>
EOFO;
		if( $show_html ) echo $html;
		return $html;
	}
	
    private static function _doLogin()
    {
		$return = array();
		$session = "";
    	$username = urldecode($_POST['username']);
		$passwd = urldecode($_POST['passwd']);
		
        $user = new User();
        $userInfo = $user->login($username, $passwd);
        
        if($userInfo)
        {
            $return['error'] = FALSE;
            $return['message'] = "index.php";
            return $return;
        }
        		
		$return['error'] = TRUE;
		$return['message'] = "The User Name or Password is not correct!<br/>Please try again!";
		return $return;
    }
    
    private static function _doDashboard()
    {
    	if(isset($_POST['qtype']) )
    	{
    		self::_doSearch();
    		return;
    	}
    	
    	if(isset($_REQUEST['dashboard']))
    	{
    		if($_REQUEST['dashboard'] == "logout")
    		{
    			self::_doLogout();
    		}
    		if($_REQUEST['dashboard'] == "export")
    		{
    			self::_doExport();
    		}
    		return;
    	}
    	
    	self::_getHtmlHeader();
 		self::_getDashboardBody();
 		self::_getFooter();
    }
    
    private static function _getDashboardBody($show_html = TRUE)
    {
		$html = <<<EODB
<script type="text/javascript" src="../js/jquery-ui/js/flexigrid.js"></script>
<style type="text/css">
body{
	background-color:#7d7d7d;
}
.clear{
	clear:both;
}
#pbar{
	position:relative;
	margin:auto;
    width:400px;
	top:200px;
	z-index:1002;
}
#pval{
	position:relative;
    color:#fff;
	top:220px;
	height:20px;
    z-index:1002;
    font-size:12px;
    text-align:center;
}
</style>
<script type="text/javascript">
	$(function(){
		$('#content').ajaxStart(function(){
			$("<div id='bdiv'></div>").addClass("ui-widget-overlay").appendTo(document.body);
			$("<div id='pbar'></div><div class='clear'></div>").appendTo("#content");
			$("<div id='pval'>Loading the data for you...</div>").appendTo("#content");
		
			$('#pbar').progressbar({value:0});
			var i = setInterval(function(){
    			var p = $('#pbar').progressbar('option','value');
    			if( p < 100 ){
    				$('#pbar').progressbar('value', p + 10);
    			}
    			else{
    				clearInterval(i);
					$('#pbar').progressbar('destroy');
					$('#pval').remove();
					$('#bdiv').remove();
			
    			}
    		}, 100);
    	});
		$.post(window.location.href,{dashboard:'load'},function(data){
			$("<div id='tabs'></div>").appendTo("#content");
			$("<ul id='tabs_content'></ul>").appendTo("#tabs");
			$("<li><a href='#tab-1'>Employee Information</a></li>").appendTo("#tabs_content");
			$("<li><a href='#tab-2'>Toolbox User Information</a></li>").appendTo("#tabs_content");
			$("<li><a href='#tab-3'>General Settings</a></li>").appendTo("#tabs_content");
			$("<div id='tab-1'></div>").appendTo("#tabs");
		    $("<div id='tab-2'></div>").appendTo("#tabs");
            $("<div id='tab-3'></div>").appendTo("#tabs");
			$("<div id='employee-grid'></div>").appendTo("#tab-1");
		    $("<div id='user-grid'></div>").appendTo("#tab-2");
			$("#tabs").tabs({
    			ajaxOptions: {
    				success : function(data){
    					var d = eval('[' + data + ']');
    					if(d[0]['action'] == 'logout'){
    						window.location = 'index.php';
    					}
    				}
    			},
    			show: function(){
    				//$("#tab-grid").removeClass("ui-tabs-panel").removeClass("ui-widget-content").removeClass("ui-corner-bottom");
    			}
    		});

			$("#employee-grid").flexigrid({
				url: window.location.href,
				dataType: 'xml',
				colModel : [
					{display: 'ID', name : 'EMPLOYEE_ID', width : 30, sortable : true, align: 'center'},
					{display: 'First Name', name : 'FIRST_NAME', width : 80, sortable : false, align: 'left'},
					{display: 'Last Name', name : 'LAST_NAME', width : 80, sortable : false, align: 'left'},
					{display: 'E-mail Address', name : 'EMAIL', width : 80, sortable : true, align: 'left'},
					{display: 'Phone', name : 'PHONE_NUMBER', width : 80, sortable : false, align: 'right'},
					{display: 'Phone Extension', name : 'INTERNAL_EXTENS', width : 80, sortable : false, align: 'right'},
					{display: 'Fule Name', name : 'FILE_NAME', width : 180, sortable : true, align: 'center'},
					],
				buttons : [
		        	{name: 'Add', bclass: 'add', onpress : add_employee},
                    {name: 'Delete', bclass: 'delete', onpress : remove_employee},
                    {separator: true},
					{name: 'Import', bclass: 'export', onpress:import_employee}
				],
				searchitems : [
					{display: 'E-mail Address', name : 'EMAIL'},
					{display: 'First Name', name: 'FIRST_NAME'},
					{display: 'Last Name', name:'LAST_NAME'},
					{display: 'Phone', name:'PHONE_NUMBER'}
				],
				sortname: "FIRST_NAME",
				sortorder: "DESC",
				usepager: true,
				useRp: true,
				rp: 30,
				showTableToggleBtn: true,
				height: 500
			});

			$("#user-grid").flexigrid({
				url: window.location.href,
				dataType: 'xml',
				colModel : [
					{display: 'ID', name : 'USER_ID', width : 30, sortable : true, align: 'center'},
					{display: 'First Name', name : 'FIRST_NAME', width : 80, sortable : false, align: 'left'},
					{display: 'Last Name', name : 'LAST_NAME', width : 80, sortable : false, align: 'left'},
					{display: 'E-mail Address', name : 'CONTACT_EMAIL', width : 80, sortable : true, align: 'left'},
					{display: 'Phone', name : 'CONTACT_NUMBER', width : 80, sortable : false, align: 'right'},
					{display: 'Status', name : 'STATUS', width : 80, sortable : false, align: 'right'},
					{display: 'Last Login Time', name : 'LAST_LOGIN_TIME', width : 180, sortable : true, align: 'center'},
					],
				buttons : [
		        	{name: 'Add', bclass: 'add', onpress : add_user},
                    {name: 'Delete', bclass: 'delete', onpress : delete_user},
                    {separator: true}
				],
				searchitems : [
					{display: 'E-mail Address', name : 'EMAIL'},
					{display: 'First Name', name: 'FIRST_NAME'},
					{display: 'Last Name', name:'LAST_NAME'},
					{display: 'Phone', name:'CONTACT_NUMBER'}
				],
				sortname: "FIRST_NAME",
				sortorder: "DESC",
				usepager: true,
				useRp: true,
				rp: 30,
				showTableToggleBtn: true,
				height: 500
			});
		        
		});

        function add_user(){
        };
        function delete_user(){
        };
        function add_employee(){
            $("<div id='add_emp' title='Add Employee'></div>").appendTo("#content");
            $("<form id='empForm' method='post'></form>").appendTo('#add_emp');
    		$("<div style='padding-bottom:5px;'><label for='FName'>First Name : </label><div class='clear'></div><input type='text' id='FName' name='FName'/><div class='clear'></div></div>").appendTo("#empForm");
    		$("<div style='padding-bottom:5px;'><label for='LName'>Last Name : </label><div class='clear'></div><input type='text' id='LName' name='LName'/><div class='clear'></div></div>").appendTo("#empForm");
    		$("<div style='padding-bottom:5px;'><label for='EMail'>Email : </label><div class='clear'></div><input type='text' id='EMail' name='EMail'/><div class='clear'></div></div>").appendTo("#empForm");
    		$("<div style='padding-bottom:5px;'><label for='Phone'>Phone Number : </label><div class='clear'></div><input type='text' id='Phone' name='Phone'/><div class='clear'></div></div>").appendTo("#empForm");
    		$("<div style='padding-bottom:5px;'><label for='Extens'>Phone Extens Number : </label><div class='clear'></div><input type='text' id='Extens' name='Extens'/><div class='clear'></div></div>").appendTo("#empForm");
    		$("<div style='padding-bottom:5px;'><label for='Photo'>Photo : </label><div class='clear'></div><input type='file' id='Photo' name='Photo'/><div class='clear'></div><label>Upload the employee Photo</label><div class='clear'></div></div>").appendTo("#empForm");
    		$("<input type='hidden' name='action' value='add_employee' />").appendTo("#empForm");
    
    		$("#add_emp").dialog({
    			modal: true,
    			buttons: {
    				"Add": function() {
    		            var frm_opt = {
		                    url : 'index.php',
		                    type : 'post',
		                    dateType : 'json',
		                    beforeSubmit : validate_emp_form,
		                    success : showResponse
		                };
		                $('#empForm').ajaxSubmit(frm_opt);
    				},
    				Cancel: function() {
    					$(this).dialog("destroy");
    					$("#add_emp").remove();
        			}
        		}
    		});
        };
        function validate_emp_form(){
	        var err = false;
		    var msg = '';
	        var f = ['FName', 'LName', 'EMail', 'Phone'];
		    var m = ['Please input the employee First Name', 'Please input the employee Last Name', 'Please input the employee Email', 'Please input the employee Phone'];
	        $.each(f, function(k,v){
		        var e = '#' + v;
		        if( $.trim($(e).val()) == "" ) {
		            msg = msg + m[k] + "<br/>";
			        err = true;
		        }
	        });
	        if( err ){
		        showErrorMessageBox(msg);
		        return false;
	        }
	        return true;
        }
        function showResponse(responseText, statusText, xhr, \$form){
        	var d = responseText;
        	if (d.error) {
        		showErrorMessageBox(d.message);
        	}
        	else {
        		window.location.href = d.message;
        	}
        }
        
        function showErrorMessageBox(m){
        	$("<div id='message' title='Ooop Error Message'><p>" + m + "</p></div>").dialog({
        		modal: true,
        		buttons: {Ok: function() {
        			$(this).dialog( "destroy" );
        		}}
        	});
        }
        function remove_employee(){
    		
        };
        function import_employee(){
    		Import();
        };
        function Import(){
    		$("<div id='dd' title='Please Set the date range and Export the data'></div>").appendTo("#content");
    		$("<form id='expForm' method='post'></form>").appendTo('#dd');
    		$("<div style='padding-bottom:5px;'><label for='from'>From</label>&nbsp;<input type='text' id='from' name='from'/><div class='clear'></div></div>").appendTo("#expForm");
    		$("<div><label for='to'>To</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' id='to' name='to'/></div>").appendTo("#expForm");
    		$("<iframe id='secretIFrame' src='' style='display:none; visibility:hidden;'></iframe>").appendTo("#expForm");
    		
    		var dates = $( "#from, #to" ).datepicker({
    			dateFormat: "yy-mm-dd",
    			defaultDate: "-1w",
    			changeMonth: true,
    			numberOfMonths: 1,
    			onSelect: function( selectedDate ) {
    				var option = this.id == "from" ? "minDate" : "maxDate",
    					instance = $( this ).data( "datepicker" ),
    					date = $.datepicker.parseDate(
    						instance.settings.dateFormat ||
    						$.datepicker._defaults.dateFormat,
    						selectedDate, instance.settings );
    						dates.not( this ).datepicker( "option", option, date );
    			}
    		});
    		$("#dd").dialog({
    			modal: true,
    			buttons: {
    				Export: function() {
    					$("#secretIFrame").attr("src",window.location.href + "&dashboard=export&from=" + $("#from").val() + "&to=" + $("#to").val());
    				},
    				Cancel: function() {
    					$(this).dialog("destroy");
    					$("#dd").remove();
        			}
        		}
    		});
        };
		        
    });
</script>
<div id="content"></div>
EODB;
		if( $show_html ) echo $html;
		return $html;
    }
    
    private static function _doSearch()
    {
    	$page = $_POST['page'];
    	$rp = $_POST['rp'];
    	$sortname = $_POST['sortname'];
    	$sortorder = $_POST['sortorder'];
    	$query = trim($_POST['query']);
    	$qtype = $_POST['qtype'];

		$str = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
    	$str .= "<rows>";
    	$str .= "<page>$page</page>";
    	$str .= "</rows>";
    	echo $str;
   		return $str;
    }
    
    static private function _doLogout()
    {
        $user = new User();
        $user->signout();
		$result['action'] = 'logout';
		$result['message'] = 'logout now';
		echo json_encode($result);
		//header("Location : ". $_SERVER['PHP_SELF']);
		return;
	}
	
	static private function _doExport()
	{
		$wc = "";
		$output_file = "csv_export_all.csv";
		if( empty($_GET['from']) && !empty($_GET['to']))
		{
			$wc = "usr_created_date <= '".$_GET['to']."'";
			$output_file = "csv_export_to_".$_GET['to'].".csv";
		}
		elseif (empty($_GET['to']) && !empty($_GET['from']) )
		{
			$wc = "usr_created_date >= '".$_GET['from']."'";
			$output_file = "csv_export_from_".$_GET['from'].".csv";
		}
		elseif (!empty($_GET['from']) && !empty($_GET['to']) )
		{
			$wc = "usr_created_date >= '".$_GET['from']."' AND usr_created_date <= '".$_GET['to']."'";
			$output_file = "csv_export_".$_GET['from']."_".$_GET['to'].".csv";
		}
		if( !empty($wc) )
		{
			$wc = "WHERE ".$wc;
		}
		
		$csv_file = "file_".time().".csv";
		$list = array();
		$fp = fopen('/tmp/'.$csv_file, 'w');
		$field_name = array('SID', 'Create Date', 'Campaign', 'Sub ID', 'Sub ID 2', 'IP Address', 'From Page',
							'SilverPOP Action', 'Recipient Id', 'First Name', 'Last Name', 'Company', 'E-mail Address',
							'Phone', 'Phone Extension', 'Job Title','Option 1','Option 2','Option 3');
		//$list[] = $field_name;
		fputcsv($fp, $field_name);
    	
		self::_getForm()->connect();
		
		$sql = "SELECT * FROM tbl_userinfo " . $wc;
    	$qry = self::_getForm()->query($sql);
    	while( ($rs = self::_getForm()->fetch_array($qry)) !== FALSE )
    	{
    		$from_page = isset(self::$_source_from[$rs['usr_from_page']]) ? self::$_source_from[$rs['usr_from_page']] : "";
    		$insert_result = ($rs['usr_insert_silverpop_status'] == "YES") ? "SUCCESS" : "FAILTH";
    		$val = array($rs['id'], $rs['usr_created_date'], $rs['campaign_id'], $rs['usr_subid'], $rs['usr_sub_id_2'], $rs['usr_ip_addr'], $from_page,
    					$insert_result, $rs['usr_recipient_id'], $rs['usr_first_name'], $rs['usr_last_name'], $rs['usr_company'], $rs['usr_work_email'],
						$rs['usr_phone'], $rs['usr_phone_extent'], $rs['usr_job_title'], $rs['usr_option1'], $rs['usr_option2'], $rs['usr_option3']);
    		fputcsv($fp, $val);
			//$list[] = $val;
    	}
    	fclose($fp);

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-type: application/force-download");
		header("Content-Transfer-Encoding: Binary");
		header("Content-length: ".filesize('/tmp/'.$csv_file));
		header("Content-disposition: attachment; filename=\"".$output_file."\"");
		readfile("/tmp/".$csv_file);

		@unlink("/tmp/".$csv_file);
		//header("");
		//				$.post(window.location.href,{dashboard:'export', from:$("#from").val(), to:},function(data){
		//				$(this).dialog( "destroy" );
		//				$("#dd").remove();
    	//			});
		
		exit;
	}
}

AdminView::Run();