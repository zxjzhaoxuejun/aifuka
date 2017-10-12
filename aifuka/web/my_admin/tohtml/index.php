<?php
/********************************************************
 /时间：2009-10-11

 程序：胡思文
 * 网站静态化操作程序，不需要更改
 ********************************************************/
include("../../include/inc.php");
include(incpath."funlist.php");

class tohtml extends alzCms
{
	function __construct()
	{
		global $cfg,$admin;

		$action=$_GET["action"];
		$lanidstr=$_GET["lanid"];
		$arr=explode("-",$action);
		switch($arr[1])
		{
			case "self"://更新栏目本身
				$cfg["toalllist"]="true";
				$this->toself($lanidstr);
				break;
			case "selfs"://更新栏目本身及文件
				$cfg["toalllist"]="true";
				$this->toselfs($lanidstr);
				break;
			case "all"://更新所有栏目
				$cfg["toalllist"]="true";
				$this->toall($lanidstr);
				break;
			case "alls"://更新所有栏目及文件
				$cfg["toalllist"]="true";
				$this->toalls($lanidstr);
				break;
			case "file":
				$this->tofile($arr[0],$lanidstr);
				break;
			case "list":
				$this->tolist($arr[0],$lanidstr);
				break;
			//对文件栏目与文件。
			case "listandfile":
				$this->tolistandfile($arr[0],$lanidstr);
				break;
			case "toclassdo":
				$this->toclassdo();//栏目更新执行
				break;
			case "tofiledo":
				$sec=$this->tofiledo($arr[0],$lanidstr);//文件更新执行，此处$lanidstr文件的lanid
				die($sec);
				break;
			
			
				
			case "jiaoz"://校正栏目
				$this->jiaoz($lanidstr);
				break;
			case "jiaozs"://校正栏目及子栏目
				$this->jiaozs($lanidstr);
				break;
			case "jiaoz_file"://校正文件
				$this->jiaoz_file($arr[0],$lanidstr);
				break;
			case "jiaoz_do"://栏目校正执行
				$this->jiaoz_do();
				break;
			case "jiaoz_file_do"://文件校正执行
				$this->jiaoz_file_do($arr[0]);
				break;
			case "jiaoz_files"://校正所有文件
				$this->jiaoz_files($arr[0]);
				break;
			case "jiaoz_files_do"://校正所有文件执行
				$this->jiaoz_files_do($arr[0]);
				break;
				//一键更新所有页面
			case "onekeytohtml":
				$this->onekeytohtml($arr[0]);
				break;
			case "tofiledo2"://一键更新时候，配合对应的栏目更新数据
				$this->tofiledo2($lanidstr);//此处$lanidstr为文件的lanid
				break;
			case "toindex":
				$this->toindex();//此处$lanidstr为文件的lanid
				break;
		}
	}



	function onekeytohtml()
	{//一键更新
		global $cfg;
		$cfg["classidstr"]=$this->getallclassidstr(allclassids(0));
		$cfg["pagestr"]=$this->getpagestr($cfg["classidstr"]);
		$cfg["datanumstr"]=$this->getdatanumstr($cfg["classidstr"]);
		echo $this->reLabel(TPL_ADMIN_DIR."tohtml/index_onekey.html");
		
	}

	function toself($lanidstr)
	{//栏目本身
		global $cfg;
		$cfg["classidstr"]=$lanidstr;
		$cfg["pagestr"]=$this->getpagestr($cfg["classidstr"]);
		echo $this->reLabel(TPL_ADMIN_DIR."tohtml/index.html");
	}
	function toselfs($lanidstr)
	{//栏目本身及文件
		global $cfg;
		$cfg["classidstr"]=$lanidstr;
		$cfg["pagestr"]=$this->getpagestr($cfg["classidstr"]);

		$cfg["datanumstr"]=$this->getdatanumstr($cfg["classidstr"]);
		echo $this->reLabel(TPL_ADMIN_DIR."tohtml/index_onekey.html");	
	}

