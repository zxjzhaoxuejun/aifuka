<?php

/**
 * 前面导航菜单显示处理类
 *
 * @version        $Id: web.config.php 1 10:33 2010年7月6日Z $
 * @package        10000CMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, 10000CMS, Inc.
 * @license        http://www.www.tiandixin.net
 * @link           http://www.www.tiandixin.net
 */

/**
 *
 * Enter description here ...
 * @author guoho
 *
 */
class menu extends alzCms {
	/**
	 *
	 * 获取 栏目列表 ,根据
	 * @param unknown_type $loopstr   可以初
	 * @param unknown_type $valuestr  模板标签中的其它字符串
	 */
	function all($loopstr, $valuestr) {
		global $cfg, $db;
		
		//父级分类 ID。
		$fid = $this->getCan($valuestr, "fid");
		if (!is_numeric($fid) && $fid != "") {
			$fid = formatnum($cfg["topclassid"], 0);
		} else {
			$fid = formatnum($fid, 0);
		}
		$pid = formatnum($this->getCan($valuestr, "pid"), 0); //是否主栏目。
		$limit =intval($this->getCan($valuestr, "limit"));		//数量限制。
		$cat = formatnum($this->getCan($valuestr, "cat"), 0);
		$aat = formatnum($this->getCan($valuestr, "aat"), 0);
		$first_a = formatnum($this->getCan($valuestr, "first_a"), 0);
		$case = formatnum($this->getCan($valuestr, "case"), 0);
		
		$name_len =intval($this->getCan($valuestr, "name_len"));		//栏目名称截取长度		
		$name_len = $name_len>0 ? $name_len : 30;
		
		 //获取多层栏目的当前栏目样式。 分类的层数不是实际的分类层数，而是假定 通过 设定fid 获取的子分类为第一层分类，以此类推。 //最高三层
		$sel_css_ids = $this->getCan($valuestr, "sel_css_ids");
		$sel_css_ids = trim($sel_css_ids) ? trim($sel_css_ids) : "nav_sel|sel01|sel02";
		$sel_css_ids = explode("|", $sel_css_ids);
		
		$subnav_style = $this->getCan($valuestr, "subnav_style");  //子级分类样式 //最高两层。
		$subnav_style = trim($subnav_style) ? trim($subnav_style) : "1|1";		
		$subnav_style = explode("|", $subnav_style);
		
		
		$limitstr = $limit > 0 ? " limit $limit " : '';		
		$addsql.=$pid != "" ? " and pid=$pid " : "";
		
		$thesql = "select * from #@__class where model<>'-' $addsql and classname<>'' and lanstr='{$cfg["lanstr"]}' and fid=$fid order by sortid,lanid ".$limitstr;
		$db->dosql($thesql);
		$i = 0;
		$cfg['actived'] = '';
		while ($rs = $db->GetArray()) {
			$i = $i + 1;			
		
			$cfg['sel_css_id'] = '';			
			$cfg['submenu'] = '';
			
			$cfg["name_short"] = left($rs["classname"], $name_len);			
			$cfg["name"] = $rs["classname"];
			
			//当前顶级分类样式名称
			if($fid==0 && $rs['lanid'] == $cfg['topclassid']){
				$cfg['sel_css_id'] = $sel_css_ids[0];
			}
	
			//当前栏目为非顶级时，获取当前栏目的样式，及子分类。
			
			//分类为三级分类时，设置低级分类的当前分类。
			if($cfg['current_fid'] && ($rs['lanid'] == $cfg['current_fid'])){
				$cfg['submenu'] = $this->all_sub_two($rs['lanid'], $sel_css_ids,$name_len, $subnav_style);
				
				$cfg['sel_css_id'] = $sel_css_ids[0];
			}
			//分类为二级分类时，判断当前分类。
			else if($rs['lanid'] == $cfg['classid']){
				$cfg['submenu'] = $this->all_sub_two($rs['lanid'], $sel_css_ids, $name_len, $subnav_style);
				$cfg['sel_css_id'] = $sel_css_ids[0];
			}
			//获取所有子分类。
			$cfg['subnav'] = $this->all_sub_two($rs['lanid'], $sel_css_ids,$name_len, $subnav_style);
			
			if($cat=="1"){
				$cfg["cat"]= $this->catproduct($rs["lanid"]);
			}
			if($aat=="1"){
				$cfg["aat"]= $this->catarticle($rs["lanid"]);
			}
			if($case=="1"){
				$cfg["case"]= $this->cat_case($rs["lanid"]);
			}
			
			if($first_a=="1"){
				$cfg["first_a"]= $this->first_article($rs["lanid"],$rs["classname"]);
			}
			
			//取得其它语言的分类名称，
			$thesql = "select classname from #@__class where lanid={$rs['lanid']} and lanstr='en'";
			$cfg['lang_name'] = $db->getValue($thesql, 'classname');
			
			$cfg["picurl"] = webpath.class_imagepath.$rs["picurl"];
		
			$cfg["paths"] = $rs["paths"];
			$cfg["menuid"] = $rs["topclassid"];
			$cfg["key"] = $i;
			
			if ($cfg["web_tohtml"]) {
				$url = $cfg["htmlPath"] . $rs["paths"];				
			} else {
				$url = "/web/index.php?topclassid=" . $rs["topclassid"] . "&classid=" . $rs["lanid"] . "&lanstr=" . $cfg["lanstr"];				
			}
			$cfg["url"] = $this->getClassUrl($rs["urlgoto"], $url);			
			$temp.=$this->reLabel2($loopstr);
		}		
		return $temp;
	}
	
	
	
