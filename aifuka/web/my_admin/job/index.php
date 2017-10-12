<?php
/********************************************************
时间：2009-10-2

程序：胡思文
********************************************************/
include("../../include/inc.php");
include(incpath."funlist.php");

class myclass extends alzCms
{
	function __destruct(){$this->admincache2();}
	function __construct()
	{
		global $cfg,$admin;		
		$this->admincache();
		loadlanXml("job","job_");
		if($_GET["c"]!=""){
			setcookies("classid",0);
			setcookies("page",1);
			setcookies("commend","");
			setcookies("del",0);
			setcookies("searchtype","");
			setcookies("keyword","");
			$cfg["classid"]=formatnum($_GET["classid"],0);
			$cfg["page"]=formatnum($_GET["page"],1);
			$cfg["commendstr"]=$_GET["commend"];
			$cfg["del"]=formatnum($_GET["del"],0);
			$cfg["searchtype"]=$_GET["searchtype"];
			$cfg["keyword"]=$_GET["keyword"];
		}else{
			$cfg["classid"]=G("classid",$_GET["classid"]);
			$cfg["page"]=G("page",$_GET["page"],1);
			$cfg["commendstr"]=G("commend",$_GET["commend"]);
			$cfg["del"]=G("del",$_GET["del"],0);
			$cfg["searchtype"]=G("searchtype",$_GET["searchtype"]);
			$cfg["keyword"]=G("keyword",$_GET["keyword"]);
		}
		define("model","job");
		define("modelpath",TPL_ADMIN_DIR."job/");
		define("model_imagepath",job_imagepath);
		define("models","jobs");
		$cfg["model_name"]=t("title");
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
		$addsql=" where lanstr='".lanstr."'";
		if($cfg["commendstr"]!=""){
			$addsql.=" and commend like '%".$cfg["commendstr"]."%' ";
		}elseif($cfg["del"]){
			$addsql.=" and del=1 ";
		}else{
			$classidsql=$cfg["classid"]!=0?" and classid in(".allclassids($cfg["classid"]).") ":"";
			$addsql.=$classidsql." and del=0 ";	
		}
		$addsql.=$this->getsearsql_job();
		$numsql="select * from #@__".model.$addsql;
		$cfg["allnums"]=$db->num($numsql,"allnum");
		$thesql="select * from #@__".model.$addsql." order by sortid desc limit $beginid,$pagesize";
		$db->dosql($thesql);		
		if($cfg["commend_".model."_key"]!=""){
			$name=explode("|",$cfg["commend_".model."_name".$cfg["lanstr"]]);
			for($i=0;$i<count($name);$i++){$str.=" ".$name[$i]." ";}
			$cfg["commendtitle"]="<td class='cellc'>$str</td>";
		}
		while($rs=$db->GetArray())
		{
			$cfg["classname"]=gets("classname","class","lanstr='".lanstr."' and lanid=".$rs["classid"]);
			$cfg["lanid"]=$rs["lanid"];
			$cfg["sortid"]=$rs["sortid"];
			if($cfg["keyword"]!=""){
				$cfg["title"]=str_replace($cfg["keyword"],"<b class=red>".$cfg["keyword"]."</b>",left($rs["title"],50));
			}else{
				$cfg["title"]=left($rs["title"],$cfg["titlecutnum"]);
			}
			$cfg["time"]=date($cfg["job_formattime".$cfg["lanstr"]],$rs["edittime"]);
			$cfg["nums"]=$rs["nums"];
			$cfg["overtime"]=$rs["overtime"];
			$cfg["money"]=$rs["money"];
			$cfg["exp"]=$rs["exp"];
			$cfg["place"]=$rs["place"];
			$cfg["commend"]=$rs["commend"];
			$cfg["paths"]=$rs["paths"];
			$topclassid=gettopclassid($rs["classidstr"]);
			$cfg["theurl"]="../../?topclassid={$topclassid}&classid={$rs["classid"]}&id={$rs["lanid"]}";
			$cfg["fileurl"]=htmlPath().$rs["paths"].$rs["filename"];
			if(file_exists(wwwroot.$cfg["fileurl"])&&$rs["filename"]!=""){
				$cfg["filelink"]="<a href='".$cfg["fileurl"]."' target='_blank' title='浏览'><img src='".skinspath."htmlfile.gif' /></a>";
			}else{
				$cfg["filelink"]="<img src='".skinspath."tip.gif' alt='未生成' />";
			}
			$funstr.=$this->reLabel2($loopstr);
		}
		return $funstr;
	}
	
