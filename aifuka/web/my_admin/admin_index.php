<?php
/**
 * 后台首页文件
 *
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */

include("../include/inc.php");
include(incpath."funlist.php");


/**
 * 
 * Enter description here ...
 * @author guoho
 *
 */
class admin_index extends alzCms
{
	function admin_index()
	{
	
		global $cfg,$admin;
		
		$expire_time = mktime(0,0,0,$cfg['expire_month'],$cfg['expire_day'],$cfg['expire_year']);
		if($cfg['is_expire_notice'] && ($expire_time - time()) < 30*24*3600 && $expire_time>time()   )  
		    $cfg['expire_text'] = ' <div class="expire">
<span>提醒</span>： 尊敬的用户，您的网站即将于 '.$cfg['expire_year'].'年 '.$cfg['expire_month'].'月'.$cfg['expire_day'].'号到期。 为避免网站自动关闭，请您马上联系续费专员， QQ： '.$cfg['expire_service_qq'].' 电话： '.$cfg['expire_service_tel'].' </div>';

		loadlanXml("adminindex","index_");
		$cfg["myrole"] = $admin->role;
		echo $this->reLabel("admin/admin_index.html");
	}	
}


adminipck();
$admin_index=new admin_index;
?>