	function toall($lanidstr)
	{//栏目及子栏目
		global $cfg;
		//得到$landistr下的所以id
		$cfg["classidstr"]=$this->getallclassidstr($lanidstr);
		//根据classidstr查询模型设置的分页
		$cfg["pagestr"]=$this->getpagestr($cfg["classidstr"]);
		echo $this->reLabel(TPL_ADMIN_DIR."tohtml/index.html");
	}
	function toalls($lanidstr)
	{//栏目及子栏目及文件
		global $cfg;
		$cfg["classidstr"]=$this->getallclassidstr($lanidstr);
		$cfg["pagestr"]=$this->getpagestr($cfg["classidstr"]);
		$cfg["datanumstr"]=$this->getdatanumstr($cfg["classidstr"]);
		echo $this->reLabel(TPL_ADMIN_DIR."tohtml/index_onekey.html");		
	}

	function tolist($model,$lanidstr)
	{//列表
		global $cfg;
		$cfg["model"]=$model;
		$cfg["classidstr"]=$this->getclassidstr($model,$lanidstr);
		$cfg["classnamestr"]=$this->getclassnamestr($cfg["classidstr"]);
		$cfg["pagestr"]=$this->getpagestr($cfg["classidstr"]);
		echo $this->reLabel(TPL_ADMIN_DIR."tohtml/index.html");
	}

	function tofile($model,$lanidstr)
	{//文件
		global $cfg;
		$cfg["model"]=$model;
		$cfg["lanidstr"]=$lanidstr;
		echo $this->reLabel(TPL_ADMIN_DIR."tohtml/index.html");
	}

	function tolistandfile($model,$lanidstr)
	{//列表和文件
		global $cfg;
		$cfg["model"]=$model;
		$cfg["classidstr"]= $this->getclassidstr($model,$lanidstr);
		$cfg["classnamestr"] = $this->getclassnamestr($cfg["classidstr"]);
		$cfg["pagestr"]=$this->getpagestr($cfg["classidstr"]);
		$cfg["lanidstr"] = $lanidstr;
		echo $this->reLabel(TPL_ADMIN_DIR."tohtml/index.html");
	}

	/**
	 *
	 * 更新首页文件
	 */
	function toindex()
	{
		global $cfg;
		$cfg["web_tohtml"]=1;
		//$this->reLabel("_tohtml.html");
		$cfg["topclassid"]=0;

		define("FRONT_THEME_DIR", TPL_FRONT_DIR.$cfg['front_theme'].'/');

		for($i=0;$i<lannums;$i++)
		{
			$cfg["lanstr"]=$cfg["language"][$i];
			$cfg["htmlPath"]= htmlPath();
			$cfg["page_title"]=$cfg["webname{$cfg["lanstr"]}"];
			$cfg["page_keywords"]=$cfg["webkeywords{$cfg["lanstr"]}"];
			$cfg["page_description"]=delcrlf($cfg["webdescription{$cfg["lanstr"]}"]);
				
			
			
			$content=$this->reLabel(FRONT_THEME_DIR. "index.html");
			if(lannums>1) writetofile(siteroot.$cfg["lanstr"].htmlIndex,$content);
			if($cfg["lanstr"]==deflan2) writetofile(siteroot.htmlIndex,$content);
		}

		/**
		 *  引导页面
		 *  如存在引导页面则生成引导页面
		 */		
		if(file_exists(template.FRONT_THEME_DIR. "first.html")){
			$cfg["page_title"]=$cfg["webnamezh_cn"];
			$cfg["page_keywords"]=$cfg["webkeywordszh_cn"];
			$cfg["page_description"]=delcrlf($cfg["webdescriptionzh_cn"]);			
			$content=$this->reLabel(FRONT_THEME_DIR. "first.html");
			writetofile(siteroot."index.html",$content);
		}
	}
	
