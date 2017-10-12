<?php
/**
 * 网站地图管理 
 */
include("../../include/inc.php");
include(incpath."funlist.php");

class tohtml extends alzCms
{
	function __construct()
	{
		global $cfg,$admin;

		$action=$_GET["action"];
		switch($action)
		{
			case "rss":
				$this->rss();
				break;
			case "rssdo":
				$this->rssdo();
				break;
			case "sitemap":
				$this->sitemap();
				break;
				//更新网站地图
			case "sitemapdo":
				$this->sitemapdo();
				break;
			case "sitemapurl":
				$this->sitemapurl();
				break;
		}
	}
	
	function sitemap(){
		echo $this->reLabel(TPL_ADMIN_DIR."map/index_sitemap.html");
	}
	function sitemapdo()
	{
		$id=$_GET["id"];
		$n=$_GET["n"];
		switch($id)
		{
			case 1:
				$this->sitemapdo_class($n);
				break;
			case 2:
				$this->sitemapdo_file($n);
				break;
			case 3:
				$this->sitemapdo_s($n);
				break;
		}
	}
	function sitemapdo_class($n=0)
	{
		global $cfg,$db;
		$cfg["do"]="class";
		for($i=0;$i<lannums;$i++)
		{
			$lanstr=$cfg["language"][$i];
			$cfg["lanstr"]=$lanstr;
			$thesql="select lanid,topclassid,paths from #@__class where lanstr='$lanstr' and model<>'-' and model<>'goto'";
			$nums=$db->num($thesql);
			$page=ceil($nums/$cfg["sitemappagesize"]);
			if($page==0)$page=1;
			if($n==0){//更新全部
				for($p=1;$p<=$page;$p++)
				{
					$begin=($p-1)*$cfg["sitemappagesize"];
					$cfg["sitemapsql"] = $thesql." limit ".$begin.",".$cfg["sitemappagesize"];
					$content=$this->reLabel(TPL_ADMIN_DIR."map/sitemap.html");
					if($lanstr==deflan2)$lanstr="";
					file_put_contents(siteroot."sitemap/map$lanstr$p.xml",$content);
				}
			}else{//只更新第$page页
				$begin=($page-1)*$cfg["sitemappagesize"];
				$cfg["sitemapsql"]=$thesql." limit ".$begin.",".$cfg["sitemappagesize"];
				$content=$this->reLabel(TPL_ADMIN_DIR."map/sitemap.html");
				if($lanstr==deflan2)$lanstr="";
				file_put_contents(siteroot."sitemap/map$lanstr$page.xml",$content);
			}
		}
		die("栏目Sitemap");
	}
	function sitemapdo_file($n=0)
	{
		global $cfg,$db;
		$cfg["do"]="file";
		for($i=0;$i<lannums;$i++)
		{
			$modelarr=explode("|",$cfg["model_havefile"]);
			$nums=0;
			for($m=0;$m<count($modelarr);$m++){
				$lanstr=$cfg["language"][$i];
				$cfg["lanstr"]=$lanstr;
				$model=$modelarr[$m];
				$thesql="select lanid,classid,classidstr,paths,filename,edittime from #@__$model where lanstr='$lanstr' and del=0 and locked=0";
				$nums=$db->num($thesql);
				$page=ceil($nums/$cfg["sitemappagesize"]);
				if($page==0)$page=1;
				if($n==0){//更新全部
					for($p=1;$p<=$page;$p++)
					{
						$begin=($p-1)*$cfg["sitemappagesize"];
						$cfg["sitemapsql"]=$thesql." limit ".$begin.",".$cfg["sitemappagesize"];
						$content=$this->reLabel(TPL_ADMIN_DIR."map/sitemap.html");
						if($lanstr==deflan2)$lanstr="";
						file_put_contents(siteroot."sitemap/$model$lanstr$p.xml",$content);
					}
				}else{//只更新第$page页
					$begin=($page-1)*$cfg["sitemappagesize"];
					$cfg["sitemapsql"]=$thesql." limit ".$begin.",".$cfg["sitemappagesize"];
					$content=$this->reLabel(TPL_ADMIN_DIR."map/sitemap.html");
					if($lanstr==deflan2)$lanstr="";
					file_put_contents(siteroot."sitemap/$model$lanstr$page.xml",$content);
				}
			}
		}
		die("文件Sitemap");
	}
	
