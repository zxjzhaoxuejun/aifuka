<?php
/********************************************************
 时间：2009-10-5

 程序：胡思文
 ********************************************************/
include("include/inc.php");
include(incpath."funlist2.php");

/**
 *
 * Enter description here ...
 * @author Administrator
 *
 */
class register extends alzCms
{
	function __construct(){
		global $cfg,$db;
		
		define("FRONT_THEME_DIR", TPL_FRONT_DIR.$cfg['front_theme'].'/');
		$cfg["modelcontent"]=$this->reLabel(FRONT_THEME_DIR."/list/register.html");	
		$content=$this->reLabel(FRONT_THEME_DIR."/main/main.html");
		echo $content;
	}
	
	
	function doregister(){
		echo "dd";
	}
}



$register=new register();
$action = $_GET["action"];
if($action=="register_update1"){
}
?>