	function add()
	{
		global $cfg,$db;
		$cfg["title"]=guid_str."添加".$cfg["model_name"];
		$cfg["click"]=0;
		$cfg["ip"]=GetIp();
                
               
		echo $this->reLabel(modelpath."form.html");
                exit;
	}
	
	function edit($lanid)
	{
		global $cfg,$db,$admin;
		$cfg["lanid"]=$lanid;
		$thesql="select * from #@__".model." where lanid=".$lanid;
		$db->dosql($thesql);
		while($rs=$db->GetArray())//通过lanid访问对应数据
		{
			if($rs["lanstr"]==deflan){//共同数据部分
				$cfg["classid"]=$rs["classid"];	
				$cfg["edittime"]=$rs["edittime"];
				$cfg["filenamestr"]=$rs["paths"].$rs["filename"];
				$cfg["addtime"]=$rs["addtime"];
			}
			//独立数据部分
			$cfg["title".$rs["lanstr"]]=$rs["title"];
			$cfg["nums".$rs["lanstr"]]=$rs["nums"];
			$cfg["overtime".$rs["lanstr"]]=$rs["overtime"];
			$cfg["money".$rs["lanstr"]]=$rs["money"];
			$cfg["exp".$rs["lanstr"]]=$rs["exp"];
                        $cfg["gender".$rs["lanstr"]]=$rs["gender"];
			$cfg["age".$rs["lanstr"]]=$rs["age"];
			$cfg["place".$rs["lanstr"]]=$rs["place"];
			$cfg["titles".$rs["lanstr"]]=$rs["titles"];			
			$cfg["tag".$rs["lanstr"]]=$rs["tag"];
			$cfg["about".$rs["lanstr"]]=$rs["about"];
			$cfg["content".$rs["lanstr"]]=$rs["content"];
			$cfg["click".$rs["lanstr"]]=$rs["click"];
		}
		$cfg["title"]=guid_str.$cfg["model_name"]."编辑";
		echo $this->reLabel(modelpath."form.html");
	}	
	
