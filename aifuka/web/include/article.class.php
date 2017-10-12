<?php
/**
 * 文章显示类
 *
 * @version        $Id: web.config.php 1 10:33 2010年7月6日Z $
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */

class article extends alzCms
{
	/**
	 *
	 * 文章显示函数
	 */
	function def()
	{
		global $cfg,$db;
		$cfg["id"]=formatnum($_GET["id"],0);
		if($cfg["id"]){
			$cfg["inpage"]=formatnum($_GET["inpage"],1);
			$thesql="select content from #@__articles where lanid=".$cfg["id"]." and lanstr='".$cfg["lanstr"]."'";

			$cfg["content"]=$db->getValue($thesql,"content");
			$cfg["content"]=$this->format_inpage($cfg["content"],$cfg["inpage"]);
			return $this->thedesc();
		}else{
			$cfg["page"]=formatpage($_GET["page"]);
			return $this->thelist();
		}
	}

	/**
	 *
	 * 文章列表显示函数
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
		//当前栏目的父级栏目ID
		$cfg["current_fid"] = $rs['fid'];
		$this->web_seo($rs["titles"],$rs["tag"],$rs["about"]);	
		
				//内页 模板		
		$cfg["modelcontent"]=$this->reLabel(FRONT_THEME_DIR.$rs['template_list']);

		//框架模板		
		if($cfg["classid"]==222223&&$cfg["classid"]==222223)	{
			$content = $this->reLabel(FRONT_THEME_DIR."main/new.html");
		}elseif($cfg["classid"]==642225&&$cfg["classid"]==622245){
			$content = $this->reLabel(FRONT_THEME_DIR."main/case.html");
		}else{
			$content = $this->reLabel(FRONT_THEME_DIR.$rs['template_main']);
		}
		
		
		if($cfg["web_tohtml"]){
			$pagestr=$cfg["pages"]==1?"":$cfg["page_prefix"].$cfg["page"]."/";
			writetofile(wwwroot.$cfg["htmlPath"].$rs["paths"].$pagestr.htmlIndex,$content);
			if($cfg["page"]==1&&$cfg["pages"]>1)
			{
				writetofile(wwwroot.$cfg["htmlPath"].$rs["paths"].htmlIndex,$content);
			}//多页情况，且当前页为最大页，则生成一份不带页码的栏目主页
			return $url.$pagestr;
		}else{
			return $content;
		}
	}


	/**
	 * 文章列表
	 * 处理 模板标签函数
	 * @param $loopstr
	 */
	function articlelist($loopstr,$valuestr)
	{
		global $cfg,$db;
		
		$title_len = intval($this->getCan($valuestr, "title_len")); //标题截取长度
		$title_len = $title_len>0 ? $title_len : 100;
		
		$brief_len = intval($this->getCan($valuestr, "brief_len")); //内容简介截取长度		
		$brief_len = $brief_len>0 ? $brief_len : 200;
		
		
		$pagesize=$cfg["articlepagesize"];
		
		$cfg["pagesize"]=$pagesize;
		
		$beginid=($cfg["page"]-1)*$pagesize;		
		
		$thesql="select lanid from #@__article where classid in(".allclassids($cfg["classid"]).") and lanstr='".$cfg["lanstr"]."' $addsql and title<>'' and del=0 and locked=0";
		$db->dosql($thesql);
		$cfg["allnums"]=$db->nums();		
		
		$limitstr="limit $beginid,$pagesize";
		
		$thesql="select * from #@__article where classid in(".allclassids($cfg["classid"]).") $searchsql and lanstr='".$cfg["lanstr"]."' $addsql and title<>'' and del=0 and locked=0 order by sortid desc $limitstr";


		$db->dosql($thesql);
		$cfg["article_jslen"]=0;
		$i = 1;
		while($rs=$db->GetArray())
		{
			
			$cfg["titles"]=$rs["title"];
			$cfg["title"] = left($rs["title"],  $title_len);
			$cfg["brief"] = left($rs["brief"],  $brief_len);
			$cfg["brief1"] = $rs["brief"];
			$cfg["file"]=$rs["file"];
			

			if(trim($rs["picurl"])){
				$cfg["picurl"]= webpath.article_imagepath.$rs["picurl"];
			}
			if(trim($rs["bigpic"])){
				$cfg["bigpic"]= webpath.article_imagepath.$rs["bigpic"];
			}
			$cfg['even'] = $i % 2 ==0 ? 'class="graybg"' : '';
			$i++;
			
			$cfg["time"]=date("Y-m-d",$rs["edittime"]);			
			$cfg["filestr"]=$this->getFileDownImg("articlefile",$rs["file"]);
			
			$url1="?topclassid={$cfg["topclassid"]}&classid={$cfg["classid"]}&id=".$rs["lanid"]."&lanstr=".$cfg["lanstr"];
			$url2=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
			$cfg["theurl"]=geturl($url1,$url2);
			
			$temp.=$this->reLabel2($loopstr);
			
		}
		return $temp;
	}



