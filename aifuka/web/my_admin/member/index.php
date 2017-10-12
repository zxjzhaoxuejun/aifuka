<?php
/**
 * 网站注册会员 管理
 */
include("../../include/inc.php");
include(incpath."funlist.php");

class myclass extends alzCms
{
	function __destruct(){$this->admincache2();}
	function __construct()
	{
		global $cfg,$admin;
		
		$this->admincache();
		loadlanXml("user","user_");
		if($_GET["c"]!=""){
			setcookies("classid",0);
			setcookies("page",1);
			setcookies("commend","");
			setcookies("del",0);
			setcookies("searchtype","");
			setcookies("keyword","");
			//			$cfg["classid"]=formatnum($_GET["classid"],0);
			$cfg["page"]=formatnum($_GET["page"],1);

			$cfg["del"]=formatnum($_GET["del"],0);
			$cfg["searchtype"]=$_GET["searchtype"];
			$cfg["keyword"]=$_GET["keyword"];
		}else{
			///			$cfg["classid"]=G("classid",$_GET["classid"]);
			$cfg["page"]=G("page",$_GET["page"],1);

			$cfg["del"]=G("del",$_GET["del"],0);
			$cfg["searchtype"]=G("searchtype",$_GET["searchtype"]);
			$cfg["keyword"]=G("keyword",$_GET["keyword"]);
		}
		define("model","member");
		define("modelpath", TPL_ADMIN_DIR."member/");
		define("model_imagepath",links_imagepath);
		$cfg["model_name"]='会员管理';
	}

	function def()
	{
		global $cfg,$db;

		$cfg["otherguidstr"]=$cfg["commendstr"]."|".$cfg["locked"]."|".$cfg["del"];
		
		echo $this->reLabel(modelpath."index.html");
	}
	function mylist($loopstr)
	{
		global $cfg,$db,$admin;

		$pagesize=$cfg["adminpagesize"];
		$cfg["pagesize"]=$pagesize;
		$beginid=($cfg["page"]-1)*$pagesize;
		$addsql=" where 1=1";
		if($cfg["del"]){
			$addsql.=" and del=1 ";
		}else{
			$addsql.=$classidsql." and del=0 ";
		}
		$addsql.=$this->getsearsql();
		$numsql="select * from #@__".model.$addsql;
		$cfg["allnums"]=$db->num($numsql,"allnum");
		$thesql="select * from #@__".model.$addsql." order by reg_time desc limit $beginid,$pagesize";

		$db->dosql($thesql);

		while($rs=$db->GetArray())
		{
			$cfg["lanid"]=$rs["id"];
			$cfg["user_name"]=$rs["user_name"];
			$cfg["email"]=$rs["email"];
			$cfg["company"]=$rs["company"];
			$cfg["tel"]=$rs["tel"];
			$cfg["realname"]=$rs["realname"];
			$cfg["level_name"] = $rs['level']==2 ? "认证会员" : '普通会员';
			$cfg["reg_time"]=date('Y-m-d H:i:s',$rs["reg_time"]);
			//	$cfg["viewurl"] = "<a href='?action=view&userid=".$rs["user_id"]."'>查看</a>";
			if($cfg["keyword"]!=""){
				$cfg["user_name"]=str_replace($cfg["keyword"],"<b class=red>".$cfg["user_name"]."</b>",$rs["user_name"]);
			}else{
				$cfg["user_name"]=left($rs["user_name"],$cfg["titlecutnum"]);
			}
			$funstr.=$this->reLabel2($loopstr);
		}
		return $funstr;
	}

