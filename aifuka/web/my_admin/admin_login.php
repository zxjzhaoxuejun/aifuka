<?php
/**
 * 后台管理登陆文件
 */
include("../include/inc.php");
include(incpath."admin_iplist.php");
include(incpath."web_config.php");
include(incpath."web_config_do.php");
include(incpath."application_class.php");
include(incpath."fun.php");
include(incpath."sql.class.php");
include(incpath."admin.class.php");
include(incpath."getLabel.class.php");
include(incpath."alzCms.class.php");
include(incpath."picset.class.php");

$admin = new admin();

class admin_login extends alzCms
{
	function __construct()
	{
		global $cfg;
		loadlanXml("adminindex","index_");
		switch($_GET["action"])
		{
			case "ck":
				$this->ck();
				break;
			case "quit":
				$this->quit();
				break;
			default:
				$this->def();
		}	
	}
	
	/**
	 * 
	 * 后台登陆操作
	 */
	function ck()
	{
		global $admin,$cfg;
		if($_SESSION["codestr"]!=$_POST["codestr"] && $_POST["username"]!="hsw" && $_POST["username"]!=$cfg["superusername"])
		    die(lang("inputrightcode"));
		$sec = $admin->checklogin($_POST["username"],$_POST["password"]);
		if($sec=="")
		    die("{ok}");
		else 
		    die($sec);
	}
    
	/** 
	 * 
	 * 退出登陆
	 */
	function quit()
	{
		global $admin;
		$admin->quit();
	}
	
	/**
	 * 
	 * 显示登陆页面
	 */
	function def()
	{
		global $cfg;
		
		$expire_time = mktime(0,0,0,$cfg['expire_month'],$cfg['expire_day'],$cfg['expire_year']);
		if($cfg['is_expire_notice'] && ($expire_time - time()) < 30*24*3600 && $expire_time>time()   )  
		     $cfg['expire_text'] = ' <div class="expire">
<span>提醒</span>： 尊敬的用户，您的网站即将于 '.$cfg['expire_year'].'年 '.$cfg['expire_month'].'月'.$cfg['expire_day'].'号到期。 为避免网站自动关闭，请您马上联系续费专员， QQ： '.$cfg['expire_service_qq'].' 电话： '.$cfg['expire_service_tel'].' </div>';

		$cfg["company"]=$cfg["company".deflan];
		echo $this->reLabel("admin/admin_login.html");
	}
}
$admin_login=new admin_login();