	/**
	 * //执行栏目生及栏目分页生成
	 */
	function toclassdo()
	{
		global $cfg,$db;
		$cfg["classid"]=formatnum($_GET["lanid"],0);

		//设置前台模板目录
		define("FRONT_THEME_DIR", TPL_FRONT_DIR.$cfg['front_theme'].'/');
		

		$cfg["pages"] = formatnum($_GET["pages"],1);//此类别总页数
		$cfg["page"] = formatnum($_GET["page"],1);//生成此页
		$thesql="select classname,topclassid,model,paths from #@__class where lanstr='".deflan."' and lanid=".$cfg["classid"];
		$rs=$db->GetOne($thesql);
		$classname=$rs["classname"];
		$model=$rs["model"];
		if($model=="-")die("此栏目不需要更新!");
		$cfg["web_tohtml"]=1;
		$cfg["topclassid"]=$rs["topclassid"];
		$cfg["paths"]=$rs["paths"];
		$cfg["web_model"]="html";
		$cfg["pageto"]="htmlpageto";
		$class=new $model;

		for($i=0;$i<lannums;$i++)
		{
			$cfg["lanstr"]=$cfg["language"][$i];

			$cfg["htmlPath"] = htmlPath();

			if($model=="onepage")
			{//如果是单页面，则要判断内容是否分页
				$cfg["content"]=gets("content","class","lanstr='".$cfg["lanstr"]."' and lanid=".$cfg["classid"]);
				if(instr($cfg["content"],cutpagestr))
				{//如果有页内分页
					$arr=explode(cutpagestr,$cfg["content"]);
					$cfg["inpages"]=count($arr);
					for($ii=0;$ii<$cfg["inpages"];$ii++)
					{
						$cfg["inpagecontent"]=1;
						$cfg["content"]=$arr[$ii];
						$cfg["inpage"]=$ii+1;//用于生成新地址
						$do.=$class->thelist();
					}
				}else{
					$cfg["inpagecontent"]=0;
					$cfg["inpage"]=1;

					$do.=$class->thelist();//会返回文件地址
				}
			}else{
				$cfg["inpagecontent"]=0;
				$cfg["inpage"]=1;
					
				$do.=$class->thelist();//会返回文件地址

			}
		}
		die($classname);
	}
	
	/**
	 *
	 * 执行单个文件生成, 不返回下一个文件， 
	 * @param unknown_type $model
	 * @param unknown_type $id
	 */
	function tofiledo($model,$id)
	{//执行文件生成
		global $cfg,$db;
		$id=formatnum($id,0);

		

		//设置前台模板目录
		define("FRONT_THEME_DIR", TPL_FRONT_DIR.$cfg['front_theme'].'/');

		$cfg["model"]=$model;
		$classidstr = gets("classidstr",$model,"lanid=".$id,0);
		$topclassid = getTopclassid($classidstr);
		$cfg["topclassid"]=$topclassid;
		$cfg["web_tohtml"]=1;
		$cfg["web_model"]="html";
		$cfg["id"]=$id;
		$cfg["inpage"]=1;
		$cfg["inpagecontent"]=false;
		$class=new $model;
		for($i=0;$i<lannums;$i++)
		{
			$cfg["lanstr"]=$cfg["language"][$i];
			$cfg["htmlPath"]=htmlPath();
			if($db->IsTable($model."s")){
				$cfg["content"]=gets("content",$model."s","lanstr='".$cfg["lanstr"]."' and lanid=".$id);
			}
			if(instr($cfg["content"],cutpagestr)){
				$arr=explode(cutpagestr,$cfg["content"]);
				$cfg["inpages"]=count($arr);
				for($ii=0;$ii<$cfg["inpages"];$ii++)
				{
					$cfg["inpagecontent"]=true;
					$cfg["content"]=$arr[$ii];
					$cfg["inpage"]=$ii+1;//用于生成新地址
					$do.=$class->thedesc();
				}
			}else{
				$do.=$class->thedesc();//返回文件地址
			}
			if($i==0)$tohtmlsec=$cfg["title"];
		}
		return "文件 “".$tohtmlsec."” 更新成功 √ ";
	}
	
	
	 
