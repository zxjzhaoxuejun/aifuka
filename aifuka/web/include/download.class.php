<?php
/**
 * 下载内容显示类文件
 *
 * @version        $Id: web.config.php 1 10:33 2010年7月6日Z $
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */

/********************************************************
 时间：2009-10-6

 程序：胡思文
 ********************************************************/
/**
 *
 * Enter description here ...
 * @author guoho
 *
 */
class download extends alzCms
{
	/**
	 *
	 * 显示操作
	 */
	function def()
	{
		global $cfg;
		$cfg["page"]=formatpage($_GET["page"]);
		return $this->thelist();
	}

	/**
	 *
	 * 下载内容列表显示。
	 */
	function thelist()
	{
		global $cfg,$db;
		loadLanxml("download","download_");
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
	 * 对应循环模板标签函数
	 * Enter description here ...
	 * @param $loopstr
	 */
	function downloadlist($loopstr,$valuestr){
		global $cfg,$db;
		$title_len = intval($this->getCan($valuestr, "title_len")); //标题截取长度
		$title_len = $title_len>0 ? $title_len : 160;
		
		$content_len = intval($this->getCan($valuestr, "content_len")); //标题截取长度
		$content_len = $content_len>0 ? $content_len : 160;
		
		$pagesize = $cfg["downloadpagesize"];
		$cfg["pagesize"] = $pagesize;
		$beginid= ($cfg["page"]-1) * $pagesize;
		$thesql = "select lanid from #@__download where classid in(".allclassids($cfg["classid"]).") and lanstr='".$cfg["lanstr"]."' and title<>''";
		$db->dosql($thesql);
		$cfg["allnums"] = $db->nums();
		
		$limitstr="limit $beginid,$pagesize";
		$thesql="select * from #@__download where classid in(".allclassids($cfg["classid"]).") $searchsql and lanstr='".$cfg["lanstr"]."' and title<>'' order by sortid desc $limitstr";
		

		$db->dosql($thesql);
		$i = 1;
		while($rs=$db->GetArray())
		{
			$cfg['even'] = $i % 2 ==0 ? 'class="graybg"' : '';
			$i++;
			
			$cfg["titles"]=$rs["title"];
			$cfg["title"] = left($rs["title"],  $title_len);
			
			if(instr($rs["url"],"http://")){
				$cfg["url"]=$rs["url"];
			}else{
				$cfg["url"]=webpath.download_imagepath.$rs["url"];
			}
			if(instr($rs["picurl"],"http://")){
				$cfg["picurl"]=$rs["picurl"];
			}else{
				$cfg["picurl"]=webpath.download_imagepath.$rs["picurl"];
			}
			$cfg["time"]=date("Y-m-d",$rs["addtime"]);
			$cfg["size"]=$rs["size"];
			$cfg["software_version"]=$rs["software_version"];
			$cfg["type"]=$rs["type"];
			$cfg["content"]=left($rs["content"],$content_len);
			$temp.=$this->reLabel2($loopstr);
			
		}
		return $temp;
	}
}
?>