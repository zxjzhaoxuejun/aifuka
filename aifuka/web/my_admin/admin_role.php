<?php
/**
 * 系统管理员角色管理
 */
include("../include/inc.php");
include(incpath."funlist.php");

/**
 * 此处是系统所有功能管理权限分类。
 */
$privTypeAll = array("class"=>"栏目管理",
					"article"=>"文章管理",
					"product"=>"产品管理",
					"download"=>"下载管理",
					"links"=>"链接管理",
					"job"=>"招聘管理",
					"feedback"=>"留言管理",
					"comment"=>"评论管理",
					"service"=>"在线客服",
					"ad"=>"广告管理",
					"member"=>"会员管理",
					"piece"=>"单页碎片",
					"keylink"=>"链接关键词",
					"set"=>"系统设置",
					"sys"=>"管理员管理",
					);

					
/**
 * 此处是根据不同的网站需要，为不同客户显示不同的管理权限分类。
 */
 $privType =array("class"=>"栏目管理",
					"article"=>"文章管理",
					"product"=>"产品管理",
					"download"=>"下载管理",
					"links"=>"链接管理",
					"job"=>"招聘管理",
					"feedback"=>"留言管理",
					
					"service"=>"在线客服",
					"ad"=>"广告管理",
					
					"piece"=>"单页碎片",
					"set"=>"系统设置",
					"sys"=>"管理员管理",
					);
 
class admin_role extends alzCms
{
	
	function __construct()
	{
		global $admin;		
		$this->admincache();
		loadlanXml("adminrole","role_");
		$id=formatnum($_GET["id"],0);
		switch($_GET["action"])
		{
			case "add":
				$admin->adminck("sys_role_add");
				$this->add();
				break;
			case "edit":
				$admin->adminck("sys_role_edit");
				$this->edit($id);
				break;
			case "del":
				$admin->adminck("sys_role_del");
				refPage(1);
				$this->del($id);
				break;
			case "save":
				refPage(2);
				$this->save();
				break;
			case "diysave":
				refPage(2);
				$admin->adminck("model_diy_io");
				$this->diysave();
				break;
			default:
				$this->def();
		}
	}
	
	function def()
	{
		global $admin;
		
		echo $this->reLabel("admin/admin_role.html");
	}
	
	function save()
	{
		global $cfg,$db;
		$id=formatnum($_POST["id"],0);
		$role=$_POST["role"];
		$adminstr=$_POST["adminstr"];
		doMydb(0);
		if($id==0)
		{
			$db->execute("insert into #@__role (role,adminstr,adminuserid) values ('".$role."','".$adminstr."',{$cfg["myadmin_userid"]})");
			doMydb(1);
			$this->adminstrtoapp();
			die("{ok}恭喜，角色添加成功！");
		}else{
			$db->execute("update #@__role set role='".$role."',adminstr='".$adminstr."' where id=".$id);
			doMydb(1);
			$this->adminstrtoapp();
			die("{ok}恭喜，角色编辑成功！");
		}
	}
	
	/**
	 * 管理组列表
	 */
	function rolelist($loopstr,$varstr)
	{
		global $db,$cfg;
		$size=formatnum($this->getCan($varstr,"size"),50);
		$uid=$cfg["myadmin_userid"];
		$str=$uid==0?"":" where adminuserid=".$uid;
		$thesql="select * from #@__role".$str;
		$db->dosql($thesql);
		if($db->nums()){
			while($rs=$db->GetArray())
			{
				$cfg["id"]=$rs["id"];
				$cfg["role"]=$rs["role"];
				$cfg["adminstr"]=left($rs["adminstr"],$size);
				$funstr.=$this->reLabel2($loopstr);
			}
		}else{
			$funstr="<tr><td colspan='3' class='norecord'>".lang("norecord")."</td></tr>";
		}
		return $funstr;
	}
	
	function inthisrole()
	{
		global $cfg,$db;
		$roleid=$cfg["id"];
		$thesql="select admin_username from #@__admin where roleid=".$roleid;
		$db->dosql($thesql,"in");
		$i=0;
		while($rs=$db->GetArray("in"))
		{
			if($funstr!=""&&$i>9)return trim($funstr,",")."……";
			$funstr.=",".$rs["admin_username"];
			$i++;
		}
		if($funstr==""){return "无";}else{return trim($funstr,",");}
	}
	
	function add()
	{
		global $cfg;
		$cfg["title"]=guid_str.t(2);
		echo $this->reLabel("admin/admin_role_form.html");
	}
	
	function edit($id)
	{
		global $cfg,$db;
		$cfg["title"]=guid_str.t(3);
		$cfg["id"]=$id;
		$thesql="select * from #@__role where id=".$id;
		$rs=$db->GetOne($thesql);
		$cfg["role"]=$rs["role"];
		$cfg["adminstr"]=$rs["adminstr"];
		echo $this->reLabel("admin/admin_role_form.html");
	}
	
	
	function del($id)
	{
		global $cfg,$db;
		$thesql="delete from #@__role where id=".$id;
		$db->execute($thesql);
		die("{ok}".lang("delok"));
	}
	
	/*
	 * 管理角色编辑 之 权限列表
	 */	
	function adminstrlist()
	{
		global $cfg,$db, $privType;
		
		
		foreach($privType as $key=>$name)
		{
		
			$funstr.="<div class='cfgstr_title ".$key."'>$name</a> ";
			$funstr.=$this->adminstrlist2($key,$key )."</div>";
		}
		return $funstr;
	}
	
	function adminstrlist2($model,$tag)
	{
		global $cfg,$db;
	//	$funstr="<div class='cfgstr_c $tag' id='adminstr_$model'>";
		$thesql="select * from #@__cfgstr where tag like '".$model."%' order by sortid,tag";
		$db->dosql($thesql,"alz");
		while($rs=$db->GetArray("alz"))
		{
			if(inadminstr($cfg["adminstr"],$rs["tag"])){$cked="checked='checked'";}else{$cked="";}
			$funstr.="<input class='post_' type='checkbox' name='adminstr' value='".$rs["tag"]."' $cked /> ".$rs["title"];
		}
		
		return $funstr;
	}
	
	function adminstrtoapp()
	{
		global $cfg,$db;
		$thesql="select tag from #@__cfgstr";
		$db->dosql($thesql);
		$temp="";		
		while($rs=$db->GetArray()){$temp.=$rs["tag"].",";}
		$temp=trim($temp,",");
		$app=new application();
		$app->app("alladminstr",$temp);
		$app=null;
		return $temp;
	}

    function __destruct(){$this->admincache2();}
}

$admin_role=new admin_role();
?>