	/**
	 * 获取文件ID，及下一文件 ID， 用于一键更新。
	 */
	function tofiledo2($id)
	{
		global $cfg,$db;
		$id=formatnum($id,0);
		$begin=formatnum($_GET["d"],0)+1;
		$classid=formatnum($_GET["classid"],0);

		//设置前台模板目录
		define("FRONT_THEME_DIR", TPL_FRONT_DIR.$cfg['front_theme'].'/');
		$cfg['actived_home'] = "";

		if($classid==0){
			die($id."classid参数错误！");
		}else{
			$model=gets("model","class","lanid=".$classid);
			$cfg['model'] = $model;
			
			if(!$model)die("model参数错误！");
			if($id==0){
				$id=gets("lanid",$model,"classid=".$classid." and del=0 and locked=0 and lanstr='".deflan."' ".$cfg[$model."_orderby"],0);
			}
			if($id==0){die("ID参数错误");}
			$sec=$this->tofiledo($model,$id);
			
			$thesql="select lanid from #@__$model where classid=$classid and del=0 and locked=0 and lanstr='".deflan."' {$cfg[$model."_orderby"]} limit $begin,1";
			$nextid=$db->getValue($thesql,"lanid");
			die($sec."|".$nextid);
			//die("classid=$classid begin=$begin id=$id nextid=$nextid".$thesql."|".$nextid);
		}
	}
	
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	/**
	 * //通过model和lanidstr得到所有的栏目ID串。
	 */
	 function getclassidstr($model,$lanidstr)
	{
		global $db;
		$thesql="select classid from #@__$model where lanid in($lanidstr) and lanstr='".deflan."'";
		$db->dosql($thesql);
		$temp="";
		while($rs=$db->GetArray())
		{
			$temp.=$rs["classid"].",";
			
		}
		$temp=trim($temp,",");
		$temp=str_replace(",,",",",$temp);
		$arr=explode(",",$temp);
		$arr=array_flip(array_flip($arr));
		$temp=implode(",",$arr);
		return $temp;
	}

	function getallclassidstr($lanidstr)//通过lanidstr得到所有的栏目及子栏目ID串。
	{
		$arr=explode(",",$lanidstr);
		$temp="";
		for($i=0;$i<count($arr);$i++)
		{
			if(is_numeric($arr[$i]))
			{
				$temp.=allclassids($arr[$i]).",";
			}
		}
		$temp=trim($temp,",");
		$arr=explode(",",$temp);
		$arr=array_flip(array_flip($arr));
		$temp=implode(",",$arr);
		return $temp;
	}

	function getclassnamestr($classidstr)//通过栏目ID串得到对应的名称串
	{
		global $db;
		$arr=explode(",",$classidstr);
		$temp="";
		for($i=0;$i<count($arr);$i++){
			if(is_numeric($arr[$i])){
				$thesql="select classname from #@__class where lanstr='".deflan."' and lanid=".$arr[$i];
				$temp.=$db->getValue($thesql,"classname").",";
			}
		}
		$temp=trim($temp,",");
		return $temp;
	}

	function getpagestr($classidstr)//通过栏目ID串得到pagenum串
	{

		$arr=explode(",",$classidstr);
		$temp="";
		for($i=0;$i<count($arr);$i++){
			$temp.=$this->getpagenum($arr[$i]).",";
		}
		$temp=trim($temp,",");
		return $temp;
	}

	function getpagenum($classid)//得到某个类别的pagenum
	{
		global $cfg,$db;
		$model=gets("model","class","lanid=".$classid);
		if(instr($cfg["model_havepage"],$model))
		{
			if(instr($cfg["model_havedel"],$model)){$addsql.=" and del=0 ";}
			if(instr($cfg["model_havelocked"],$model)){$addsql.=" and locked=0 ";}
			$thesql="select lanid from #@__$model where lanstr='".deflan."' and classid in (".allclassids($classid).") ".$addsql;
			$nums=$db->num($thesql);
			$pagesize=formatnum($cfg[$model."pagesize"],$cfg["webpagesize"]);
			$pagenum=ceil($nums/$pagesize);
			if($pagenum<1)$pagenum=1;
			return $pagenum;
		}
		return 1;
	}

	function getdatanumstr($lanidstr)
	{
		global $cfg,$db;
		$arr=explode(",",$lanidstr);
		for($i=0;$i<count($arr);$i++)
		{
			$model=gets("model","class","lanid=".$arr[$i]);
			$num=$this->getdatanum($model,$arr[$i]);
			$temp.=$num.",";
			$cfg["alldatanums"]+=$num;
		}
		return trim($temp,",");
	}