	/**
	 *
	 * 分灰的二级子分类
	 
	 * 
	 */
	function all_sub_two($fid,$sel_css_ids ,$name_len,$subnav_style) {
		global $cfg, $db;
		$numsqls = "select * from #@__class where lanstr='{$cfg["lanstr"]}' and fid=$fid order by sortid,lanid";
		$menusnum = $db->num($numsqls, "allnum");
		if ($menusnum >0) {
			$thesql = "select * from #@__class where lanstr='{$cfg["lanstr"]}' and fid=$fid order by sortid,lanid";
			$db->dosql($thesql, "menu2");
			
			//其它样式需要自己设定。
			//$temp = $subnav_style[0] ==1 ? '<ul class="sub">' : '<div>';
			$temp = $subnav_style[0] ==1 ? '<ul class="sub" id="sub'.$fid.'">' : '';
			while ($rs = $db->GetArray("menu2")) {
				if ($cfg["web_tohtml"]) {
					$url = $cfg["htmlPath"] . $rs["paths"];
				} else {
					$url = "/web/index.php?topclassid=" . gettopclassid($rs["classidstr"]) . "&classid={$rs["lanid"]}" . "&lanstr=" . $cfg["lanstr"];
				}
				$url = $this->getClassUrl($rs["urlgoto"], $url);				
				$active = $cfg['classid'] && ($rs['lanid'] == $cfg['classid']) ? $sel_css_ids[1] : '';				
				$picurl=webpath.class_imagepath.$rs['picurl'];
				//其它样式需要自己设定。				
				$name = left($rs["classname"], $name_len);
				$sub_three = $this->all_sub_three($rs['lanid'], $sel_css_ids, $name_len,$subnav_style);

				if($subnav_style[0]==1)  {
					$temp.="<li><a href='$url' id=$active>{$name}</a></li>";
				}
				else if ($subnav_style[0]==2) $temp.="<dd><a href='$url' id=$active>{$name}</a></dd>";				
				else if ($subnav_style[0]==3) $temp.="<dd><a href='$url' id=$active>{$name}</a></dd>";
				
				
			}
			//其它样式需要自己设定。
			$temp.= $subnav_style[0] ==1 ? '</ul>' : '';
		}
		return $temp;
	}


