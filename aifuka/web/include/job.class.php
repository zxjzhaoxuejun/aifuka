<?php
/**
 * 招聘记录，显示类
 *
 * @version        $Id: web.config.php 1 10:33 2010年7月6日Z $
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */

class job extends alzCms
{
	function def()
	{
		global $cfg,$db;
		$cfg["id"]=formatnum($_GET["id"],0);
		$cfg["action"] = $_GET["action"];
		if($cfg["id"]){

			return $this->thedesc();

		}else{
			$cfg["page"]=formatpage($_GET["page"]);
			return $this->thelist();
		}
	}

	/**
	 *
	 * 记录列表
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
		
		//一级栏目ID
		$cfg["current_fid"] = $rs['fid'];
			
		//内页 模板
		$cfg["modelcontent"]=$this->reLabel(FRONT_THEME_DIR.$rs['template_list']);

		//框架模板
		$content = $this->reLabel(FRONT_THEME_DIR.$rs['template_main']);

		if($cfg["web_tohtml"]){
			$pagestr=$cfg["pages"]==1?"":$cfg["page_prefix"].$cfg["page"]."/";
			writetofile(wwwroot.$url.$pagestr.htmlIndex,$content);
			if($cfg["pages"]==$cfg["page"]&&$cfg["pages"]>1)//多页情况，且当前页为最大页，则生成一份不带页码的栏目主页
			{
				writetofile(wwwroot.$url.htmlIndex,$content);
			}
			return $url.$pagestr;
		}else{
			return $content;
		}
	}

	/**
	 *
	 * 模板标签函数
	 * @param $loopstr
	 */
	function joblist($loopstr)
	{
		global $cfg,$db;
		loadlanXml("job","job_");
		$pagesize=$cfg["jobpagesize"];
		$cfg["pagesize"]=$pagesize;
		$beginid=($cfg["page"]-1)*$pagesize;
		$searchsql=$this->getsearsql($cfg["searchtype"],$cfg["searchkeyword"],"job","title,overtime,money,exp,place,content");
		$thesql="select id from #@__job where classid in(".allclassids($cfg["classid"]).") $searchsql and lanstr='".$cfg["lanstr"]."' and title<>''";
		$db->dosql($thesql);
		$cfg["allnums"]=$db->nums();
		
		$limitstr="limit $beginid,$pagesize";
		$thesql="select * from #@__job where classid in(".allclassids($cfg["classid"]).") $searchsql and lanstr='".$cfg["lanstr"]."' and title<>'' order by sortid desc $limitstr";
		
		$i==1;

		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$cfg["time"]=date($cfg["job_formattime{$cfg["lanstr"]}"],$rs["edittime"]);
			if($cfg["searchkeyword"]!=""){
				$newkeyword="<b class='searchedkey'>".$cfg["searchkeyword"]."</b>";
				$cfg["title"]=str_replace($cfg["searchkeyword"],$newkeyword,$rs["title"]);
				$cfg["nums"]=str_replace($cfg["searchkeyword"],$newkeyword,$rs["nums"]);
				$cfg["overtime"]=str_replace($cfg["searchkeyword"],$newkeyword,$rs["overtime"]);
				$cfg["money"]=str_replace($cfg["searchkeyword"],$newkeyword,$rs["money"]);
				$cfg["exp"]=str_replace($cfg["searchkeyword"],$newkeyword,$rs["exp"]);
				$cfg["place"]=str_replace($cfg["searchkeyword"],$newkeyword,$rs["place"]);
			}else{
				$cfg["title"]=$rs["title"];
				$cfg["nums"]=$rs["nums"];
				$cfg["overtime"]=$rs["overtime"];
				$cfg["money"]=$rs["money"];
				$cfg["exp"]=$rs["exp"];

				$cfg["gender"]=$rs["gender"];
				$cfg["age"]=$rs["age"];

				$cfg["place"]=$rs["place"];
				$cfg["content"]=$rs["content"];
			}
			if($i%2==1){
				$cfg["clear"]='<div style="clear:both;"></div>';
			}else{
				$cfg["clear"]="";
			}
			if($cfg["web_tohtml"]){
				$cfg["theurl"]=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
				$temp.=$this->reLabel2($loopstr);
			}else{
				$cfg["theurl"]="?topclassid={$cfg["topclassid"]}&classid={$cfg["classid"]}&id=".$rs["lanid"];
				$temp.=$this->reLabel2($loopstr);
			}
			$i++;
		}
		return $temp;
	}


	/**
	 *
	 * Enter description here ...
	 */
	function thedesc()
	{
		global $cfg,$db;
		loadlanXml("job","job_");
		$thesql="select * from #@__job where lanid=".$cfg["id"]." and lanstr='".$cfg["lanstr"]."'";
		$rs=$db->GetOne($thesql);
		$this->web_seo($rs["titles"],$rs["tag"],$rs["about"]);

		$cfg["classid"]=$rs["classid"];

		if($rs["title"]!=""){
			//获取顶级目录Banner
			$cat = $this->getClassOne($rs["classid"]);
			//一级栏目ID
			$cfg["current_fid"] = $cat['fid'];

			//栏目Banner 图片
			$cfg["classbanner"] = $cat['banner'] ? $cat["banner"] : $this->getTopBanner($cfg["topclassid"]);

			if($cfg["web_tohtml"])
			{
				$pagestr=$cfg["inpage"]==1?"":$cfg["page_prefix"].$cfg["inpage"]."/";
				$url=$cfg["htmlPath"].$rs["paths"].$rs["filename"].$pagestr.htmlIndex;
				if($cfg["inpagecontent"]){//在调此函数前已经给了值.动态时,content的页类分页在此页最上方
					$pageurl=sitepath.$cfg["htmlPath"].$rs["paths"].$rs["filename"];
					$cfg["content"].=$this->htmlpage($pageurl,$cfg["inpages"],$cfg["inpage"],1);
				}
			}
			$cfg["classid"]=$rs["classid"];
			$cfg["title"]=$rs["title"];
			$cfg["nums"]=$rs["nums"];
			$cfg["overtime"]=$rs["overtime"];
			$cfg["money"]=$rs["money"];
			$cfg["exp"]=$rs["exp"];
			$cfg["place"]=$rs["place"];
			$cfg["content"]=$rs["content"];
			$cfg["time"]=date($cfg["job_formattime{$cfg["lanstr"]}"],$rs["edittime"]);
			$cfg["click"]=$rs["click"];


			//$cfg["applyurl"]="<a href='javascript:apply_job(".$cfg["topclassid"].",".$cfg["classid"].",".$rs["lanid"].");'>应聘此职位</a>";
			if($cfg["lanstr"]=="zh_cn")
			{
				$cfg["applyurl"]="<a href='".webpath."job.php?id=".$cfg["id"]."&lanstr=".$cfg["lanstr"]."'>应聘此职位</a>";
			}elseif($cfg["lanstr"]="en")
			{
				$cfg["applyurl"]="<a href='".webpath."job.php?id=".$cfg["id"]."&lanstr=".$cfg["lanstr"]."'>Apply for this position</a>";
			}
			$cfg["classidstr"]=$rs["classidstr"];


			//内页 模板
			$cfg["modelcontent"]=$this->reLabel(FRONT_THEME_DIR.$cat['template_detail']);
			
			
			//框架模板
			$content = $this->reLabel(FRONT_THEME_DIR.$cat['template_main']);

			if($cfg["web_tohtml"]){
				writetofile(wwwroot.$url, $content);
				return $cfg["htmlPath"].$rs["paths"].$cat["filename"];
			}else{
				return $content;
			}
		}
	}


	
}
?>