	function getdatanum($model,$classid)
	{
		global $cfg,$db;
		$dbarr=explode("_",$model);
		$model=$dbarr[0];
		if(instr($cfg["model_havefile"],$model))
		{
			if(instr($cfg["model_havedel"],$model)){$addsql.=" and del=0 ";}
			if(instr($cfg["model_havelocked"],$model)){$addsql.=" and locked=0 ";}
			$thesql="select lanid from #@__$model where lanstr='".deflan."' and classid=$classid $addsql";
			return $db->num($thesql);
		}else{return 0;}
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function jiaoz($lanidstr)
	{
		global $cfg,$db;
		$cfg["lanidstr"]=$lanidstr;
		$cfg["classnamestr"]=$this->getclassnamestr($cfg["lanidstr"]);
		echo $this->reLabel(TPL_ADMIN_DIR."tohtml/jiaoz_class.html");
	}

	function jiaozs($lanidstr)
	{
		global $cfg,$db;
		$cfg["lanidstr"]=$this->getallclassidstr($lanidstr);
		$cfg["classnamestr"]=$this->getclassnamestr($cfg["lanidstr"]);
		echo $this->reLabel(TPL_ADMIN_DIR."tohtml/jiaoz_class.html");
	}

	function jiaoz_do()
	{
		global $cfg,$db;
		$id=formatnum($_GET["lanid"],0);
		$this->jiaoz_classone($id);
	}
	function jiaoz_classone($classid)
	{
		global $db;
		doMydb(0);
		$classidstr=getFidstr("class",$classid);
		$topclassid=getTopclassid($classidstr);
		if(instr($classidstr,","))
		{
			$arr=explode(",",$classidstr);
			for($i=0;$i<count($arr);$i++){$paths.=gets("path","class","lanid=".$arr[$i]);}
		}
		if($paths!="")$pathstr=",paths='$paths'";
		$thesql="update #@__class set classidstr='$classidstr',topclassid=$topclassid $pathstr where lanid=".$classid;
		$db->execute($thesql);
		doMydb(1);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function jiaoz_file($model,$lanidstr)
	{
		global $cfg;
		$cfg["model"]=$model;
		$cfg["lanidstr"]=$lanidstr;
		echo $this->reLabel(TPL_ADMIN_DIR."tohtml/jiaoz_file.html");
	}

	function jiaoz_file_do($model)
	{
		global $db;
		$id=formatnum($_GET["lanid"],0);
		$this->jiaoz_onefile($model,$id);
		$title=gets("title",$model,"lanstr='".deflan."' and lanid=".$id);
		die("数据 “".left($title,100)."” 校正成功 √");
	}

	function jiaoz_onefile($model,$id)//校正一条数据
	{
		global $db;
		doMydb(0);
		$classid=gets("classid",$model,"lanid=".$id);
		$this->jiaoz_classone($classid);
		$thesql="select classidstr,paths from #@__class where lanid=$classid and lanstr='".deflan."'";
		$rs=$db->GetOne($thesql);
		$thesql="update #@__$model set classidstr='{$rs["classidstr"]}',paths='{$rs["paths"]}' where lanid=$id";
		$db->execute($thesql);
		doMydb(1);
	}

	function jiaoz_files($model)
	{//校正所有文件，首先给一个最开始的ID，然后通过ajax传到执行函数，成功校正后，再返回下一个ID，直到无返回ID结束。
		global $cfg,$db;
		$cfg["model"]=$model;
		$thesql="select lanid from #@__$model where lanstr='".deflan."' ".$cfg[$model."_orderby"];
		$db->dosql($thesql);
		$rs=$db->GetArray();
		$cfg["nums"]=$db->nums();
		$cfg["id"]=$rs["lanid"];
		echo $this->reLabel(TPL_ADMIN_DIR."tohtml/jiaoz_files.html");
	}

	function jiaoz_files_do($model)
	{//校正一条数据，再传回下一条数据ID,格式：title|id
		global $cfg,$db;
		$id=formatnum($_GET["lanid"],0);
		$this->jiaoz_onefile($model,$id);
		$beginid=formatnum($_GET["j"],0)+1;
		$thesql="select lanid from #@__$model where lanstr='".deflan."' {$cfg[$model."_orderby"]} limit $beginid,1";
		$title=gets("title",$model,"lanstr='".deflan."' and lanid=".$id);
		$nextid=$db->getValue($thesql,"lanid");
		die($title."|".$nextid);
	}
}
$tohtml=new tohtml();
?>