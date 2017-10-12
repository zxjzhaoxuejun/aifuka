<?php

/**
 * 后台左栏菜单程序文件
 *
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */
include("../include/inc.php");
include(incpath . "funlist.php");

/**
 *
 * Enter description here ...
 * @author guoho
 *
 */
class admin_left extends alzCms {

	function __construct() {
		global $cfg;
		$cfg["leftfile"] = "admin/admin_left.html";
	}

	/**
	 *
	 * 显示左栏文件
	 */
	function def() {
		global $cfg;
		loadlanXml("adminleft", "left_");
		$cfg["topClassMenu"] = $menuStr;
		if($cfg["myadmin_username"]==$cfg["superusername"]){
			$cfg["is_superadmin"] = 1;
		}
		echo $this->reLabel("admin/admin_left.html");

	}


	/**
	 *
	 * 第一级分类列表,包括二级分类，三级分类，四级分类。
	 * @param unknown_type $loopstr
	 * @param unknown_type $valuestr
	 */
	function adminLeftMenuList($loopstr) {
		global $cfg, $db;
		$cfg["left_lanid"] = "";
		$thesql = "select * from #@__class where fid=0 and lanstr='" . lanstr . "' and lanid<>456 order by sortid,lanid";
		$db->dosql($thesql);
		$temp = '';
		while ($rs = $db->GetArray()) {
			if($rs['model'] =='feedback') continue;
			
			$cfg['classnum'] = '';
			$href = "";
			$cfg["lanid"] = $rs['lanid'];
			$childmenu = '';
			$childmenu=$this->adminleftmenu2($rs['model']);
			 
			$num='';
			if($rs['model']=='article' || $rs['model']=='product'){
			//分类数据量
				$num = $this->getdbnum("", $rs['model'], "classid in(" . allclassids($rs["lanid"]) . ") $lockedstr");
			}
				
			$href = 'class/?fid='.$rs["lanid"];
			// 是否有子分类
			if($childmenu==""){
				if($rs['model']!='onepage'){
					 $href = $rs['model'].'/?classid='.$rs["lanid"].'&c=1';
				}
				else{
				 	$href = 'class/?action=edit&lanid='.$rs["lanid"].'&lid='.$rs["lid"].'&topclassid='.$rs["topclassid"].'&main=1';
				}
				 
			}
			if($rs['model']=='article' || $rs['model']=='product' || $rs['model']=='feedback'){
				$_num = $this->getdbnum("", $rs['model'], "classid in(" . allclassids($rs["lanid"]) . ") and locked=1");
				
				$childmenu.="<li class='unlist'><a target='right' onclick='rightSrc(this);' href='".$rs['model']."/?locked=1&c=1''>未审核$_num</a></li>";
				$_num = $this->getdbnum("", $rs['model'], "classid in(" . allclassids($rs["lanid"]) . ") and del=1");

				$childmenu.="<li class='unlist'><a target='right' onclick='rightSrc(this);' href='".$rs['model']."/?del=1&c=1''>回收站$_num</a></li>";
			}
			$childmenu = $childmenu!="" ? "<ul>".$childmenu."</ul>" : "";
			$temp.='<li class="layer1"><a target="right" onclick="rightSrc(this);" href="'.$href.'">'.$rs['classname'].' '.$num .' </a>'.$childmenu.'</li>';
			 
		}
		return $temp;
	}

	/**
	 *
	 * 第二级子分类
	 * @param $model
	 */
	function adminleftmenu2($model) {
		global $cfg, $db;
		$lanid = $cfg["lanid"];
		$thesql = "select * from #@__class where fid=$lanid and lanstr='" . lanstr . "' order by sortid,lanid";
		$db->dosql($thesql, "m2");
		$temp='';
		while ($rs = $db->GetArray("m2")) {
			if($rs['model'] =='feedback') continue;
			$thelanid = $rs["lanid"];
			$cfg["left_lanid"].=$thelanid . ",";
			$theclassname = left($rs["classname"],15);
			$lockedstr = getLockedstr($model, " and locked=0 ");
			//获取分类的内容数量
			$num='';
			if($model=='article' || $model=='product'){
				$num = $this->getdbnum("", $model, "classid in(" . allclassids($thelanid) . ") $lockedstr");
			}
			$childmenu = '';
			$childmenu =$this->adminleftmenu3($thelanid, $model);
			$href = 'class/?fid='.$rs["lanid"].'&c=1';

			if($rs['model']!='onepage' ) {
				$href = $rs['model'].'/?classid='.$rs["lanid"].'&c=1';
			}
			else {
				$href = 'class/?action=edit&lanid='.$rs["lanid"].'&lid='.$rs["lid"].'&topclassid='.$rs["topclassid"].'';
			}
			$childmenu = $childmenu!="" ? "<ul>".$childmenu."</ul>" : "";
			$temp.="<li><a target='right' onclick='rightSrc(this);' href='$href'>" . $theclassname . $num. "</a>$childmenu</li>";
		}
		 
		return $temp;
	}

