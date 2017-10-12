<?php
/********************************************************
 时间：2009-10-27

 程序：胡思文
 ********************************************************/
include("include/inc.php");
include(incpath."funlist2.php");

$cfg["lanstr"] = G("lanstr",$_GET["lanstr"]);
if($cfg["lanstr"]=="" || ($cfg["lanstr"]!="en" && $cfg["lanstr"]!="zh_cn") )	$cfg["lanstr"] = $cfg["deflan2"];

$action = $_GET["action"];

if($_GET["topclassid"] !="" || $_GET["classid"] !="") 
   header("location:index.php?topclassid=$topclassid&classid=$classid&lanstr=$lanstr");

$cfg['front_username'] = $_SESSION['front_user_name'];

$members= new members();
$cfg['level_name'] = $_SESSION['front_level'] == 2 ? $members->_lang('level_cert','member') : $members->_lang('level_comm','member');
switch($action)
{
	case "check_username":
		die($members->check_username($username));
		break;
	case "subcomment":
		die($members->subcomment());
		break;
	case "register":
		die($members->register());
		break;

	case "register_update":
		die($members->register_act());
		break;
	case "register_update1":
		$members->register_act1();
		break;
	case "article":
		$members->getarticle();
		break;		
	case "comment":
		die($members->comment());
		break;
	case "modify":
		die($members->modify());
	case "register_edit":
		die($members->register_edit());
		break;
	case "logincheck":
		die($members->logincheck());
		break;
	case "forget":
		die($members->forget());
		break;
	case "getpassword":
		die($members->getpassword());
		break;
	case "change":
		die($members->change_pass());			
		break;
	case "changepassword":
		die($members->changepassword());
		break;
	case "logout":
		$members->username_quit();
		header("location:index.php?topclassid=0&classid=0&lanstr=".$cfg["lanstr"]);
		break;
	default:
		if($_SESSION['front_user_id']){
			die($members->center());
		}
		else {
			die($members->register());
		}
		break;
}
?>