	function maplist($loopstr)
	{
		global $cfg,$db;
		$db->dosql($cfg["sitemapsql"]);
		while ($rs=$db->GetArray())
		{
			if($cfg["do"]=="class"){
			
				$cfg["loc"]="http://".$_SERVER['HTTP_HOST'].sitepath."web/?topclassid=".$rs["topclassid"]."&amp;classid=".$rs["lanid"]."&amp;lanstr=".$cfg["lanstr"];
			
				$cfg["loc"]="http://".$_SERVER['HTTP_HOST'].htmlPath().$rs["paths"];
			
			}
			if($cfg["do"]=="file"){
				
					$cfg["loc"]="http://".$_SERVER['HTTP_HOST'].sitepath."web/?topclassid=".gettopclassid($rs["classidstr"])."&amp;classid=".$rs["classid"]."&amp;id=".$rs["lanid"]."&amp;lanstr=".$cfg["lanstr"];
				
					$cfg["loc"]="http://".$_SERVER['HTTP_HOST'].htmlPath().$rs["paths"].$rs["filename"];
				
			}
			$cfg["lastmod"]=date("Y-m-d h:i:s",$rs["edittime"]);
			$cfg["changefreq"]="monthly";
			$cfg["priority"]="0.8";
			$temp.=$this->reLabel2($loopstr);
		}
		return $temp;
	}
	function sitemapdo_s($n=0)
	{
		global $cfg;
		$sitemappath= siteroot."sitemap";
		if($handle=opendir($sitemappath)){
			$i=0;
			while(false!==($file=readdir($handle))){
				if($file!='.'&&$file!=='..'){if(instr($file,".xml")){
					$time=date("Y-m-d H:i:s",filemtime($sitemappath."/".$file));
					$temp[$i]="http://".$_SERVER['HTTP_HOST']."/sitemap/".$file."=+=".$time;}
					$i++;
				}
			}
			closedir($handle);
		}
		$nums=count($temp);
		$page=ceil($nums/$cfg["sitemappagesize"]);
		if($page==0)$page=1;
		if($n==0){//更新全部
			for($p=1;$p<=$page;$p++)
			{
				$p2=$p==1?"":$p;
				$cfg["sitemaparr"]=$temp;
				$cfg["sitemapbegin"]=($p-1)*$cfg["sitemappagesize"];
				$content=$this->reLabel(TPL_ADMIN_DIR."map/sitemaplist.html");
				file_put_contents(siteroot."sitemap$p2.xml",$content);
			}
		}else{//只更新第$page页
			$p2=$page==1?"":$page;
			$cfg["sitemaparr"]=$temp;
			$cfg["sitemapbegin"]=($page-1)*$cfg["sitemappagesize"];
			$content=$this->reLabel(TPL_ADMIN_DIR."map/sitemaplist.html");
			file_put_contents(siteroot."sitemap$p2.xml",$content);
		}
		die("索引Sitemap");
	}
	function sitemaps($loopstr)
	{
		global $cfg;
		$n=count($cfg["sitemaparr"]);
		$m=$cfg["sitemapbegin"]+$cfg["sitemappagesize"];
		$num=$n>$m?$m:$n;
		for($i=$cfg["sitemapbegin"];$i<$num;$i++)
		{
			$s=explode("=+=",$cfg["sitemaparr"][$i]);
			$cfg["loc"]=$s[0];
			$cfg["lastmod"]=$s[1];
			$temp.=$this->reLabel2($loopstr);
		}
		return $temp;
	}
	
	function rss(){
		global $cfg;
		for($i=0;$i<lannums;$i++)
		{
			$lanstr=$cfg["language"][$i];			
			if($lanstr==deflan2)$lanstr="";
			$rss="rss".$lanstr.".xml";
			$url=sitepath.$rss;
			$cfg["rssurl"].="【<a href='$url' target='_blank'>$rss</a>】 ";
		}
		echo $this->reLabel(TPL_ADMIN_DIR."map/index_rss.html");
	}	
	function rssdo()
	{
		global $cfg;
		for($i=0;$i<lannums;$i++)
		{
			$lanstr=$cfg["language"][$i];
			$cfg["lanstr"]=$lanstr;
			$cfg["webname"]=$cfg["webname".$lanstr];
			$cfg["webdescription"]=$cfg["webdescription".$lanstr];
			$cfg["weburl"]=$cfg["weburl".$lanstr];
			$cfg["company"]=$cfg["company".$lanstr];
			$content=$this->reLabel(TPL_ADMIN_DIR."map/rss.html");
			if($lanstr==deflan2)$lanstr="";
			file_put_contents(siteroot."rss$lanstr.xml",$content);
		}
	}	
	function rsslist($loopstr)
	{
		global $cfg,$db;
		$lanstr=$cfg["lanstr"];		
		$thesql="select * from #@__article where lanstr='$lanstr' and title<>'' and del=0 and locked=0 ".$cfg["article_orderby2"]." limit 0,".$cfg["rsssize"];
		$db->dosql($thesql);
		$weburl="http://".$_SERVER["HTTP_HOST"];
		while($rs=$db->GetArray())
		{
			$class=$db->GetOne("select classname,paths,topclassid from #@__class where lanid={$rs["classid"]} and lanstr='$lanstr'");
			$cfg["title"]=$rs["title"];
			$cfg["classname"]=$class["classname"];			
			
			$cfg["url"]=$weburl.sitepath."web/?topclassid=".$class["topclassid"]."&amp;classid=".$rs["classid"]."&amp;id=".$rs["lanid"]."&amp;lanstr=".$lanstr;
			$cfg["classurl"]=$weburl.sitepath."web/?topclassid=".$class["topclassid"]."&amp;classid=".$rs["classid"]."&amp;lanstr=".$lanstr;
			
			$cfg["url"]=$weburl.htmlPath().$rs["paths"].$rs["filename"];
			$cfg["classurl"]=$weburl.htmlPath().$class["paths"];
			
			$cfg["author"]=$rs["author"];
			$cfg["source"]=$rs["source"];
			$cfg["time"]=date("Y-m-d h:i:s",$rs["addtime"]);
			$cfg["about"]=$rs["about"];
			$temp.=$this->reLabel2($loopstr)."\r\n";
		}
		return $temp;
	}
	
	function sitemapurl(){
		if($handle=opendir(siteroot)){
			while(false!==($file=readdir($handle))){
				if($file!='.'&&$file!=='..'){
					if(instr($file,"sitemap")){
						$url="http://".$_SERVER['HTTP_HOST'].sitepath.$file;
						$temp.="<a href='$url' target='_blank'>$url</a><br />";
					}
				}
			}
			closedir($handle);
		}
		die($temp);
	}
}
$tohtml=new tohtml();
?>