	/**
	 * 相关阅读
	 * 处理 模板标签函数
	 * @param $loopstr
	 */
	function recommandlist($loopstr,$valuestr){
		global $cfg,$db;
		$title_len = intval($this->getCan($valuestr, "title_len")); //标题截取长度
		$title_len = $title_len>0 ? $title_len : 160;
		
		$brief_len = intval($this->getCan($valuestr, "brief_len")); //内容简介截取长度		
		$brief_len = $brief_len>0 ? $brief_len : 200;
		
		$thesql="select recommands from #@__article where lanid=".$cfg["id"]." and lanstr='".$cfg["lanstr"]."'";
		$one =$db->GetOne($thesql);
		if($one['recommands']) {
			$thesql="select * from #@__article where lanid in(".$one['recommands'].") and lanstr='".$cfg["lanstr"]."' and title<>'' and del=0 and locked=0 order by sortid desc";	
			$db->dosql($thesql);
			while($rs=$db->GetArray())
			{	

				
				$cfg["titles"]=$rs["title"];
				$cfg["title"] = left($rs["title"],  $title_len);
				$cfg["brief"]=left($rs["brief"],  $brief_len);

				if(trim($rs["picurl"])){
					$cfg["picurl"]= webpath.article_imagepath.$rs["picurl"];
				}
				$cfg['even'] = $i % 2 ==0 ? 'class="graybg"' : '';
				$i++;
				$cfg["time"]=date("Y-m-d",$rs["edittime"]);
			
				$url1="?topclassid={$cfg["topclassid"]}&classid={$cfg["classid"]}&id=".$rs["lanid"]."&lanstr=".$cfg["lanstr"];
				$url2=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
				$cfg["theurl"]=geturl($url1,$url2);			
				$temp.=$this->reLabel2($loopstr);			
			}
		}
		return $temp;
		
	}