	/**
	 *
	 * 第三层分类。
	 * @param $fid
	 */
	function all_sub_three($fid,$sel_css_ids ,$name_len,$subnav_style) {
		global $cfg, $db;
		$thesql = "select * from #@__class where lanstr='{$cfg["lanstr"]}' and fid=$fid order by sortid,lanid";
		$db->dosql($thesql, "menu3");
		
		$temp = $subnav_style[1] ==1 ? '<ul>' : '<div>';
		$center = "";
		while ($rs = $db->GetArray("menu3")) {
			if ($cfg["web_tohtml"]) {
				$url = $cfg["htmlPath"] . $rs["paths"];
			} else {
				$url = "/web/index.php?topclassid=" . gettopclassid($rs["classidstr"]) . "&classid={$rs["lanid"]}";
			}
			$url = $this->getClassUrl($rs["urlgoto"], $url);
			$active = $cfg['classid'] && ($rs['lanid'] == $cfg['classid']) ? $sel_css_ids[2] : '';	
			
			$active = $cfg['classid'] && ($rs['lanid'] == $cfg['classid']) ? $sel_css_ids[2] : '';				
			//其它样式需要自己设定。
			
			$name = left($rs["classname"], $name_len);
			$sub_four = $this->all_sub_four($rs['lanid'], $sel_css_ids, $name_len,$subnav_style);						
			if($subnav_style[1]==1)  $center.="<li><a href='$url'>{$name}</a>".$sub_four."</li>";
			else if ($subnav_style[1]==2) $center.="<a href='$url'>{$name}</a>".$sub_four."";				
			else if ($subnav_style[1]==3) $center.="<div><a href='$url'>{$name}</a>".$sub_four."<div>";
			
		}
		//其它样式需要自己设定。
		if($center=='') {
			return '';
		}
		
		return $temp .$center .( $subnav_style[0] ==1 ?  '</ul>' : '</div>');
	}
	/**
	 *
	 * 第四层分类。
	 * @param $fid
	 */
	function all_sub_four($fid,$sel_css_ids ,$name_len,$subnav_style) {
		global $cfg, $db;
		$thesql = "select * from #@__class where lanstr='{$cfg["lanstr"]}' and fid=$fid order by sortid,lanid";
		$db->dosql($thesql, "menu4");
		
		$temp = $subnav_style[1] ==1 ? '<ul>' : '<div>';
		$center = "";
		while ($rs = $db->GetArray("menu4")) {
			if ($cfg["web_tohtml"]) {
				$url = $cfg["htmlPath"] . $rs["paths"];
			} else {
				$url = "/web/index.php?topclassid=" . gettopclassid($rs["classidstr"]) . "&classid={$rs["lanid"]}";
			}
			$url = $this->getClassUrl($rs["urlgoto"], $url);
			$active = $cfg['classid'] && ($rs['lanid'] == $cfg['classid']) ? $sel_css_ids[2] : '';	
			
			$active = $cfg['classid'] && ($rs['lanid'] == $cfg['classid']) ? $sel_css_ids[2] : '';				
			//其它样式需要自己设定。
			
			$name = left($rs["classname"], $name_len);
							
			if($subnav_style[1]==1)  $center.="<li><a href='$url'>{$name}</a></li>";
			else if ($subnav_style[1]==2) $center.="<a href='$url'>{$name}</a>";				
			else if ($subnav_style[1]==3) $center.="<div><a href='$url'>{$name}</a><div>";
			
		}
		//其它样式需要自己设定。
		if($center=='') {
			return '';
		}
		
		return $temp .$center .( $subnav_style[0] ==1 ?  '</ul>' : '</div>');
	}

	/**
	 * 获取底部导航列表
	 */
	function botnav($loopstr,$valuestr) {
		global $cfg, $db;
		 
		$fid = $this->getCan($valuestr, "fid");
		$limit =intval($this->getCan($valuestr, "limit"));
		$name_len =intval($this->getCan($valuestr, "name_len"));		//栏目名称截取长度		
		$name_len = $name_len>0 ? $name_len : 20;
		//$img =intval($this->getCan($valuestr, "img"));
		//$img=$name_len>0 ? $name_len : 0;
		
		if (!is_numeric($fid) && $fid != "") {
			$fid = formatnum($cfg["topclassid"], 0);
		} else {
			$fid = formatnum($fid, 0);
		}
		/*if($img){
			$addsql.=" and picurl<>''";
		}*/
		$thesql = "select * from #@__class where model<>'-' $addsql and lanstr='{$cfg["lanstr"]}' and fid=$fid and botnav=1 order by sortid,lanid limit ".$limit;
		//exit($thesql);
		$db->dosql($thesql);
		$i = 0;
		$cfg['actived'] = '';
		while ($rs = $db->GetArray()) {
			$cfg["fid"] = $rs["lanid"];
			$cfg["picurl"] = webpath.class_imagepath.$rs["picurl"];
			$cfg['subnav'] = $this->botnav_child();			
			$cfg["name"] = left($rs["classname"],$name_len);
			$cfg["classname"] = $rs["classname"];
			if ($cfg["web_tohtml"]) {
				$url = $cfg["htmlPath"] . $rs["paths"];
			} else {
				$url = "/web/index.php?topclassid=" . gettopclassid($rs["classidstr"]) . "&classid={$rs["lanid"]}&lanstr=".$cfg["lanstr"];
			}
			$cfg["url"] = $this->getClassUrl($rs["urlgoto"], $url);	
			$temp.=$this->reLabel2($loopstr);
		}
		return $temp;
	}

