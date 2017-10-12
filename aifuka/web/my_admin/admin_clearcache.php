<?php
/**
 *  Clear system cache file.
 */
include("../include/inc.php");
include(incpath."funlist.php");

/**
 * 
 * 删除缓存文件
 * @author Administrator
 *
 */
class clearcache extends alzCms
{
	function __construct()
	{
		global $cfg,$db;
		$type=$_GET["type"];
		$this->reLabel("admin/admin_clearcache.html");
		//Delete the admin system cache file.
		if($type==1){
			$patharr=listdir(admincachepath);
			foreach($patharr as $k=>$file){
				if(is_file($file))
				   unlink($file);
			}
		}
		//Delete the front system file.
		if($type==2){
			$patharr = listdir(uicachepath);
			foreach($patharr as $k=>$file){
				if(is_file($file))
				unlink($file);
			}
		}
	}
}
$clearcache=new clearcache();
?>