	/**
	 *
	 * 文章内容详情函数
	 */
	function thedesc()
	{
		global $cfg,$db;
		
		$thesql="select * from #@__article where lanid=".$cfg["id"]." and lanstr='".$cfg["lanstr"]."'";
		$rs=$db->GetOne($thesql);
		$this->web_seo($rs["titles"],$rs["tag"],$rs["about"]);
		
		$cfg["classid"] = $rs["classid"];
		
		//访问权限设置
		if($rs['isvip'] == 1  && $_SESSION['front_level']<2 ){
			$this->_halt($this->_lang('nopriv','member'),webpath.'index.php');
		}
		//更新点击数
		if($rs['lanid']) $db->execute("UPDATE #@__article set click=click+1 WHERE lanid=".$rs['lanid']);
		
		//获取顶级目录Banner		
		$cat = $this->getClassOne($cfg["classid"]);		
		//栏目Banner 图片		
		$cfg["classbanner"] = $cat['banner'] ? $cat["banner"] : $this->getTopBanner($cfg["topclassid"]);
		//当前栏目的父级栏目ID
		$cfg["current_fid"] = $cat['fid'];

		if($cfg["web_tohtml"])
		{
			$pagestr=$cfg["inpage"]==1?"":$cfg["page_prefix"].$cfg["inpage"]."/";
			$url=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
			if($cfg["inpagecontent"]){//在调此函数前已经给了值.动态时,content的页类分页在此页最上方
				$pageurl=$cfg["htmlpath"].$rs["paths"].$rs["filename"];
				$cfg["content"].=$this->htmlpage($pageurl,$cfg["inpages"],$cfg["inpage"],1);
			}
		}
		$cfg["content"] = $this->addKeylink($cfg["content"]);
		$cfg['url']  = "?topclassid={$cfg["topclassid"]}&classid={$cfg["classid"]}&id=".$cfg["id"];
		
		$cfg["picurl"] =sitepath."skins/images/product/sol_show.jpg";
		if(trim($rs["picurl"])){
			$uu=substr($rs["picurl"],strrpos($rs["picurl"],'/'));
			$cfg["picurl"]= webpath.article_imagepath."big/".$uu;
		}			
		$cfg["bigpic"] =sitepath."skins/images/product/sol_show.jpg";
		if(trim($rs["bigpic"])){
			$uu=substr($rs["bigpic"],strrpos($rs["bigpic"],'/'));
			$cfg["bigpic"]= webpath.article_imagepath."".$uu;
		}			
		$cfg["title"] = $rs["title"];
		$cfg["brief"] = $rs["brief"];
		$cfg["vurl"] = $rs["vurl"];
		$cfg["author"] = $rs["author"]!=""?"<p>".t("author")."：{$rs["author"]}</p>":"";
		$cfg["source"] = $rs["source"]!=""?"<p>".t("source")."：{$rs["source"]}</p>":"";
		$cfg["date"] = date("Y-m-d",$rs["edittime"]);
		$cfg["click"] = $rs["click"];
		$cfg["filestr"]=$this->getFileDownImg("articlefile",$rs["file"],"filedown");
		if($rs["picurl"]!=""){
			$uu=substr($rs["picurl"],strrpos($rs["picurl"],'/'));
			$img=webpath.article_imagepath."big/".$uu;
			$cfg["img"]="<img src=\"".webpath."images/loading.gif\" onload=\"setImg(this,300,300,'$img');\" class=\"aimg\" />";
		}		
		$cfg["classidstr"]=$rs["classidstr"];
		
			

		//内页 模板 		
		$cfg["modelcontent"]=$this->reLabel(FRONT_THEME_DIR.$cat['template_detail']);

		//框架模板			
		$content = $this->reLabel(FRONT_THEME_DIR.$cat['template_main']);
		

		if($cfg["web_tohtml"]){
			writetofile(wwwroot.$url,$content);
			return $cfg["htmlPath"].$rs["paths"].$rs["filename"];
		}else{
			return $content;
		}
	}

	/**
	 * 
	 * 上一新闻
	 */
	function prev(){
		global $db,$cfg;
		$thesql1="select lanid,title,edittime from #@__article where lanid=".$cfg["id"];
		$rs1=$db->GetOne($thesql1);
		//echo $thesql1."<br />";
		$orderstr=$cfg["article_orderby"];//正常
		$thesql="select * from #@__article where sortid>".$rs1["lanid"]." and lanstr='".$cfg["lanstr"]."' and classid in(".allclassids($cfg["classid"]).") ".$cfg["article_orderby"]." LIMIT 1";
		$rs=$db->GetOne($thesql);
//exit($thesql);
		if($rs){
			$url1="?topclassid={$cfg["topclassid"]}&classid={$cfg["classid"]}&id=".$rs["lanid"];
			$url2=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
			$url=geturl($url1,$url2);
			return '<a href="'.$url.'">'.$rs['title'].'</a>&nbsp;&nbsp;'.date("Y/m/d",$rs["edittime"]);
		}
		else {
			return $this->_lang('nothing','article');
		}

	}
	
	/**
	 * 
	 * 下一新闻
	 */
	function next(){
		global $db,$cfg;

		$orderstr=$cfg["article_orderby"];//正常

		$thesql1="select lanid,title,edittime from #@__article where lanid=".$cfg["id"];
		$rs1=$db->GetOne($thesql1);

		$thesql="select * from #@__article where sortid<".$rs1["lanid"]." and lanstr='".$cfg["lanstr"]."' and classid in(".allclassids($cfg["classid"]).") ".$cfg["article_orderby"]." LIMIT 1";
		$rs=$db->GetOne($thesql);


		if($rs){
			$url1="?topclassid={$cfg["topclassid"]}&classid={$cfg["classid"]}&id=".$rs["lanid"];
			$url2=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
			$url=geturl($url1,$url2);
			return '<a href="'.$url.'">'.$rs['title'].'</a>&nbsp;&nbsp;'.date("Y/m/d",$rs["edittime"]);
		}
		else {
			return $this->_lang('nothing','article');
		}

	}



}
?>