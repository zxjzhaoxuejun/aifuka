<?php
/**
 * 网站离不开系统语言管理
 */
include("../include/inc.php");
include(incpath."funlist.php");
$cfg["models"]=Array("text","article","product","job",'member',"links","feedback","download","page","help");

class language extends alzCms
{
	function __destruct(){$this->admincache2();}
	function __construct(){
		global $admin;
		$admin->adminck("sys_language");
		$this->admincache();
	}
	
	function def(){
		global $db,$cfg;
	
		if($_GET["c"]!=""){
			delcookies("xmltype");
			delcookies("modelname");
			delcookies("orderby");
			delcookies("keyword");
			delcookies("pagesize");
			delcookies("page");
			delcookies("newtag");
			delcookies("newmodel");
			delcookies("newxmltext");
			$xmltype="all";
			$modelname="all";
			$orderby="desc";
			$keyword=lang("inputkeyword");			
			$page=1;
		}else{
			$xmltype=$_GET["xmltype"];
			$modelname=$_GET["modelname"];
			$orderby=$_GET["orderby"];
			$keyword=$_POST["keyword"];
			$page=$_GET["page"];
			$cfg["newmodel"]=G("newmodel",$modelname);
		}		
		//当上次
                if(isset($_GET["modelname"]) && $_GET["modelname"]!= getcookies("modelname")){
                    delcookies("page");
                }
                
		$cfg["xmltype"]=G("xmltype",$xmltype);
		$cfg["modelname"]= G("modelname",$modelname);
		$cfg["orderby"]=G("orderby",$orderby);
		$cfg["keyword"]=G("keyword",$keyword,lang("inputkeyword"));
		$cfg["page"]= G("page",$page,1);
		if($cfg["page"]<1)$cfg["page"]=1;
		$cfg["newtag"]=G("newtag");		
		$cfg["newxmltext"]=G("newxmltext");				
		if($cfg["keyword"]!=""&&$cfg["keyword"]!=lang("inputkeyword"))$cfg["addsql"].=" and tag like '%".$cfg["keyword"]."%'";		
		if($cfg["xmltype"]!=""&&$cfg["xmltype"]!="all")$cfg["addsql"].=" and xmltext=".$cfg["xmltype"];		
		if($cfg["modelname"]!="" &&$cfg["modelname"]!="all") $cfg["addsql"].=" and model='".$cfg["modelname"]."' ";				
		echo $this->reLabel("admin/admin_language.html");
	}
	//获取语言列表
	function languagelist($loopstr){
		global $db,$cfg;
		$pagesize=$cfg["adminpagesize"];
		$cfg["pagesize"]=$pagesize;
		$beginid=($cfg["page"]-1)*$pagesize;
		$thesql="select * from #@__language where lanstr='".lanstr."' ".$cfg["addsql"];
                
		$db->dosql($thesql);
		$cfg["allnums"]=$db->nums();
		$thesql="select * from #@__language where 
		lanstr='".deflan."' ".$cfg["addsql"]." order by model ".$cfg["orderby"].",id ".$cfg["orderby"]." 
		limit $beginid,$pagesize";
               
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$cfg["tag"]=$rs["tag"];
			$cfg["model"]=$rs["model"];
			$cfg["xmltext"]=$rs["xmltext"];
			$cfg["lanid"]=$rs["lanid"];
			$funstr.=$this->reLabel2($loopstr);
		}
		return $funstr;
	}
		
	function languagemore($valuestr){
		global $db,$cfg;
		$id=$this->getCan($valuestr,"id");
		switch ($id)
		{
			case 1:
				for($i=0;$i<lannums;$i++)
				{
					$funstr.="<td>".$cfg["languagename"][$i]."</td>";
				}
				break;
			case 2:
				for($i=0;$i<lannums;$i++)
				{
					$funstr.="<td><textarea class='lancontent newpost_' name='newcontent".$cfg["language"][$i]."'></textarea></td>\r\n";
				}
				break;
			default:
				for($i=0;$i<lannums;$i++)
				{
					$lanid=$cfg["lanid"];
					$lanstr=$cfg["language"][$i];
					$thesql="select content from #@__language where lanid=$lanid and lanstr='".$lanstr."'";
					$rs=$db->GetOne($thesql);
					$funstr.="<td><textarea class='lancontent post_' name='content".$lanid.$lanstr."'>".$rs["content"]."</textarea></td>\r\n";
				}
				break;
		}
		return $funstr;
	}
	
	function modelselect(){
		global $cfg;
		$modelname=$cfg["modelname"];
		$funstr='<select class="" onchange="selectto(this);">';
		$funstr.='<option value="?modelname=all"'.$this->cked("all",$model).'>全部</option>';
		for($i=0;$i<count($cfg["models"]);$i++)
		{
			$m=$cfg["models"][$i];
			$funstr.='<option value="?modelname='.$m.'"'.$this->cked($m,$modelname).'>'.$m.'</option>';
		}
		$funstr.='</select>';
		return $funstr;
	}
	
	function xmltextselect(){
		global $cfg;
		$xmltype=$cfg["xmltype"];
		$funstr='<select onchange="selectto(this);">';
		$funstr.='<option value="?xmltype=all"'.$this->cked("all",$xmltype).'>全部</option>';
		$funstr.='<option value="?xmltype=1"'.$this->cked("1",$xmltype).'>转义</option>';
		$funstr.='<option value="?xmltype=0"'.$this->cked("0",$xmltype).'>直接</option>';
		$funstr.='</select>';
		return $funstr;			
	}
	
	function cked($id,$value){
		global $cfg;
		if($id=="all")
		{
			if($value==$id||$value=="")return "selected='selected'";
		}else{
			if($value==$id)return "selected='selected'";
		}
	}
	
	function update(){
		global $db,$cfg;
		doMydb(0);
		$newtag=post("newtag");
		$newmodel=post("newmodel");
		$newcontent=post("newcontent".deflan);
		
		if($newtag!=""&&$newmodel!=""&&$newcontent!="")//插入新记录
		{
			$newxmltext=formatnum($_POST["newxmltext"],0);
			setcookies("newtag",$newtag);
			setcookies("newmodel",$newmodel);
			setcookies("newxmltext",$newxmltext);
			$newcontent=post("newcontent".deflan);
			$thesql="insert into #@__language 
			(tag,model,xmltext,content,lanstr) values 
			('".$newtag."','".$newmodel."',$newxmltext,'".$newcontent."','".deflan."')";
			$db->execute($thesql);
			$lanid=$db->GetLastID();
			$thesql="update #@__language set lanid=".$lanid." where id=".$lanid;
			$db->execute($thesql);
			for($i=1;$i<lannums;$i++)
			{
				$lan=$cfg["language"][$i];
				$newcontent=post("newcontent".$lan);
				$thesql="insert into #@__language 
				(lanid,tag,model,xmltext,content,lanstr) values 
				($lanid,'".$newtag."','".$newmodel."',$newxmltext,'".$newcontent."','".$lan."')";
				$db->execute($thesql);
			}			
		}
		//更新数据，通过POST过来的lanid查数据库，再循环数据库数据更新参数
		$lanid=$_POST["lanid"];
		if($lanid!="")
		{
			$thesql="select * from #@__language where lanid in(".$lanid.")";
			$db->dosql($thesql);
			while($rs=$db->GetArray())
			{
				$id=$rs["id"];
				$lanstr=$rs["lanstr"];
				$lanid=$rs["lanid"];
				$tag=post("tag".$lanid);
				$model=post("model".$lanid);				
				$xmltext=formatnum($_POST["xmltext".$lanid],0);
				$content=post("content".$lanid.$lanstr);
				$thesql="update #@__language set tag='".$tag."',model='".$model."',xmltext=".$xmltext.",content='".$content."' where id=".$id;
				$db->execute($thesql);				
			}
		}
		doMydb(1);
		die("{ok}恭喜，系统语言更新成功！");
	}
	
	function toxml()
	{
		global $db,$cfg;
		for($m=0;$m<count($cfg["models"]);$m++)
		{
			$model=$cfg["models"][$m];
			for($i=0;$i<lannums;$i++)
			{				
				$lanstr=$cfg["language"][$i];
				$thesql="select * from #@__language where lanstr='".$lanstr."' and model='".$model."'";	
				$db->dosql($thesql);				
				$funstr="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
				$funstr.="<".xmlroot.">\r\n";
				while($rs=$db->GetArray())
				{
					$tag=$rs["tag"];
					$model=$rs["model"];
					$xmltext=$rs["xmltext"];
					$content=$rs["content"];
					if($xmltext)$content="<![CDATA[".$content."]]>";
					$funstr.="\t<".$tag.">".$content."</".$tag.">\r\n";
				}
				$funstr.="</".xmlroot.">";
				writetofile("../lanXml/".$model."/".$lanstr.".xml",$funstr);			
			}
		}
		writeconfigfile();
		writeconfigjs();
		die("{ok}恭喜，lanXml生成成功！");
	}
	
	function ifck($valuestr)
	{
		global $cfg;
		$id=$this->getCan($valuestr,"id");
		$value=$this->getCan($valuestr,"value");
		if($cfg[$id]==$value){
			$str=$this->getCan($valuestr,"str");
			return $str;
		}		
	}
}

$language=new language();
if($_GET["update"]!=""){refPage(1);$language->update();}

if($_GET["dbref"]!=""){
	$language->dbref("language");
	}
if($_GET["toxml"]!="")$language->toxml();
$language->def();
?>