<?php
/**
 *
 * 产品信息控制器类
 * @author guoho
 *
 */
class product extends alzCms
{
	/**
	 *
	 * 显示产品详情或产品列表信息
	 */
	function def()
	{
		global $cfg,$db;
		$cfg["id"] = formatnum($_GET["id"],0);
		if($cfg["id"]){
			$cfg["inpage"]=formatnum($_GET["inpage"],1);
			$thesql="select content from #@__products where lanid=".$cfg["id"]." and lanstr='".$cfg["lanstr"]."'";
			$cfg["content"]=$db->getValue($thesql,"content");
			$cfg["content"]=$this->format_inpage($cfg["content"],$cfg["inpage"]);
			return $this->thedesc();
		}else{
			$cfg["page"] = formatpage($_GET["page"]);
			return $this->thelist();
		}

	}

	/**
	 *
	 * 显示产品列表
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
		
		//内页 模板		
		$cfg["modelcontent"]=$this->reLabel(FRONT_THEME_DIR.$rs['template_list']);
		//框架模板		
		
		$content = $this->reLabel(FRONT_THEME_DIR.$rs['template_main']);
		/*if($cfg["classid"]==515&&$cfg["topclassid"]==515){
			$content = $this->reLabel(FRONT_THEME_DIR."main/procate.html");
		}else{
			$content = $this->reLabel(FRONT_THEME_DIR.$rs['template_main']);
		}*/
		if($cfg["web_tohtml"]){
			$pagestr=$cfg["pages"]==1?"":$cfg["page_prefix"].$cfg["page"]."/";
			writetofile(wwwroot.$url.$pagestr.htmlIndex,$content);
			if($cfg["page"]==1&&$cfg["pages"]>1)
			{
				writetofile(wwwroot.$url.htmlIndex,$content);
			}
			//return $url.$pagestr;
		}else{
			return $content;
		}
	}

	/**
	 *
	 * 模板标签函数
	 * @param $loopstr
	 */
	function prolist($loopstr,$valuestr)
	{
		global $cfg,$db;
		$title_len = intval($this->getCan($valuestr, "title_len")); //标题截取长度
		$title_len = $title_len>0 ? $title_len : 14;
		
		$brief_len = intval($this->getCan($valuestr, "brief_len")); //内容简介截取长度		
		$brief_len = $brief_len>0 ? $brief_len : 200;
		
		$uu="";
		if(!empty($_GET["tiaojian"])){
			switch($_GET["tiaojian"]) {
				case 1:
					$uu = " and commend='commend'";
					break;
				case 2:
					$uu = " and is_new=1";
					break;
				default:
					$uu="";
					break;
			}
		}
		
		$pagesize = $cfg["pagesize"] = $cfg["productpagesize"];
		$beginid=($cfg["page"]-1)*$pagesize;
		
		$thesql="select lanid from #@__product where classid in(".allclassids($cfg["classid"]).") and lanstr='".$cfg["lanstr"]."' and title<>'' and del=0 and locked=0".$uu;
		$db->dosql($thesql);
		$cfg["allnums"]=$db->nums();

		$limitstr="limit $beginid,$pagesize";
		
		$thesql="select * from #@__product where classid in(".allclassids($cfg["classid"]).") $searchsql and lanstr='".$cfg["lanstr"]."' and title<>'' and del=0 and locked=0 ".$uu." order by sortid desc $limitstr";
		//exit($thesql);
		
		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{

			$cfg["titles" ]= $rs["title"];
			$cfg["product_sn" ]= $rs["product_sn"];
			$cfg["title"] = left($rs["title"],  $title_len);
			$cfg["brief"]=left(strip_tags($rs["brief"]),$brief_len);
			
			$cfg["time"]=date($cfg["article_formattime{$cfg["lanstr"]}"],$rs["edittime"]);
			
			$cfg["about"]= $rs['about'];
			$cfg["filestr"]=$this->getFileDownImg("productfile",$rs["file"]);
			$cfg["price"]= $rs['price'];
			
			$p=webpath.product_imagepath;
			$cfg["picurl"]=$p.$rs["smallpic"];		
			$cfg["bigpic"]=$p.$rs["bigpic"];
			//$cfg["midpic"]=$p.$rs["bigpic"];
			$cfg["tu1"]=$p.$rs["tu1"];
			$cfg["tu2"]=$p.$rs["tu2"];
			$cfg["tu3"]=$p.$rs["tu3"];
			$cfg["tu4"]=$p.$rs["tu4"];
			$cfg["tu5"]=$p.$rs["tu5"];
			$cfg["file"]=webpath.product_filepath.$rs["file"];

			$url3 = "index.php?topclassid={$topclassid}&classid=".$rs["classid"];
			$url4 = $cfg["htmlPath"].$rs["paths"];	
			$cfg["classurl"]=geturl($url3,$url4);		
			$cfg["classname"]=gets("classname","class","lanid={$rs["classid"]} and lanstr='{$cfg["lanstr"]}'");
			
			$url1 = "?topclassid={$cfg["topclassid"]}&classid={$rs["classid"]}&id=".$rs["lanid"]."&lanstr=".$cfg["lanstr"];
			$url2 = $cfg["htmlPath"].$rs["paths"].$rs["filename"];
			$cfg["theurl"] = geturl($url1,$url2);		
			
			$temp.=$this->reLabel2($loopstr);			
		}		
		//没有产品时提示。
		if($cfg['allnums']<1){			
			$temp = $this->reLabel(FRONT_THEME_DIR."piece/norecord.html");		

		}

		return $temp;
	}

	
	/**
	 * 相关阅读
	 * 处理 模板标签函数
	 * @param $loopstr
	 */
	function recommandlist($loopstr,$valuestr)
	{
		global $cfg,$db;
		$title_len = intval($this->getCan($valuestr, "title_len")); //标题截取长度
		$title_len = $title_len>0 ? $title_len : 60;
		
		$brief_len = intval($this->getCan($valuestr, "brief_len")); //内容简介截取长度		
		$brief_len = $brief_len>0 ? $brief_len : 100;	
		
		$limit = intval($this->getCan($valuestr, "limit")); //内容简介截取长度		
		$limit_str = $limit>0 ? "limit 0,".$limit : "";	
		
		$thesql="select recommands from #@__product where lanid=".$cfg["id"]." and lanstr='".$cfg["lanstr"]."'";
		$one =$db->GetOne($thesql);
		if($one['recommands']) {
			$thesql="select * from #@__product where lanid in(".$one['recommands'].") and lanstr='".$cfg["lanstr"]."' and title<>'' and del=0 and locked=0 order by sortid desc  ".$limit_str;
			$db->dosql($thesql);
			while($rs=$db->GetArray())
			{
				
				$cfg["titles" ]= $rs["title"];				
				$cfg["title"] = left($rs["title"],  $title_len);
				$cfg["brief"]=left($rs["brief"],  $brief_len);
				$cfg["price" ]= $rs["price"];
				$cfg["product_sn" ]= $rs["product_sn"];
			
				$url1="?topclassid={$cfg["topclassid"]}&classid={$cfg["classid"]}&id=".$rs["lanid"]."&lanstr=".$cfg["lanstr"];
				$url2=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
				$cfg['url']=geturl($url1,$url2);
				
				$p=webpath.product_imagepath;
			$cfg["picurl"]=$p.$rs["smallpic"];		
			$cfg["bigpic"]=$p.$rs["bigpic"];		
				$temp.=$this->reLabel2($loopstr);
				

			}
			return $temp;
		}
	}
	
	/**
	 * 相关阅读
	 * 处理 模板标签函数
	 * @param $loopstr
	 */
	function articlelist($loopstr,$valuestr)
	{
		global $cfg,$db;
		$title_len = intval($this->getCan($valuestr, "title_len")); //标题截取长度
		$title_len = $title_len>0 ? $title_len : 60;
		
		$brief_len = intval($this->getCan($valuestr, "brief_len")); //内容简介截取长度		
		$brief_len = $brief_len>0 ? $brief_len : 100;	
		
		$limit = intval($this->getCan($valuestr, "limit")); //内容简介截取长度		
		$limit_str = $limit>0 ? "limit 0,".$limit : "";	
		
		$thesql="select article from #@__product where lanid=".$cfg["id"]." and lanstr='".$cfg["lanstr"]."'";
		$one =$db->GetOne($thesql);
		if($one['article']) {
			$thesql="select * from #@__article where lanid in(".$one['article'].") and lanstr='".$cfg["lanstr"]."' and title<>'' and del=0 and locked=0 order by sortid desc  ".$limit_str;
			$db->dosql($thesql);
			while($rs=$db->GetArray())
			{
				
				$cfg["titles" ]= $rs["title"];				
				$cfg["title"] = left($rs["title"],  $title_len);
				$cfg["brief"]=left($rs["brief"],  $brief_len);
				$cfg["price" ]= $rs["price"];
				$cfg["product_sn" ]= $rs["product_sn"];
			
				$url1="?topclassid=21&classid=21&id=".$rs["lanid"]."&lanstr=".$cfg["lanstr"];
				$url2=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
				$cfg['url']=geturl($url1,$url2);
				
				$p=webpath.product_imagepath;
			$cfg["picurl"]=$p.$rs["smallpic"];		
			$cfg["bigpic"]=$p.$rs["bigpic"];		
				$temp.=$this->reLabel2($loopstr);
				

			}
			return $temp;
		}
	}


	/**
	 * 产品关联下载
	 * 处理 模板标签函数
	 * @param $loopstr
	 */
	function downloadlist($loopstr,$valuestr)
	{
		global $cfg,$db;
		$title_len = intval($this->getCan($valuestr, "title_len")); //标题截取长度
		$title_len = $title_len>0 ? $title_len : 60;
		
		
		
		$thesql="select downloads from #@__product where lanid=".$cfg["id"]." and lanstr='".$cfg["lanstr"]."'";
		$one =$db->GetOne($thesql);
		if($one['downloads']) {
			$thesql="select * from #@__download where lanid in(".$one['downloads'].") and lanstr='".$cfg["lanstr"]."' and del=0 and locked=0 order by sortid desc  ";
			$db->dosql($thesql);
			while($rs=$db->GetArray())
			{
				
				$cfg["titles" ]= $rs["title"];				
				$cfg["title"] = left($rs["title"],  $title_len);
				
				if(instr($rs["url"],"http://")){
					$cfg["url"] = $rs["url"];
				}else{
					$cfg["url"] = webpath.download_imagepath.$rs["url"];
				}
				$cfg["size"]=$rs["size"];
				$cfg["type"]=$rs["type"];
				$cfg["content"]=$rs["content"];	
				$temp.=$this->reLabel2($loopstr);
			}
			return $temp;
		}
	}



	
	/**
	 * 相册图片
	 */
	function gallerylist($loopstr,$valuestr){
		
		global $cfg,$db;	
		$thesql="select gallery from #@__product where lanid=".$cfg["id"]." and lanstr='".$cfg["lanstr"]."'";
		$rs =$db->GetOne($thesql);
		$arr = unserialize($rs["gallery"]);
		if($rs["gallery"]){
		$i=0;
		foreach ($arr as  $key=>$tmp){
				if(trim($tmp["image"]) && trim($tmp["thumb"])){
					$cfg['thumb'] = webpath.product_imagepath.$tmp["thumb"];
					$cfg['image'] = webpath.product_imagepath.$tmp["image"];
					$cfg['bigpic'] = webpath.product_imagepath.$tmp["bigpic"];
					$cfg['original'] = webpath.product_imagepath.$tmp["original"];
					$cfg["ys"]=$i==0?" class=\"on\" ":"";
					$temp.=$this->reLabel2($loopstr);
				}else{
					$cfg['thumb'] = $cfg["front_skins_path"].$cfg["front_theme"]."/skins/images/nopic.gif";
					$cfg['image'] = $cfg["front_skins_path"].$cfg["front_theme"]."/skins/images/nopic_big.gif";
					$cfg['bigpic'] = $cfg["front_skins_path"].$cfg["front_theme"]."/skins/images/nopic_big.gif";
					$cfg['original'] =$cfg["front_skins_path"].$cfg["front_theme"]."/skins/images/nopic_big.gif";
					$temp.=$this->reLabel2($loopstr);
					
				}
				$i++;
				$cfg["key"]=$i;
			}
		}	
		return $temp;
	
	}
	
	/**
	 *
	 * 产品信息详情
	 */
	function thedesc()
	{
		global $cfg,$db;
		loadlanXml("product","product_");
		$thesql="select * from #@__product where lanid=".$cfg["id"]." and lanstr='".$cfg["lanstr"]."'";
		$rs=$db->GetOne($thesql);
		
		$this->web_seo($rs["titles"],$rs["tag"],$rs["about"]);
		
		//访问权限设置
		if($rs['isvip'] == 1  && $_SESSION['front_level']<2 ){
			$this->_halt($this->_lang('nopriv','member'),webpath.'index.php');
		}
		//点击数
		if($rs['lanid']) $db->execute("UPDATE #@__article set click=click+1 WHERE lanid=".$rs['lanid']);
		
		$cfg["classid"]=$rs["classid"];
		
		//获取顶级目录Banner		
		$cat = $this->getClassOne($cfg["classid"]);
		//一级栏目ID
		$cfg["current_fid"] = $cat['fid'];
				
		//栏目Banner 图片		
		$cfg["classbanner"] = $cat['banner'] ? $cat["banner"] : $this->getTopBanner($cfg["topclassid"]);	
	

		if($cfg["web_tohtml"])
		{
			$pagestr=$cfg["inpage"]==1?"":$cfg["page_prefix"].$cfg["inpage"]."/";
			$url=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
			if($cfg["inpagecontent"]){//在调此函数前已经给了值.动态时,content的页类分页在此页最上方
				$pageurl=$cfg["htmlPath"].$rs["paths"].$rs["filename"];
				$cfg["content"].=$this->htmlpage($pageurl,$cfg["inpages"],$cfg["inpage"],1);
			}
		}
		
		//$cfg["file"]= $rs["file"] && $_SESSION['front_level']>0 ?  '<div class="tab02"><a href="'.webpath."userfiles/productfile/".$rs["file"].'" class="downbtn">'.$this->lang('download','product') .'</a></div> ' : '';
		
		$cfg["title"] = $rs["title"];
		$cfg["brief"] = $rs["brief"];
		$cfg["click"] = $rs["click"];
		$cfg["about"]= left($rs["about"],90,"");
		$cfg["price"] = $rs["price"];
		$cfg["tu1"] = webpath.product_imagepath.$rs["tu1"];
		$cfg["tu2"] = webpath.product_imagepath.$rs["tu2"];
		$cfg["tu3"] = webpath.product_imagepath.$rs["tu3"];
		$cfg["tu4"] = webpath.product_imagepath.$rs["tu4"];
		$cfg["tu5"] = webpath.product_imagepath.$rs["tu5"];
		$cfg["product_sn"] =$rs["product_sn"];	
		$cfg["file"]=webpath.product_filepath.$rs["file"];
		if($cfg["lanstr"]=="zh_cn"){
			$cfg["share"]='<div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare">
<a class="bds_qzone"></a>
<a class="bds_tsina"></a>
<a class="bds_tqq"></a>
<a class="bds_renren"></a>
<a class="bds_t163"></a>
<span class="bds_more">更多</span>
<a class="shareCount"></a>
</div>
<script type="text/javascript" id="bdshare_js" data="type=tools&uid=6513684" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript"> 
document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000)
</script>';
		}elseif($cfg["lanstr"]=="en"){
			$cfg["share"]='<div class="bdsharebuttonbox"><a href="#" class="bds_more" data-cmd="more"></a><a title="Share to Facebook" href="#" class="bds_fbook" data-cmd="fbook"></a><a title="Share to Twitter" href="#" class="bds_twi" data-cmd="twi"></a><a title="Share to delicious" href="#" class="bds_deli" data-cmd="deli"></a><a title="Share to linkedin" href="#" class="bds_linkedin" data-cmd="linkedin"></a><a title="Share to print" href="#" class="bds_print" data-cmd="print"></a></div>
<script>window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{},"image":{"viewList":["fbook","twi","deli","linkedin","print"],"viewText":"Share to ：","viewSize":"16"},"selectShare":{"bdContainerClass":null,"bdSelectMiniList":["fbook","twi","deli","linkedin","print"]}};with(document)0[(getElementsByTagName(\'head\')[0]||body).appendChild(createElement(\'script\')).src=\'http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion=\'+~(-new Date()/36e5)];</script>';
		}
		
		$cfg["standard"] =$rs["standard"];	
		$cfg["parameter"] =$rs["parameter"];
		$cfg["other"] =$rs["other"];			
		
		

		//产品图片与相册
		$arr = unserialize($rs["gallery"]);
		if($rs["gallery"]){
			foreach ($arr as  $key=>$tmp){
				if(trim($tmp["image"]) && trim($tmp["thumb"])){
					$thumb = webpath.product_imagepath.$tmp["thumb"];
					$image = webpath.product_imagepath.$tmp["image"];
					$bigpic = webpath.product_imagepath.$tmp["bigpic"];
					$original = webpath.product_imagepath.$tmp["original"];
					$galleryStr.='<li><img  onmouseover="changePic(\''.$image.'\',\''.$original.'\')" border="0" src="'.$thumb.'" /></li>';
				}
			}
			
			if($arr[0]["image"]!=""){
				
				$cfg["bigpic"] =  webpath.product_imagepath.$arr[0]["image"];
			}else{
				$cfg["bigpic"] = $cfg["front_skins_path"].$cfg["front_theme"]."/skins/images/nopic_big.gif";
			}
			if($arr[0]["original"]!=""){
				$cfg["origpic"] = webpath.product_imagepath.$arr[0]["original"];
			}else{
				$cfg["bigpic"] = $cfg["front_skins_path"].$cfg["front_theme"]."/skins/images/nopic_big.gif";
			}

		}else{
				$cfg["bigpic"] = $cfg["front_skins_path"].$cfg["front_theme"]."/skins/images/nopic_big.gif";
				$cfg["original"] = $cfg["front_skins_path"].$cfg["front_theme"]."/skins/images/nopic_big.gif";
				
			}
				

		//当前分类信息
		$url1="?topclassid={$cfg["topclassid"]}&classid=".$rs["classid"];
		$url2=$cfg["htmlPath"].$rs["paths"];
		$cfg["classurl"]=geturl($url1,$url2);
		$cfg["classname"]=gets("classname","class","lanid={$rs["classid"]} and lanstr='".$cfg["lanstr"]."'");		
		$cfg["classidstr"] = $rs["classidstr"];		
		
		
		/** 访问历史 */
		if (!empty($_COOKIE['YM']['history']))
		{
			$history = explode(',', $_COOKIE['YM']['history']);
			array_unshift($history, $cfg["id"]);
			$history = array_unique($history);
			while (count($history) > 10)
			{
				array_pop($history);
			}
			setcookie('YM[history]', implode(',', $history), time() + 3600 * 24 * 30);
		}
		else
		{
			setcookie('YM[history]', $cfg["id"], time() + 3600 * 24 * 30);
		}
		
		//内页 模板 		
		$cfg["modelcontent"]=$this->reLabel(FRONT_THEME_DIR.$cat['template_detail']);

		//框架模板			
		//$content = $this->reLabel(FRONT_THEME_DIR.$cat['template_main']);
		$content = $this->reLabel(FRONT_THEME_DIR."main/product.html");
		
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
		$thesql1="select lanid,title,edittime from #@__product where lanid=".$cfg["id"];
		$rs1=$db->GetOne($thesql1);
		//echo $thesql1."<br />";
		$orderstr=$cfg["product_orderby"];//正常
		$thesql="select * from #@__product where sortid>".$rs1["lanid"]." and lanstr='".$cfg["lanstr"]."' and classid in(".allclassids($cfg["classid"]).") ".$cfg["product_orderby"]." LIMIT 1";
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

		$orderstr=$cfg["product_orderby"];//正常

		$thesql1="select lanid,title,edittime from #@__product where lanid=".$cfg["id"];
		$rs1=$db->GetOne($thesql1);

		$thesql="select * from #@__product where sortid<".$rs1["lanid"]." and lanstr='".$cfg["lanstr"]."' and classid in(".allclassids($cfg["classid"]).") ".$cfg["product_orderby"]." LIMIT 1";
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