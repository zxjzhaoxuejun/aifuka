<?php
/**
 * 后台默认右栏文件
 */
include("../include/inc.php");
include(incpath."funlist.php");

class admin_main extends alzCms
{
	function __destruct(){$this->admincache2();}
	function __construct()
	{
		global $cfg,$db;
	//	$this->admincache();
		$cfg["php_uname"]=php_uname();
		$cfg["dbVersion"]=$db->GetVersion();
		echo $this->reLabel("admin/admin_main.html");
	}

}
$admin_main=new admin_main();
?>