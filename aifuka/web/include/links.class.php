<?php
/**
 * 友情链接记录显示类
 *
 * @version        $Id: web.config.php 1 10:33 2010年7月6日Z $
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */


class links extends alzCms
{
	function def()
	{
		global $cfg;
		$cfg["page"]=formatpage($_GET["page"]);
		return $this->thelist();
	}

	/**
	 *
	 * 友情链接列表
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
		
		//内页 模板		
		$cfg["modelcontent"]=$this->reLabel(FRONT_THEME_DIR.$rs['template_list']);

		//框架模板		
		$content = $this->reLabel(FRONT_THEME_DIR.$rs['template_main']);
		
		
		if($cfg["web_tohtml"]){
			$pagestr=$cfg["pages"]==1?"":$cfg["page_prefix"].$cfg["page"]."/";
			writetofile(wwwroot.$url.$pagestr.htmlIndex,$content);
			if($cfg["pages"]==$cfg["page"]&&$cfg["pages"]>1)
			{//多页情况，且当前页为最大页，则生成一份不带页码的栏目主页
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
	function linkslist($loopstr)
	{
		global $cfg,$db;
		$pagesize=$cfg["linkspagesize"];
		$cfg["pagesize"]=$pagesize;
		$beginid=($cfg["page"]-1)*$pagesize;
		$thesql="select lanid from #@__links where classid in(".allclassids($cfg["classid"]).") and lanstr='".$cfg["lanstr"]."' and title<>''";
		$db->dosql($thesql);
		$cfg["allnums"]=$db->nums();
		if($cfg["web_tohtml"]){
			$orderstr=$cfg["links_orderby2"];//倒序
			if($cfg["pages"]==$cfg["page"]){//如果为最近一页，那么开始ID等于总记录数减每页数，如果小于零，则等于零。以确保最新一页满。
				$beginid=$cfg["allnums"]-$pagesize;
				if($beginid<0)$beginid=0;
			}
			$limitstr="limit $beginid,$pagesize";
		}else{
			
			$limitstr="limit $beginid,$pagesize";
		}
		$thesql="select * from #@__links where classid in(".allclassids($cfg["classid"]).") $searchsql and lanstr='".$cfg["lanstr"]."' and title<>''  order by sortid desc $limitstr";


		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$cfg["title"]=$rs["title"];
			$link=$rs["url"];
			$link=instr($link,"http://")?$link:"http://".$link;
			$cfg["url"]=$link;
			if(instr($rs["logo"],"http://")){
				$cfg["pic"]=$rs["logo"];
			}else{
				$cfg["pic"]=webpath.links_imagepath.$rs["logo"];
			}
			$cfg["brief"]=$rs["content"];
			$temp.=$this->reLabel2($loopstr);
			
		}
		return $temp;
	}	
}
?>