    /**
	 *
	 * 底部分类的二级分类
	 */
	function botnav_child() {
		global $cfg, $db;		 
		$thesql = "select * from #@__class where lanstr='{$cfg["lanstr"]}' and fid={$cfg["fid"]} order by sortid,lanid";
		$db->dosql($thesql, "menu2");
		while ($rs = $db->GetArray("menu2")) {
			$url = "/web/index.php?topclassid=" . gettopclassid($rs["classidstr"]) . "&classid={$rs["lanid"]}";
			$temp.="<a href='$url' title=".$rs["classname"].">".left($rs["shortname"],20,"")."</a>";
		}
		return $temp;
	}
	
	
	/**
	 *
	 * 获取 栏目列表 及栏目产品列表,根据
	 * @param unknown_type $loopstr   可以初
	 * @param unknown_type $valuestr  模板标签中的其它字符串
	 */
	function listbyfid($loopstr) {
		global $cfg, $db;
		$thesql = "select * from #@__class where lanstr='{$cfg["lanstr"]}' and fid={$cfg["classid"]} order by sortid,lanid";
		$db->dosql($thesql);
		$i = 0;
		while ($rs = $db->GetArray()) {
			$i = $i + 1;
			$cfg["picurl"] = trim($rs["picurl"]) ?  webpath.class_imagepath.$rs["picurl"] : sitepath."images/test.jpg";
			$cfg["name"] = $rs["classname"];
			$cfg["paths"] = $rs["paths"];
			$cfg["brief"] = left($rs["brief"],600);
			$cfg["content"] = left($rs["content"],200);
			$cfg["menuid"] = $rs["topclassid"];
			if ($cfg["web_tohtml"]) {
				$url = $cfg["htmlPath"] . $rs["paths"];
			} else {
				$url = "/web/index.php?topclassid=" . $rs["topclassid"] . "&classid=" . $rs["lanid"] . "&lanstr=" . $cfg["lanstr"];
			}				
			$cfg["url"] = $this->getClassUrl($rs["urlgoto"], $url);
			$cfg['products'] = $this->catproduct($rs['lanid']);
			$temp.=$this->reLabel2($loopstr);
		}
		return $temp;
	}

	function catproduct($cid){
		global $cfg, $db;
		$thesql="select * from #@__product where classid in(".allclassids($cid).") and lanstr='".$cfg["lanstr"]."' and title<>'' and del=0 and locked=0 order by lanid desc";
		$rs = $db->dosql($thesql,"menu2");
		$list='<ul>';
		while($rs=$db->GetArray("menu2"))
		{
			$title = $rs["minititle"];

			$p = webpath.product_imagepath;
			//设置产品的默认图片
			$picurl = sitepath."images/product/list.jpg";
			if(trim($rs["smallpic"]) && file_exists(webroot.product_imagepath.$rs["smallpic"])){
				$picurl =$p.$rs["smallpic"];
			}
			$url1 = "/web/index.php?topclassid={$cfg["topclassid"]}&classid=".$rs["classid"];
			$url2 = sitepath.$cfg["htmlPath"].$rs["paths"];
			$url1 = "/web/index.php?topclassid={$cfg["topclassid"]}&classid={$rs["classid"]}&id=".$rs["lanid"];
			$url2 = $cfg["htmlPath"].$rs["paths"].$rs["filename"];
			$theurl = geturl($url1,$url2);

			$list .= '<li><a href="'.$theurl.'" title="'.$title.'">'.$title.'</a></li>';
		}
		$list.="</ul>";
		return $list;
	}
	
