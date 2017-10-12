<?php
/**
 *
 * 网站栏目内容所采用的模板之 单页模板 处理类。
 * @author guoho
 *
 */
class onepage extends alzCms
{
	function def()
	{
		global $cfg;
		$cfg["inpage"] = formatnum($_GET["inpage"],1);
		return $this->thelist();
	}

	/**
	 *
	 * 单页数据内容表。
	 */
	function thelist()
	{
		global $cfg,$db;
		//分类信息		
		$rs = $this->getClassOne($cfg["classid"]);		
		//栏目Banner 图片		
		
		
		$cfg["classbanner"] = $rs['banner'] ? $rs["banner"] : $this->getTopBanner($cfg["topclassid"]);
		$cfg['classpic'] = $rs['picurl'];	
		if($cfg["web_tohtml"]){
			$url=$cfg["htmlPath"].$rs["paths"];
			$cfg["filename"]=$url;//在替换模块前给分页参数给值
		}
		$cfg["classidstr"]=$rs["classidstr"];		
		$this->web_seo($rs["titles"],$rs["tag"],$rs["about"]);	
		
		//当前栏目的父级栏目ID
		$cfg["current_fid"] = $rs['fid'];

		//框架模板		
		
		

		if($cfg["web_tohtml"])
		{
			$pagestr=$cfg["inpage"]==1?"":$cfg["page_prefix"].$cfg["inpage"]."/";
			$url=$cfg["htmlPath"].$rs["paths"].$pagestr.htmlIndex;
			if($cfg["inpagecontent"]){
				$pageurl=$cfg["htmlPath"].$rs["paths"];
				$cfg["content"].=$this->htmlpage($pageurl,$cfg["inpages"],$cfg["inpage"],1);
				$cfg["content"].='<script type="text/javascript">page('.$cfg["inpages"].',1,0,"inhtmlpageto");</script>';
			}
			$content=$cfg["content"];
		}else{
			$content=$this->format_inpage($rs["content"],$cfg["inpage"]);

		}
		if($content==""){
			//	$content="<div class='norecord'>".lang("norecord")."</div>";
		}else{
			//$content=$this->addTagLink($content,$rs["tag"],"onepage",$rs["lanid"]);
		}
			
		//解析内容模板
		$cfg["modelcontent"]= $this->reHtml($content);
		
		if($cfg["classid"]==298){
			$rs_dsj = $this->getClassOne(300);	
			$rs_about = $this->getClassOne(302);
			$cfg["dsj"]= $this->reHtml($rs_dsj["content"]);
			$cfg["about"]= $this->reHtml($rs_about["content"]);
		
		}
		if($cfg["classid"]==316){
			$rs_dsj = $this->getClassOne(320);	
			$rs_about = $this->getClassOne(318);
			$cfg["dsj"]= $this->reHtml($rs_dsj["content"]);
			$cfg["about"]= $this->reHtml($rs_about["content"]);
		
		}
		
		$template_main = $rs['template_main'];
		$content = $this->reLabel(FRONT_THEME_DIR.$template_main);
		
		
		if($cfg["web_tohtml"]){
			writetofile(wwwroot.$url,$content);
			return $url;
		}else{
			return $content;
		}
	}

	/**
	 *
	 * Enter description here ...
	 */
	function product_index()
	{
		global $cfg,$db;
		$w=150;
		$h=120;
		$thesql="select * from #@__class where fid={$cfg["classid"]} and lanstr='{$cfg["lanstr"]}' order by sortid,lanid";
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$url1="?topclassid={$cfg["topclassid"]}&classid=".$rs["lanid"];
			$url2=$cfg["htmlPath"].$rs["paths"];
			$cfg["url"]= geturl($url1,$url2);
			$cfg["width"]=$w;
			$cfg["height"]=$h;
			$cfg["picurl"]=$rs["picurl"];
			$cfg["classname"]=$rs["classname"];
			$cfg["about"]=$rs["about"];
			$cfg["theclassid"]=$rs["lanid"];
			$temp.=$this->reLabel("product/product_indexs.html");
		}
		return $temp;
	}

	/**
	 *
	 * 生成新的 新的首页
	 */
	function newindex(){
		global $loop,$cfg,$db;
		$thesql="select * from #@__article where classid in (".allclassids($loop["lanid"]).") and commend like '%commend%' and del=0 and locked=0 and lanstr='{$cfg["lanstr"]}' and title<>'' order by sortid,lanid limit 0,5";
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			if($cfg["web_tohtml"]){
				$url=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
			}else{
				$url="?topclassid=".getTopclassid($rs["classidstr"])."&classid=".$rs["classid"]."&id=".$rs["lanid"];
			}
			$filestr=$this->getFileDownImg("articlefile",$rs["file"]);
			$temp.="<p>".$filestr."<a class='thea' href='$url'>".$rs["title"]."</a></p>\r\n";
		}
		return $temp;
	}
}
?>