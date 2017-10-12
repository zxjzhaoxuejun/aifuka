<?php
/**
 *  首页轮换特效管理
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
		loadlanXml("ad","ad_");
		define("model","ad");
		
		define("modelpath",TPL_ADMIN_DIR."ad/");
		define("model_imagepath",ad_imagepath);
		define("models","ad");
		$cfg["model_name"] = "特效广告";
	}
	//显示列表
	function def()
	{
		echo $this->reLabel(modelpath."index.html");
	}


	//特效列表
	function mylist($loopstr)
	{
		global $cfg,$db,$admin;
		$thesql="select * from #@__".model." where lanstr='".lanstr."'";
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{			
			$cfg["lanid"]=$rs["lanid"];
			$cfg["title"]=$rs["title"];
			$funstr.=$this->reLabel2($loopstr);
		}
		return $funstr;
	}

	/**
	 * 添加新特效
	 * @global type $cfg
	 * @global type $db
	 * @global type $admin
	 */
	function add()
	{
		global $cfg,$db,$admin;		
		$cfg["ad_config"] = "{type:'".$cfg["pic_ext_c"].",SWF',size:3072,addmore:'ad'}";
		$cfg["pagetitle"] = guid_str."添加".$cfg["model_name"];
		$cfg["time"]=5000;
		
		echo $this->reLabel(modelpath."form.html");
	}


	/**
	 * 编辑特效
	 * @global type $cfg
	 * @global type $db
	 * @global type $admin
	 * @param type $lanid
	 */
	function edit($lanid)
	{
		global $cfg,$db,$admin;
		$cfg["ad_config"]="{type:'".$cfg["pic_ext_c"].",SWF',size:3072,addmore:'ad'}";
		$cfg["lanid"]=$lanid;
		$thesql="select * from #@__".model." where lanid=".$lanid;
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$cfg["title"]=$rs["title"];
			$cfg["css"]=$rs["css"];
			$cfg["target"]=$rs["target"];
			$cfg["time"]=$rs["time"];
			$cfg["picstr".$rs["lanstr"]]=$rs["picstr"];
			$cfg["smallstr".$rs["lanstr"]]=$rs["smallstr"];
			$cfg["smallstr1".$rs["lanstr"]]=$rs["smallstr1"];
			$cfg["namestr".$rs["lanstr"]]=$rs["namestr"];
			$cfg["linkstr".$rs["lanstr"]]=$rs["linkstr"];

		}
		$cfg["pagetitle"]=guid_str.$cfg["model_name"]."编辑";
		if($cfg["lanid"]==43){
			echo $this->reLabel(modelpath."form1.html");
		}elseif($cfg["lanid"]==35){
			echo $this->reLabel(modelpath."form2.html");
		}elseif($cfg["lanid"]==28){
			echo $this->reLabel(modelpath."form3.html");
		}else{
			echo $this->reLabel(modelpath."form.html");
		}
	}


	/**
	 * 更新特效
	 * @global type $cfg
	 * @global type $db
	 * @global type $admin
	 */
	function update()
	{
		global $cfg,$db,$admin;
		$lanid = formatnum($_POST["lanid"],0);

		for($i=0;$i<$cfg['lannums'];$i++)
		{
			$lanstr = $cfg["language"][$i];
			$title=$_POST["title"];
			$css=$_POST["css"];
			$target=$_POST["target"];
			$time= intval($_POST["time"]);
			$picstr=$_POST["picstr".$lanstr];
			$smallstr=$_POST["smallstr".$lanstr];
			$smallstr1=$_POST["smallstr1".$lanstr];
			$namestr=$_POST["namestr".$lanstr];
			$linkstr=$_POST["linkstr".$lanstr];
			if($lanid){//编辑
				$htmllanid=$lanid;
				$thesql="update #@__".model." set
				title='".$title."',time=$time,picstr='".$picstr."',smallstr='".$smallstr."',"."smallstr1='".$smallstr1."',
				namestr='".$namestr."',linkstr='".$linkstr."'				
				where lanstr='".$lanstr."' and lanid=".$lanid;
				setcookies("123",$thesql);
				$db->execute($thesql);
			}else{
				if($i==0)$newlanid=0;
				$thesql="insert into #@__".model." (lanid,lanstr,title,picstr,namestr,linkstr,time,target,smallstr,smallstr1
				) values (
				$newlanid,'".$lanstr."','".$title."','".$picstr."','".$namestr."','".$linkstr."',$time,'$target','$smallstr','$smallstr1'
				)";
				$db->execute($thesql);
				if($i==0){
					$newlanid=$db->GetLastID();
					$htmllanid=$newlanid;
					$thesql2="update #@__".model." set lanid=$newlanid where id=".$newlanid;
					$db->execute($thesql2);
				}
			}
			//生成 XML文件等操作。
			$picArr = explode("\n",str_replace("\r\n", "\n", $picstr));
			$linkArr = explode("\n",str_replace("\r\n", "\n", $linkstr));
			$smallArr = explode("\n",str_replace("\r\n", "\n", $smallstr));
			$small1Arr = explode("\n",str_replace("\r\n", "\n", $smallstr1));
			$nameArr = explode("\n",str_replace("\r\n", "\n", $namestr));
			$str = "<?xml version='1.0' encoding='GB2312'?>
			        <imgList>
					<pic>";
			$count = 0;
			foreach($picArr as $key=>$pic){
				if(trim($pic)){				
					$link = $linkArr[$key]!="" ?  $linkArr[$key] : '#';
					if(!instr($link,"http") && $link !='#'){
						$link=webpath.$link;
					}
					$count++;
					$str.="<list path='".webpath.ad_imagepath.$pic."' smallpath='".webpath.ad_imagepath.$smallArr[$key]."' smallinfo='".$nameArr[$key]."'>".$linkArr[$key]."</list>";
				}

			}
			
			$str.="</pic>
			<rollTime fade_in='10'>".(++$count)."</rollTime>
			<text font='Arial' size='12' bold='true' color='0xfffffff'></text>
			</imgList>";
			file_put_contents(siteroot.'skins/list_'.$lanstr.'.xml',  mb_convert_encoding($str,"GBK","UTF-8"));
			
		}
		if($lanid){die("{ok}恭喜，".$cfg["model_name"]."编辑成功！");}else{die("{ok}恭喜，".$cfg["model_name"]."添加成功！");}
	}
	/**
	 * 删除特效
	 * @global type $db
	 * @global type $admin
	 * @param type $lanid
	 */
	function del($lanid)
	{
		global $db,$admin;
		
		$thesql="delete from #@__ad where lanid=".formatnum($lanid,0);
		$db->execute($thesql);
		die("{ok}恭喜，删除成功！");
		
	}
}

//操作引导
$myclass=new myclass();
$lanid=$_GET["lanid"];
switch($_GET["action"])
{
	case "add":
		$admin->adminck("ad_add");
		$myclass->add();
		break;
	case "edit":
		$admin->adminck("ad_edit");
		$myclass->edit($lanid);
		break;
	case "update":
		refPage(2);
		$admin->adminck_ajax("ad_edit");
		$myclass->update();
		break;
	case "del":
		refPage(1);
		$admin->adminck_ajax("ad_del");
		$myclass->del($lanid);
		break;
	default:
		$myclass->def();
}
?>