	function catarticle($cid){
		global $cfg, $db;
		$thesql="select * from #@__article where classid in(".allclassids($cid).") and lanstr='".$cfg["lanstr"]."' and title<>'' and del=0 and locked=0 order by lanid desc limit 1,4";
		$rs = $db->dosql($thesql,"menu3");
		$list='<ul>';
		while($rs=$db->GetArray("menu3"))
		{
			$title = $rs["title"];

			$p = webpath.article_imagepath;
			//设置产品的默认图片
			$picurl = sitepath."images/product/list.jpg";
			if(trim($rs["smallpic"]) && file_exists(webroot.article_imagepath.$rs["smallpic"])){
				$picurl =$p.$rs["smallpic"];
			}
			$url1 = "/web/index.php?topclassid={$cfg["topclassid"]}&classid=".$rs["classid"];
			$url2 = sitepath.$cfg["htmlPath"].$rs["paths"];
			$url1 = "/web/index.php?topclassid={$cfg["topclassid"]}&classid={$rs["classid"]}&id=".$rs["lanid"];
			$url2 = $cfg["htmlPath"].$rs["paths"].$rs["filename"];
			$theurl = geturl($url1,$url2);

			$list .= '<li><a href="'.$theurl.'" title="'.$title.'">'.$title.'</a><span>'.date("Y-m-d",$rs["edittime"]).'</span></li>';
		}
		$list.="</ul>";
		return $list;
	}
	
	function first_article($cid,$c_name){
		global $cfg, $db;
		$thesql="select * from #@__article where classid in(".allclassids($cid).") and lanstr='".$cfg["lanstr"]."' and title<>'' and del=0 and locked=0 order by lanid desc limit 1";
		$rs = $db->dosql($thesql,"menu3");
		$list='<ul>';
		while($rs=$db->GetArray("menu3"))
		{
			$title = $rs["title"];

			$p = webpath.article_imagepath;
			//设置产品的默认图片
			if(trim($rs["picurl"])){
				$picurl= webpath.article_imagepath.$rs["picurl"];
			}
			$url1 = "/web/index.php?topclassid={$cfg["topclassid"]}&classid=".$rs["classid"];
			$url2 = sitepath.$cfg["htmlPath"].$rs["paths"];
			$url1 = "/web/index.php?topclassid={$cfg["topclassid"]}&classid={$rs["classid"]}&id=".$rs["lanid"];
			$url2 = $cfg["htmlPath"].$rs["paths"].$rs["filename"];
			$theurl = geturl($url1,$url2);
			$list .= '<div class="first1">
                        <div class="first-left">
                        	<div class="box1-title"><h1>'.$c_name.'</h1></div>
                            <div class="first-title"><a href="'.$theurl.'" title="'.$title.'">'.$title.'</a></div>
                        </div>
                        <div class="first-right">
                            <a href="'.$theurl.'" title="'.$title.'"><img src="'.$picurl.'" width="209" height="118" alt="" /></a>
                        </div>
                    </div>';
		}
		$list.="</ul>";
		return $list;
	}
	
	function cat_case($cid){
		global $cfg, $db;
		$thesql="select * from #@__article where classid in(".allclassids($cid).") and lanstr='".$cfg["lanstr"]."' and title<>'' and del=0 and locked=0 order by lanid desc limit 3";
		$rs = $db->dosql($thesql,"menu3");
		$list='<ul class="caselist">';
		while($rs=$db->GetArray("menu3"))
		{
			$title = $rs["title"];
			$brief = $rs["brief"];

			if(trim($rs["picurl"])){
				$picurl= webpath.article_imagepath.$rs["picurl"];
			}
			$url1 = "/web/index.php?topclassid={$cfg["topclassid"]}&classid=".$rs["classid"];
			$url2 = sitepath.$cfg["htmlPath"].$rs["paths"];
			$url1 = "/web/index.php?topclassid={$cfg["topclassid"]}&classid={$rs["classid"]}&id=".$rs["lanid"];
			$url2 = $cfg["htmlPath"].$rs["paths"].$rs["filename"];
			$theurl = geturl($url1,$url2);
			$list .= '<li>
                    	<a href="'.$theurl.'" title="'.$title.'" class="case-img"><img src="'.$picurl.'" width="212" /></a>
                        <a href="'.$theurl.'" title="'.$title.'" class="case-title">'.$title.'</a>
                    </li>';
		}
		$list.="</ul>";
		return $list;
	}
	
	
	