	function update()
	{
		global $cfg,$db,$admin;
		$now=time();
		$lanid=formatnum($_POST["lanid"],0);
		$classid=formatnum($_POST["classid"],0);
		if($classid==0)die("{err}请确定栏目是否存在！");
		$rs=$db->GetOne("select classidstr,paths,model from #@__class where paths<>'' and lanid=".$classid);
		if(!instr($rs["model"],"job"))die("{err}错误：当前栏目所在模型【{$rs["model"]}】内不可以添加资料！");
		$classidstr=$rs["classidstr"];
		$paths=$rs["paths"];
		$newlanid=0;
		doMydb(0);
		for($i=0;$i<lannums;$i++)
		{
			$lanstr=$cfg["language"][$i];
			$lanpath=$cfg["languagepath"][$i];
			$title=post("title".$lanstr);			
			
			$titles=post("titles".$lanstr);
			if($titles=="")$titles=$title;
			
			$tag=post("tag".$lanstr);
			$about=post("about".$lanstr);
			$content=post("content".$lanstr);
			$tag=$this->seo_tag($tag,$content);
			$nums=$_POST["nums".$lanstr];
			$overtime=$_POST["overtime".$lanstr];
			$money=$_POST["money".$lanstr];
			$exp=$_POST["exp".$lanstr];
                        
                        $age=$_POST["age".$lanstr];
			$gender=$_POST["gender".$lanstr];
                        
			$place=$_POST["place".$lanstr];
			$click=formatnum($_POST["click".$lanstr],0);
			if($lanid){
				$addtime=$_POST["addtime"];
				$filename=$this->getFilename($lanid,$title,$addtime);
				if($i==0)
				{
					$filenameck="select id from #@__".model." where filename='".$filename."' and lanstr='".$lanstr."' and lanid<>$lanid and classid=".$classid;
					if($db->num($filenameck))$filename.=$lanid."/";
				}
				$thesql="update #@__".model." set
				classid=$classid,
				classidstr='".$classidstr."',
				paths='".$paths."',
				filename='".$filename."',
				title='".$title."',
				nums='".$nums."',
				overtime='".$overtime."',
				money='".$money."',
				exp='".$exp."',
                                gender='".$gender."',
				age='".$age."',
				place='".$place."',
				content='".$content."',	
				titles='".$titles."',			
				tag='".$tag."',
				about='".$about."',
				click=$click,
				edittime=$now
				where lanstr='".$lanstr."' and lanid=".$lanid;
				$db->execute($thesql);
				$oldpaths=wwwroot.$lanpath.$_POST["filenamestr"];
				$nowfilestr=wwwroot.$lanpath.$paths.$filename;
				if($oldpaths!=$nowfilestr&&file_exists($oldpaths)){delfile($oldpaths);}
			}else{
				$filename=$this->getFilename($newlanid,$title,$now);
				if($i==0){
					$filenameck="select id from #@__".model." where filename='".$filename."' and lanstr='".$lanstr."' and classid=".$classid;
					$cknum=$db->num($filenameck);
				}
				$thesql="insert into #@__".model." (
				lanid,lanstr,sortid,classid,classidstr,title,paths,filename,content,tag,about,addtime,edittime,click,nums,overtime,money,exp,gender,age,place
				) values (
				$newlanid,'".$lanstr."',$newlanid,$classid,'".$classidstr."','".$title."','".$paths."','".$filename."',
				'".$content."','".$tag."','".$about."',$now,$now,$click,'".$nums."','".$overtime."','".$money."','".$exp."','".$gender."','".$age."','".$place."'
				)";
				$db->execute($thesql);
				if($i==0){
					$newlanid=$db->GetLastID();
					$filename=str_replace("{ID}",$newlanid,$filename);
					if($cknum)$filename.=$newlanid."/";
					$thesql2="update #@__".model." set lanid=$newlanid,sortid=$newlanid,filename='".$filename."' where id=".$newlanid;
					$db->execute($thesql2);
				}
			}
		}
		doMydb(1);
		if($lanid){die("{ok}恭喜，".$cfg["model_name"]."编辑成功！");}else{die("{ok}恭喜，".$cfg["model_name"]."添加成功！");}
	}
	
	function getsearsql_job()
	{
		global $cfg,$db;
		if($cfg["keyword"]=="")return;
		if($cfg["searchtype"]=="")return;
		return " and ".$cfg["searchtype"]." like '%".$cfg["keyword"]."%' ";
	}
	
	function read($lanid)
	{
		global $cfg,$db;
		$lanid=$_GET["lanid"];
		$thesql="select lanstr,content from #@__".model." where lanid=".$lanid;
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$cfg["content".$rs["lanstr"]]=$rs["content"];
		}
		echo $this->reLabel(modelpath."read.html");
	}
	
	
	function admindo($lanidstr)
	{
	global $db;
		$str=$_GET["str"];
		$fun=$_GET["fun"];
		switch ($fun)
		{
			case "dels":
				$thesql = "delete from #@__job where lanid in (" . $lanidstr . ")";
                $db->execute($thesql);
				
//				$this->mydid($lanidstr,model,"del=1");
				break;
			case "re":
				$this->mydid($lanidstr,model,"del=0");
				break;
			case "reall":
				$this->mydidall(model,"del=0");
				break;
			case "deltrue":
				$this->deltrue(model,$lanidstr);
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
	case "add":
		$admin->adminck("job_add");
		$myclass->add();           
		break;
	case "edit":
		$admin->adminck("job_edit");	
		$myclass->edit($lanid);
		break;	
	case "update":
		refPage(2);
		$admin->adminck_ajax("job_edit");
		$myclass->update();
		break;
	case "sort":
		refPage(1);
		$admin->adminck_ajax("job_edit");		
		$myclass->sort(model);
		break;
	case "dbref":
		$myclass->dbref(model,models);
		break;
	case "commenddo":
		refPage(1);
		$myclass->commenddo(model,$lanid);
		break;
	case "guid":
		$myclass->guid(model);
		break;
	case "read":
		$myclass->read($lanid);
		break;
	case "admindo":
		refPage(1);
		$admin->adminck_ajax("job_del");
		$myclass->admindo($lanid);
		break;
	default:
		$myclass->def();
}
?>