	/**
	 *
	 * 第三级子分类
	 * @param $lanid
	 * @param $model
	 */
	function adminleftmenu3($lanid, $model) {
		global $cfg, $db;
		$thesql = "select * from #@__class where fid=$lanid and lanstr='" . lanstr . "' order by sortid,lanid";
		$db->dosql($thesql, "m3");
		$temp='';
		while ($rs = $db->GetArray("m3")) {
			if($rs['model']=='feedback')                continue;
			$thelanid = $rs["lanid"];
			$cfg["left_lanid"].=$thelanid . ",";
			$theclassname = left($rs["classname"],15);
			$lockedstr = getLockedstr($model, " and locked=0 ");
			$num='';
			if($model=='article' || $model=='product')
			$num = $this->getdbnum("", $model, "classid in(" . allclassids($thelanid) . ") $lockedstr");

			$childmenu = '';
			$childmenu = $this->adminleftmenu4($thelanid, $model);
			$href = 'class/?fid='.$rs["lanid"].'&c=1';
			if($childmenu==""){
				if($rs['model']=='article' || $rs['model']=='product'|| $rs['model']=='job' || $rs['model']=='download') $href = $rs['model'].'/?classid='.$rs["lanid"].'&c=1';
				else $href = 'class/?action=edit&lanid='.$rs["lanid"].'&lid='.$rs["lid"].'&topclassid='.$rs["topclassid"].'';
			}
			$childmenu = $childmenu!="" ? "<ul>".$childmenu."</ul>" : "";
			$temp.="<li><a target='right' onclick='rightSrc(this);' href='$href'>" . $theclassname . $num . "</a>$childmenu</li>";

		}
		return $temp.'';
	}

	/**
	 *
	 * 第三级子分类
	 * @param $lanid
	 * @param $model
	 */
	function adminleftmenu4($lanid, $model) {
		global $cfg, $db;
		$thesql = "select * from #@__class where fid=$lanid and lanstr='" . lanstr . "' order by sortid,lanid";
		$db->dosql($thesql, "m4");
		$temp='';
		while ($rs = $db->GetArray("m4")) {
			if($rs['model']=='feedback')                continue;
			$thelanid = $rs["lanid"];
			$cfg["left_lanid"].=$thelanid . ",";
			$theclassname = left($rs["classname"],12);
			$lockedstr = getLockedstr($model, " and locked=0 ");
			$num='';
			if($model=='article' || $model=='product')
			$num = $this->getdbnum("", $model, "classid in(" . allclassids($thelanid) . ") $lockedstr");

			$href = 'class/?action=edit&lanid='.$rs["lanid"].'&lid='.$rs["lid"].'&topclassid='.$rs["topclassid"].'';
			if($rs['model']=='article' || $rs['model']=='product'|| $rs['model']=='job' || $rs['model']=='download') $href = $rs['model'].'/?classid='.$rs["lanid"].'&c=1';

			 
			$temp.="<div><a target='right' onclick='rightSrc(this);' href='$href'>" . $theclassname . $num . "</a></div>\r\n";
		}
		return $temp.'';
	}

	function adminLeftFeedNum() {
		global $cfg, $db;
		$thesql = "select id from #@__feedback where lanstr='" . lanstr . "'";
		$num = $db->num($thesql, "num");
		return "(<b>" . $num . "</b>)";
	}

	/**
	 *
	 * Enter description here ...
	 * @param $valuestr
	 * @param $model
	 * @param $type
	 */
	function getdbnum($valuestr="", $model="", $type="") {
		global $cfg, $db;

		if (!$cfg["adminleft_dbnum"])
		return;
		if ($valuestr != "") {
			$model = $this->getCan($valuestr, "model");
			$type = $this->getCan($valuestr, "type");
		}


		if ($type == "")
		$type = "1=1";
		if ($model == "comment") {
			$thesql = "select id from #@__$model where $type";

		} else if ($model == "feedback") {
			$thesql = "select id from #@__$model where $type";
		} elseif ($model == "member") {
			$thesql = "select lanid from #@__$model where $type";
		} else {
			$thesql = "select id from #@__$model where $type and lanstr='" . lanstr . "'";

		}
		
		$num = $db->num($thesql, "num");
		return "(<b>" . $num . "</b>)";
	}

	function getTableCount($valuestr) {
		if ($valuestr != "") {
			$model = $this->getCan($valuestr, "model");
			$type = $this->getCan($valuestr, "type");
		}
		if ($type == "")
		$type = "1=1";
		if ($model == "feedback") {
			$thesql = "select id from #@__$model where $type";
		} elseif ($model == "member") {
			$thesql = "select lanid from #@__$model where $type";
		} else {
			$thesql = "select id from #@__$model where $type and lanstr='" . lanstr . "'";
		}
		return $thesql;

		$num = $db->num($thesql, "num");
		return "(<b>" . $num . "</b>)";
	}

	/**
	 *
	 * Enter description here ...
	 * @param $valuestr
	 */
	function getdbnums($valuestr) {
		global $cfg, $db;
		if (!$cfg["adminleft_dbnum"])
		return;
		$type = $this->getCan($valuestr, "type");
		if ($type == "")
		$type = "1=1";
		$model = $cfg["model_getnum"];
		$lanid = $cfg["lanid"];
		if ($model != "feedback")
		$addsql = "and lanstr='" . lanstr . "'";
		$thesql = "select id from #@__$model where classid in (" . allclassids($lanid) . ") $addsql and " . $type;
		$num = $db->num($thesql, "nums");
		return "(<b>" . $num . "</b>)";
	}

	function getCount($valuestr){
		global $cfg, $db;
		$type = $this->getCan($valuestr, "type");
	
		$addsql = $type =='feedback' ? " and objid=0 " : ' and objid>0 ';

		$numsql = "select * from #@__comment where locked=0" . $addsql;
		
		$num = $db->num($numsql, "nums");
		return "(<b>" . $num . "</b>)";

	}

	function getUnCount($valuestr){
		global $cfg, $db;
		$type = $this->getCan($valuestr, "type");
		$addsql = $type =='feedback' ? " and objid=0 " : ' and objid>0 ';
		$numsql = "select * from #@__comment where locked=1" . $addsql;
		$num = $db->num($numsql, "nums");
		return "(<b>" . $num . "</b>)";

	}

}

$admin_left = new admin_left();
$admin_left->def();
?>