	/**
	 *
	 * 获取分类 的 URL地址。
	 * @param $valuestr
	 */
	function url($valuestr) {
		global $db, $cfg;
		$id = $this->getCan($valuestr, "id");
		if ($id == "index") {
			if ($cfg["web_tohtml"]) {
				if(lannums>1){	
					if($cfg["lanstr"]=="en")	{
						$url = sitepath . "";
					}else{
						$url = sitepath . $cfg["lanstr"] . htmlIndex;
					}
				}
				else {
					$url = sitepath . "";
				}

			} else {
				$url = "/web/index.php?topclassid=0&classid=0&lanstr=".$cfg["lanstr"];
			}
		}else if ($id == "index1") {
			if ($cfg["web_tohtml"]) {
				if(lannums>1){	
					if($cfg["lanstr"]=="en")	{		
						$url = sitepath . "zh_cn" . htmlIndex;
					}else{
						$url = sitepath . "en" . htmlIndex;
					}
				}
				else {
					$url = sitepath . htmlIndex;
				}

			} else {
				$url = "/web/index.php?topclassid=0&classid=0&lanstr=".$cfg["lanstr"];
			}
		} elseif ($id != "" && !is_numeric($id)) {//语言跳转
			if ($cfg["web_tohtml"]) {
				if ($id == deflan2) {
					$url = sitepath. "";
				} else {
					$url = sitepath . $id . htmlIndex;
				}
			} else {
				$topclassid = formatnum($cfg["topclassid"], 0);
				$classid = formatnum($cfg["classid"], 0);

				$url = "/web/index.php?topclassid=$topclassid&classid=$classid&id=" . $cfg["id"] . "&lanstr=" . $id;
			}
		} else {//普通栏目ID跳转
			if ($id < 1)
			$id = $cfg["topclassid"];
			$thesql = "select * from #@__class where lanid=$id and lanstr='{$cfg["lanstr"]}'";
			$rs = $db->GetOne($thesql);
			if ($cfg["web_tohtml"]) {
				$url = $cfg["htmlPath"] . $rs["paths"];
			} else {
				$url = "/web/index.php?topclassid={$rs["topclassid"]}&classid=" . $rs["lanid"]."&lanstr=".$cfg["lanstr"];;
			}
			$url = $this->getClassUrl($rs["urlgoto"], $url);
		}
		return $url;
	}
	function getbanben(){
		$id="en";
		$banben="ENGLISH";
		//exit($cfg["lanstr"]);
		if($cfg["lanstr"]=="en") {$id="zh_cn";$banben="中文版";}
		//if ($cfg["web_tohtml"]) {
			if ($id == deflan2) {
				$url = sitepath. htmlIndex;
			} else {
				$url = sitepath . $id . htmlIndex;
			}
		/*} else {
			$topclassid = formatnum($cfg["topclassid"], 0);
			$classid = formatnum($cfg["classid"], 0);

			$url = "/web/index.php?topclassid=$topclassid&classid=$classid&id=" . $cfg["id"] . "&lanstr=" . $id;
		}*/
		return $url;
	}