	function export()
	{
		global $cfg,$db,$admin;
		include("../phpexcel/PHPExcel.php");

		$PHPExcel = new PHPExcel();

		$thesql="select * from #@__member order by reg_time desc";

		$db->dosql($thesql);

		$PHPExcel->setActiveSheetIndex(0);
		$Sheet = $PHPExcel->getActiveSheet();
		$Sheet->setCellValue('A1', '会员类型');
		$Sheet->setCellValue('B1', '账号');
		$Sheet->setCellValue('C1', '姓名');
		$Sheet->setCellValue('D1', '公司名称');
		$Sheet->setCellValue('E1', '联系电话');
		$Sheet->setCellValue('F1', '邮箱');
		$Sheet->setCellValue('G1', '地址');
		$Sheet->setCellValue('H1', '注册时间');

		$Sheet->getColumnDimension('A')->setAutoSize(true);
		$Sheet->getColumnDimension('B')->setAutoSize(true);
		$Sheet->getColumnDimension('C')->setAutoSize(true);
		$Sheet->getColumnDimension('D')->setAutoSize(true);
		$Sheet->getColumnDimension('E')->setAutoSize(true);
		$Sheet->getColumnDimension('F')->setAutoSize(true);
		$Sheet->getColumnDimension('G')->setAutoSize(true);
		$Sheet->getColumnDimension('H')->setAutoSize(true);
		  
		
		$i = 2;
		while($rs=$db->GetArray())
		{
			$Sheet->setCellValue('A'.$i, $rs['level']==2 ? "认证会员" : '普通会员');
			$Sheet->setCellValue('B'.$i, $rs["user_name"]);
			$Sheet->setCellValue('C'.$i, $rs["realname"]);
			$Sheet->setCellValue('D'.$i, $rs["company"]);
			$Sheet->setCellValue('E'.$i, $rs["tel"]);
			$Sheet->setCellValue('F'.$i, $rs["email"]);
			$Sheet->setCellValue('G'.$i, $rs["address"]);
			$Sheet->setCellValue('H'.$i, date('Y-m-d H:i:s',$rs["reg_time"]));	
			$i++;		
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="memberlist.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	function add()
	{
		global $cfg,$db;
		$cfg["title"]=guid_str."添加".$cfg["model_name"];
		echo $this->reLabel(modelpath."form.html");
	}

	function edit($lanid)
	{
		global $cfg,$db,$admin;
		$cfg["lanid"]=$lanid;
		$thesql="select * from #@__".model." where id=".$lanid;
		$db->dosql($thesql);
		while($rs=$db->GetArray())//通过lanid访问对应数据
		{
			$cfg["lanid"]=$rs["id"];
			$cfg["username"]=$rs["user_name"];
			
			$cfg["buydate"]=$rs["buydate"];
			$cfg["buymodel"]=$rs["buymodel"];
			$cfg["buyshop"]=$rs["buyshop"];
			$cfg["buyprice"]=$rs["buyprice"];
			
			$cfg["qq"]=$rs["qq"];
			$cfg["haoma"]=$rs["haoma"];
			
			
			$cfg["email"]=$rs["email"];
			$cfg["level"]=$rs["level"];
			$cfg['level'] = $cfg['level']==1 ? '<option value="1" selected>普通会员</option><option value="2"> 认证会员</option>' : '<option value="1">普通会员</option><option value="2" selected>认证会员</option>';

			$cfg["company"]=$rs["company"];
			$cfg["realname"]=$rs["realname"];
			$cfg["address"]=$rs["address"];
			$cfg["tel"]=$rs["tel"];

		}
		$cfg["title"]=guid_str.$cfg["model_name"]."编辑";
		echo $this->reLabel(modelpath."form.html");
	}



	function repass($id)
	{
		global $cfg,$db,$admin;
		$cfg["lanid"]=$id;
		$thesql="select * from #@__".model." where id=".$id;
		$db->dosql($thesql);
		while($rs=$db->GetArray())//通过lanid访问对应数据
		{
			$cfg["lanid"]=$rs["id"];
			$cfg["username"]=$rs["user_name"];
		}
		$cfg["title"]=guid_str.$cfg["model_name"]."密码重置";
		echo $this->reLabel(modelpath."reset.html");
	}




	function update()
	{
		global $cfg,$db,$admin;
		$type = $_POST['action_type'];
		if($type!='level'){
			$lanid=formatnum($_POST["lanid"],0);
			doMydb(0);
			$password = $_POST["password"];
			if($password==''){
				die("{ok}密码不能为空！");
			}

			$password= md5($password);
			$thesql="update #@__".model." set password='".$password."' where id=".$lanid;
			$db->execute($thesql);
			doMydb(1);
			if($lanid){die("{ok}恭喜，".$cfg["model_name"]."密码重置成功,请将密码({$_POST["password"]})告知会员！");}
		}
		else {

			$lanid=formatnum($_POST["lanid"],0);
			doMydb(0);
			$level = intval($_POST["level"]);

			$thesql="update #@__".model." set level='".$level."' where id=".$lanid;

			$db->execute($thesql);
			doMydb(1);
			if($lanid){die("{ok}恭喜，".$cfg["model_name"]."更改会员等级成功！");}
		}
	}
	
	function getsearsql()
	{
		global $cfg,$db;
		if($cfg["keyword"]=="")return;
		if($cfg["searchtype"]=="")return;
		return " and ".$cfg["searchtype"]." like '%".$cfg["keyword"]."%' ";
	}

	function admindo($lanidstr)
	{
		global $db;
		$str=$_GET["str"];
		$fun=$_GET["fun"];
		switch ($fun)
		{
			case "dels":
				if($lanidstr){
					$thesql = "delete from #@__member where id in ($lanidstr)";
					$db->execute($thesql);
				}
				break;
			case "re":
				$this->mydid($lanidstr,model,"del=0");
				break;
			case "reall":
				$this->mydidall(model,"del=0");
				break;
			case "deltrue":
				$thesql = "delete from #@__member where id in ($lanidstr)";
				$db->execute($thesql);
				break;
			case "delclear":
				$this->deltrue(model);
				break;
			case "locked":
				$this->mydid($lanidstr,model,"locked=1");
				break;
			case "dellocked":
				$this->mydid($lanidstr,model,"locked=0");
				break;
			case "delalllocked":
				$this->mydidall(model,"locked=0");
				break;
		}
		die("{ok}恭喜,".$str."操作成功!");
	}
}

$myclass=new myclass();
$lanid=$_GET["lanid"];
switch($_GET["action"])
{
	
	case "reset":
		$admin->adminck("member_set");
		$myclass->repass($lanid);
		break;
	case "edit":
		$admin->adminck("member_set");
		$myclass->edit($lanid);
		break;
	case "export":
		$admin->adminck("member_set");
		$myclass->export();
		break;

	case "update":
		refPage(2);
		$admin->adminck("member_set");
		$myclass->update();
		break;
	
	case "dbref":
		$myclass->dbref(model);
		break;

	case "guid":
		$myclass->guid(model);
		break;

	case "admindo":
		refPage(1);
		$myclass->admindo($lanid);
		break;
	default:
		$myclass->def();
}
?>