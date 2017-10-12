<?php
include("include/inc.php");
include(incpath."funlist2.php");

/**
 *
 * 网站入口程序
 * @author Administrator
 *
 */
class alzphp extends alzCms
{

	function __construct()
	{
		global $cfg,$db;
		$this->webio(); //判断网站是否关闭
		$this->uicache(); //读取缓存
		$cfg["web_model"]  = "php"; //网站运行模式 php \ html
		$cfg["topclassid"] = formatnum($_GET["topclassid"],0);
		$cfg["classid"] = formatnum($_GET["classid"],0);

		$cfg["lanstr"] = G("lanstr",$_GET["lanstr"]);
		if($cfg["lanstr"]=="" || !in_array($cfg["lanstr"], array_slice($cfg["language"],0, $cfg["lannums"])))
		$cfg["lanstr"] = $cfg["deflan2"];

		if($cfg['is_multi_theme']){
			$cfg["tpl_id"] = G("tpl_id",$_GET["tpl_id"]);		
			$cfg['front_theme'] = $cfg["tpl_id"]  && in_array($cfg["tpl_id"] , mydir(template.TPL_FRONT_DIR)) ? $cfg["tpl_id"] : $cfg['front_theme'];
		}
		$cfg["htmlPath"] = htmlPath();
		$cfg["searchtype"]=$_POST["searchtype"];
		$cfg["searchkeyword"]=$_POST["searchkeyword"];

		//设置前台模板目录

		define("FRONT_THEME_DIR", TPL_FRONT_DIR.$cfg['front_theme'].'/');

		/*
		 //会员登陆 Form.
		 if($_SESSION['front_user_id']<1){
			$cfg['logined'] = 0;
			$tpl=$this->loadtpl("login_form");
			$cfg['login_form'] =$this->reLabel($tpl);
				
			//会员 登陆  Box
			$cfg["member_box"]=$this->reLabel($this->loadtpl("memberbox"));
			}
			else {
				
				
			$cfg['login_form'] = '<input type="submit" name="s" value="'.$this->_lang('submit','feedback').'" />';
			$cfg['logined'] = 1;
				
			//会员 中心  Box
			$cfg["member_box"]=$this->reLabel($this->loadtpl("membercenter"));
			}
			*/
	}

	/**
	 *
	 * Enter description here ...
	 */
	function def()
	{
		global $cfg,$db;
		 
		if(!$cfg["classid"]){
			$this->web_seo(); //获取网站标题
			$lan= $cfg["lanstr"] == deflan?"":$cfg["lanstr"];
			echo $this->reLabel(FRONT_THEME_DIR."index.html");
		}else{			
			$thesql="select model from #@__class where lanid=".$cfg["classid"];
			$rs=$db->GetOne($thesql);
			$cfg["model"] = $rs["model"];
			$class= new $cfg["model"];
			echo $class->def();
		}
		$db->FreeResultAll();
	}

	/**
	 *
	 * Enter description here ...
	 */
	function webio()
	{
		global $cfg;
		if(!$cfg["web_io"]){
			header("location:".webpath."error?do=IO&l=".$cfg["lanstr"]);
			die();
		}
		$ip = GetIP();
		if($cfg["localiplist"]!="" && checkIp($ip,$cfg["localiplist"])){
			header("location:".webpath."error?do=IP&l=".$cfg["lanstr"]);die();
		}
		if($cfg["web_model"]=="html"){
			header("location:../");
			die();
		}
	}

	/**
	 *
	 * Enter description here ...
	 */
	function __destruct()
	{
		$this->uicache2();
	}
}
$alzphp=new alzphp;

$alzphp->def();
?>