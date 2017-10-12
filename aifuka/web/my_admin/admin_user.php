<?php
/**
 * 系统管理员管理
 */
include("../include/inc.php");
include(incpath."funlist.php");
class admin_user extends alzCms
{
	function __destruct(){
            $this->admincache2();
           
        }
	function __construct()
	{
		global $cfg,$admin;
		
		$this->admincache();
	//	loadlanXml("adminuser","user_");
	}
	
	function def()
	{
		echo $this->reLabel("admin/admin_user.html");
	}
	
	function my()
	{
		global $cfg;
		$cfg["title"]=guid_str."修改我的密码";
		echo $this->reLabel("admin/admin_user_myform.html");
	}
	
	function save()
	{
		global $cfg,$db,$admin;
		$id=formatnum($_POST["id"],0);
		$admin_username=$_POST["admin_username"];
		if($admin_username==$cfg["superusername"])die("{err}此帐号不可以修改！");
		$admin_password=$_POST["admin_password"];		
		$fid=formatnum($_POST["fid"],0);
		$roleids=explode("|",$_POST["roleid"]);
		$roleid=formatnum($roleids[0],0);
		$role=$roleids[1];
		doMydb(0);
		if($id==0){
			$thesql="select admin_username from #@__admin where admin_username='".$admin_username."'";
			$num=$db->num($thesql);
			if($num>0){die("{err}重复的用户名！");}
			if($admin_password==""){die("{err}密码设置错误！");}
			$admin_password=md5($admin_password);		
			$thesql="insert into #@__admin (admin_username,admin_password,fid,roleid,role) values ('".$admin_username."','".$admin_password."',$fid,$roleid,'".$role."')";
			$db->execute($thesql);
			doMydb(1);
			die("{ok}恭喜，用户添加成功！");
		}else{
			if($admin_password!=""&&$admin_password!=lang("notedit")){$pwdstr="admin_password='".md5($admin_password)."',";}else{$pwdstr="";}
			if($fid!=""){$fidstr="fid=$fid,";}else{$fidstr="";}
			if($roleid!=0){$roleidstr="roleid=$roleid,";}else{$roleidstr="";}
			if($role!=""){$rolestr="role='$role',";}else{$rolestr="";}
			$thesql="update #@__admin set ".$pwdstr.$fidstr.$roleidstr.$rolestr."admin_username='".$admin_username."' where id=".$id;
			$db->execute($thesql);
			doMydb(1);
			die("{ok}恭喜，修改成功！");
		}
	}

	function adminuserlist($loopstr,$varstr)
	{
		global $db,$cfg;
		$size=formatnum($this->getCan($varstr,"size"),50);
		$myuserid=formatnum(getsession("admin_userid"),0);
		//$userids=alladminuserids($myuserid,false);
		$thesql="select * from #@__admin where roleid=3 order by id";
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$cfg["have_record"]=true;
			$cfg["logintime"]=date("Y-m-d H:i:s",$rs["logintime"]);
			$cfg["loginip"]=lookip($rs["loginip"]);
			$cfg["id"]=$rs["id"];
			$cfg["admin_username"]=$rs["admin_username"];
			$cfg["role"]=$rs["role"];
			$cfg["fusername"]=$db->getvalue("select admin_username from #@__admin where id=".$rs["fid"],"admin_username");
			if($cfg["fusername"]=="")$cfg["fusername"]="-";
			$funstr.=$this->reLabel2($loopstr);
		}
		return $funstr;
	}
	
	function fuser()
	{
		global $db,$cfg;		
		$oldfid=$cfg["fid"];
		$myuserid=formatnum(getsession("admin_userid"),0);
		$userids=alladminuserids($myuserid,false);
		$funstr="<select name='fid' class='post_'>";
		$funstr.="<option value='$myuserid'>直属于我</option>";		
		$db->dosql("select * from #@__admin where id in($userids)");
		while($rs=$db->GetArray())
		{
			if($oldfid==$rs["id"]){$cked="selected='selected'";}else{$cked="";}
			if($cfg["id"]!=$rs["id"])$funstr.="<option value='".$rs["id"]."' $cked>".$rs["admin_username"]."</option>";
		}
		$funstr.="</select>";
		return $funstr;
	}
	
	function rolelist()
	{
		global $db,$cfg,$admin;
		$adminstr=$admin->adminstr;
		$myrid=gets("id","role","adminstr='$adminstr'");
		$roleid=$cfg["roleid"];
		$funstr="<select name='roleid' class='post_'>";
		$uid=$cfg["myadmin_userid"];
		$str=$uid==0?"":" where id=$myrid or adminuserid=".$uid;
		$thesql="select * from #@__role".$str;
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			if($roleid==$rs["id"]){$cked="selected='selected'";}else{$cked="";}
			$funstr.="<option value='".$rs["id"]."|".$rs["role"]."' $cked>".$rs["role"]."</option>";
		}
		$funstr.="</select>";
		return $funstr;
	}
	
	function add()
	{
		global $cfg,$admin;
		$admin->adminck("sys_adminuser_add");
		$cfg["title"]=guid_str.t(2);
		echo $this->reLabel("admin/admin_user_form.html");
	}
	
	function edit($id)
	{
		global $cfg,$db;
		$cfg["title"]=guid_str.t(9);
		$thesql="select * from #@__admin where id=".$id;
		$rs=$db->GetOne($thesql);
		$cfg["id"]=$id;
		$cfg["fid"]=$rs["fid"];
		$cfg["roleid"]=$rs["roleid"];
		$cfg["role"]=$rs["role"];
		$cfg["adminusername"]=$rs["admin_username"];
		$cfg["noteditnull"]=lang("notedit");
		echo $this->reLabel("admin/admin_user_form.html");
	}
	
	function del($id)
	{
		global $cfg,$db;
		$thesql="delete from #@__admin where id=".$id;
		$db->execute($thesql);
		die("{ok}".lang("delok"));
	}
}


$admin_user=new admin_user();
$id=$_GET["id"];
switch ($_GET["action"])
{
	case "my":
		$admin_user->my();
		break;
	case "add":
		$admin_user->add();
		break;
	case "edit":
		$admin_user->edit($id);
		break;
	case "del":
		$admin_user->del($id);
		break;
	case "save":
		refPage(2);
		$admin_user->save();
		break;
	default:
		$admin_user->def();
}
?>