	/**
	 *
	 * 获取分类名称
	 * @param $valuestr
	 */
	function classname($valuestr) {
		global $db, $cfg;
		$id = intval($this->getCan($valuestr, "id"));
		if ($id == 0) $id = $cfg["topclassid"];		
		$thesql = "select classname from #@__class where lanid=$id and lanstr='{$cfg["lanstr"]}'";
		$name = $db->getValue($thesql, 'classname');	
		$name=left($name,25);	
		return $name;
	}
	function classpic($valuestr) {
		global $db, $cfg;
		$id = intval($this->getCan($valuestr, "id"));
		if ($id == 0) $id = $cfg["topclassid"];		
		$thesql = "select picurl from #@__class where lanid=$id and lanstr='{$cfg["lanstr"]}'";
		$picurl = $db->getValue($thesql, 'picurl');	
		$cfg["picurl"] = webpath.class_imagepath.$picurl;
		return $cfg["picurl"];
	}
	function classbrief($valuestr) {
		global $db, $cfg;
		$id = intval($this->getCan($valuestr, "id"));
		if ($id == 0) $id = $cfg["topclassid"];		
		$thesql = "select brief from #@__class where lanid=$id and lanstr='{$cfg["lanstr"]}'";
		$brief = $db->getValue($thesql, 'brief');	
		//$brief=left($name,25);	
		return $brief;
	}
	/**
	 *
	 * 获取分类名称
	 * @param $valuestr
	 */
	function first($valuestr) {
		global $db, $cfg;
		$id = intval($this->getCan($valuestr, "id"));
		if ($id == 0) $id = $cfg["topclassid"];		
		if($id==1){
			$name="a";
		}else if($id==9){
			$name="p";
		}else if($id==23){
			$name="n";
		}else if($id==29){
			$name="s";
		}else if($id==268){
			$name="j";
		}else if($id==270){
			$name="f";
		}else if($id==31){
			$name="c";
		}
		return $name;
	}
	/**
	 *
	 * 获取分类反当前版本名称
	 * @param $valuestr
	 */
	function get_classname($valuestr) {
		global $db, $cfg;
		$id = intval($this->getCan($valuestr, "id"));
		if ($id == 0) $id = $cfg["topclassid"];	
		$yuyan="en";	
		if($cfg["lanstr"]=="en"){
			$yuyan="zh_cn";
		}
		$thesql = "select classname from #@__class where lanid=$id and lanstr='{$yuyan}'";
		$name = $db->getValue($thesql, 'classname');		
		return $name;
	}
	
	function get_iproc($valuestr){
		global $cfg;
		if($cfg["lanstr"]=="en"){
			return "产品目录";
		}else{
			return "Product Categories";
		}
	}
	/**
	 *
	 * 获取分类反当前版本名称
	 * @param $valuestr
	 */
	function get_cnclassname($valuestr) {
		global $db, $cfg;
		$id = intval($this->getCan($valuestr, "id"));
		if ($id == 0) $id = $cfg["topclassid"];	
		$thesql = "select classname from #@__class where lanid=$id and lanstr='zh_cn'";
		$name = $db->getValue($thesql, 'classname');		
		return $name;
	}
	
	/**
	 *
	 * 获取分类名称
	 * @param $valuestr
	 */
	function classname_en($valuestr) {
		global $db, $cfg;
		$id = intval($this->getCan($valuestr, "id"));
		if ($id == 0) $id = $cfg["topclassid"];		
		switch($id){
			case 156:
				$name="About Us";
				break;
			case 176:
				$name="News";
				break;
			case 182:
				$name="Service";
				break;
			case 185:
				$name="Download";
				break;
			case 189:
				$name="Contact Us";
				break;
			default:
				$name="About Us";
		}
		return $name;
	}
	
	/**
	 *
	 * 获取分类名称
	 * @param $valuestr
	 */
	function curclassname($valuestr) {
		global $db, $cfg;
		$id = $this->getCan($valuestr, "id");
		//exit($id);
		if ($id == 0) {$id=$cfg["classid"];}	
		
		//if($id == "d") {$id=$cfg["classid"];	exit("dd_".$id);}
		
		$thesql = "select classname from #@__class where lanid=$id and lanstr='{$cfg["lanstr"]}'";
		$name = $db->getValue($thesql, 'classname');	
		if(!empty($_GET["tiaojian"])){
			switch($_GET["tiaojian"]) {
				case 1:
					$name = "热卖产品";
					break;
				case 2:
					$name = "新品推荐";
					break;
				default:
					$name=$name;
					break;
			}
		}
		return $name;
	}
	
	/**
	 *
	 * 判断给出的id 是否为当前栏目，并返回样式 CSS ID.
	 * @param $valuestr
	 */
	function current_css_id($valuestr) {
		global $db, $cfg;
		$id = intval($this->getCan($valuestr, "id"));
		$tag = $this->getCan($valuestr, "tag");
		$css_id = "";
		if($cfg['current_fid'] && ($id == $cfg['current_fid'])){
			$css_id = $tag;	
		}
		//分类为二级分类时，判断当前分类。
		else if($id == $cfg['classid']){
				$css_id = $tag;	
		}
		return $css_id; 	
	}
}

?>