<?php
/**
 * 系统参数设置
 *
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */
include("../include/inc.php");
include(incpath."funlist.php");

class admin_set extends alzCms
{
	function __construct()
	{
		global $cfg,$admin;
	
		$this->admincache();
		loadlanXml("adminset","set_");
	}
	// 保存系统配置参数
	function save_()
	{
		global $db,$cfg;
		doMydb(0);
		foreach ($_POST as $key=>$value)
		{
			if($key=='piece_customer'){
				$valuestr = str_replace("\n","||",str_replace("\r", '', $value));
					
			}
			else {
				$valuestr = urldecode($value);
			}
			$valuestr = str_replace("[\$add]","+",$valuestr);
			$n=$db->num("select keystr from #@__cfg where keystr='".$key."'");
			if($n){
				$db->execute("update #@__cfg set valuestr='".$valuestr."' where keystr='".$key."'");
			}else{
				$db->execute("insert into #@__cfg values ('".$key."','".$valuestr."')");
			}
			$cfg[$key]=$valuestr;
		}
		doMydb(1);
		writeconfigfile();
		writeconfigtextcache();
		writeconfigjs();
		die("{ok}");
	}


	// 保存网站过期信息配置
	function save_expire_()
	{
		global $db,$cfg;
		doMydb(0);

		$expire_year = intval($cfg['expire_year'])+1;

		$db->execute("update #@__cfg set valuestr='".$expire_year."' where keystr='expire_year'");

		doMydb(1);
		writeconfigfile();
		writeconfigtextcache();
		writeconfigjs();
		die("{ok}");
	}


	function company_()
	{

		return $this->reLabel(TPL_ADMIN_DIR."set/company_.html");
	}

	function web_()
	{
		return $this->reLabel(TPL_ADMIN_DIR."set/web_.html");
	}

	function pic_()
	{
		return $this->reLabel(TPL_ADMIN_DIR."set/pic_.html");
	}

	function picsize_()
	{
		global $cfg;
		if($cfg["myadmin_username"] == $cfg["superusername"])
		return $this->reLabel(TPL_ADMIN_DIR."set/picsize_.html");
	}


	function count_()
	{
		return $this->reLabel(TPL_ADMIN_DIR."set/count_.html");
	}



	function alz_()
	{
		global $cfg;
		
		$list = mydir(template.TPL_FRONT_DIR);
		foreach ($list as $val){
			if($val == '.' || $val == '..' || !is_dir(template.TPL_FRONT_DIR.$val)) continue;
			$val = strval($val);
			$option.= $val== $cfg['front_theme'] ? "<option value='$val' selected>$val</option>" : "<option value='$val'>$val</option>" ;
		}

		$cfg['themes_option'] = $option;
		
		if($cfg["myadmin_username"] == $cfg["superusername"])
		return $this->reLabel(TPL_ADMIN_DIR."set/alz_.html");
	}
	// 网站到期设置 超级管理员可见
	function expire_()
	{
		global $cfg;
		if($cfg["myadmin_username"] == $cfg["superusername"])
		return $this->reLabel(TPL_ADMIN_DIR."set/expire_.html");
	}
	
// 网站到期设置 超级管理员可见
	function field_()
	{
		global $cfg;
		if($cfg["myadmin_username"] == $cfg["superusername"])
		return $this->reLabel(TPL_ADMIN_DIR."set/field_.html");
	}

	function def()
	{
		global $cfg;
		
		if($cfg["myadmin_username"]==$cfg["superusername"]){
			$cfg["is_superadmin"] = 1;
		}
		
		$cfg["web_deflan2"]=lannums>1?1:0;
		$cfg['logo_config'] = "{type:'JPG,GIF,PNG',size:3072}";
		$cfg['tab0'] = 'On';
		for($i=0; $i < 8; $i++){
			
			$cfg['table'.$i] = intval($_GET['type']) == $i ? '' : ' style="display:none;"';
		}
		
		echo $this->reLabel(TPL_ADMIN_DIR."set/def_.html");
	}

	function __destruct(){
		$this->admincache2();
	}
}

$admin_set=new admin_set();
//保存
if($_GET['expire']){
	$admin_set->save_expire_();
}
if($_GET["save"]){
	//refPage(1);
	$admin_set->save_();
}else{
	$admin_set->def();
}
?>