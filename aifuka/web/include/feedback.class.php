<?php
/**
 * 程序配置参数文件
 *
 * @version        $Id: web.config.php 1 10:33 2010年7月6日Z $
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */

/********************************************************
 时间：2009-10-26

 程序：胡思文
 ********************************************************/
/**
 *
 * Enter description here ...
 * @author guoho
 *
 */
class feedback extends alzCms
{
	/**
	 *
	 * Enter description here ...
	 */
	function def()
	{
		global $cfg;
		$cfg["page"]=formatpage($_GET["page"]);
		
		return $this->thelist();
	}

	/**
	 *
	 * Enter description here ...
	 */
	function thelist()
	{
		global $cfg,$db;
		loadlanxml("feedback","feedback_");
		
		//分类信息		
		$rs = $this->getClassOne($cfg["classid"]);
		
		//栏目Banner 图片		
		$cfg["classbanner"] = $rs['banner'] ? $rs["banner"] : $this->getTopBanner($cfg["topclassid"]);
		$cfg['classpic'] = $rs['picurl'];
		
		$url=$cfg["htmlPath"].$rs["paths"];
		$cfg["datatable"]="feedback";

		if($cfg["web_tohtml"]){
			$url=$cfg["htmlPath"].$rs["paths"];
			$cfg["filename"]=$url;//在替换模块前给分页参数给值
		}
		$cfg["classidstr"]=$rs["classidstr"];
		$this->web_seo($rs["titles"],$rs["tag"],$rs["about"]);	
		$cfg["chanpin"]= $this->reHtml($_GET["cp"]);
		
				//内页 模板		
		$cfg["modelcontent"]=$this->reLabel(FRONT_THEME_DIR.$rs['template_list']);

		//框架模板		
		$content = $this->reLabel(FRONT_THEME_DIR.$rs['template_main']);
		
		
		if($cfg["web_tohtml"]){
			writetofile(wwwroot.$url.htmlIndex,$content);
			return $url.$pagestr;
		}else{
			return $content;
		}
	}
	/**
	 *
	 * Enter description here ...
	 * @param $loopstr
	 */
	function feedbacklist($loopstr)
	{
		global $cfg,$db;
		$pagesize=$cfg["feedbackpagesize"];
		$cfg["pagesize"]=$pagesize;
		$beginid=($cfg["page"]-1)*$pagesize;
		$lanstrIo=$cfg["feedback_lanstr_io"]?" and lanstr='{$cfg["lanstr"]}' ":"";
		$thesql="select id from #@__feedback where locked=0 and recontent is not null and classid in(".allclassids($cfg["classid"]).") $lanstrIo";
		$db->dosql($thesql);
		$cfg["allnums"]=$db->nums();
			
		$orderstr="order by addtime desc,id desc";
		$limitstr="limit $beginid,$pagesize";
		$thesql="select * from #@__feedback where locked=0 and recontent is not null and classid in(".allclassids($cfg["classid"]).") $lanstrIo $orderstr $limitstr";
		//echo $thesql;
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{
			$cfg["username"]=$rs["username"];
			$cfg["title"]=$rs["title"];
			$cfg["time"]=date("Y-m-d",$rs["addtime"]);
			$cfg["content"]=$rs["content"];
			$cfg["recontent"]=$rs["recontent"]!=""?"<div class='recontent'><b class='text'>".t(1)."</b>".$rs["recontent"]."</div>":"";
			$temp.=$this->reLabel2($loopstr);
		}
		return $temp;
	}
}
?>