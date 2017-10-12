<?php

/**
 *
 * 页面独立模块操作显示类。
 * @author guoho
 *
 */
class o extends alzCms {
	
	/**
	 *
	 * 取得推荐产品列表
	 * @param $loopstr
	 */
	function topProduct($loopstr,$valuestr) {
		global $cfg, $db;
		$limit = $this->getCan($valuestr, "limit");   //Limit 数量限制。
		
		$title_len = intval($this->getCan($valuestr, "title_len")); //标题截取长度
		$title_len = $title_len>0 ? $title_len : 20;
		
		$brief_len = intval($this->getCan($valuestr, "brief_len")); //内容简介截取长度		
		$brief_len = $brief_len>0 ? $brief_len : 100;
		
		$type = $this->getCan($valuestr, "type"); //条件类型，		
		$classid = intval($this->getCan($valuestr, "classid")); //所限定的分类
		

		$cfg['commend'] = "commend";
		$cfg['type'] = "type";
		if ($type=='$cfg["commend"]'){
			$addsql = " and commend like '%{$cfg["commend"]}%' ";
		}
		if($type=='hot'){
			$addsql = " and is_hot=1 ";
		}
		else if($type=='new'){
			$addsql = " and is_new=1 ";
		}
		if($classid>0){
			$addsql.= " and classid in (".allclassids($classid).")";
		}
		
		$thesql = "select * from #@__product where lanstr='{$cfg["lanstr"]}'  $addsql and del=0 and locked=0 order by sortid desc limit 0,$limit";
		//$thesql = "select * from #@__product where lanstr='{$cfg["lanstr"]}'  $addsql and del=0 and locked=0 order by sortid desc limit $limit";
		$db->dosql($thesql, "in");
		$i = 1;
		while ($rs = $db->GetArray("in")) {
			$cfg['step'] = $i;
			$cfg['display']  = $i ==1 ? "inline" : "none";
			$cfg["key"]=$i;
			$i = $i+1;
			
			
			
			$p=webpath.product_imagepath;
			$cfg["picurl"]=$p.$rs["smallpic"];		
			$cfg["bigpic"]=$p.$rs["bigpic"];
			
			$cfg["titles"] = $rs["title"];
			$cfg["lanzi"] = $rs["lanzi"];
			$cfg["huizi"] = $rs["huizi"];
			$cfg["minititle"] = $rs["minititle"];

			$cfg["title"] = left($rs["title"],  $title_len);
			$cfg["brief"]=left($rs["brief"],  $brief_len);
			$cfg["brief2"]=left(strip_tags($rs["brief"]),  $brief_len);

			$cfg["price"] = $rs["price"];
			$cfg["product_sn"] = $rs["product_sn"];
			
				
			if ($cfg["web_tohtml"]) {
				$cfg["theurl"] = $cfg["htmlPath"] . $rs["paths"] . $rs["filename"];
			} else {
				$topclassid = getTopclassid($rs["classidstr"]);
				$cfg["theurl"] = "index.php?topclassid=$topclassid&classid={$rs["classid"]}&id=" . $rs["lanid"];
			}
			$temp.=$this->reLabel2($loopstr);
		}
		return $temp;
	}


/**
	 *
	 * 取得推荐产品列表
	 * @param $loopstr
	 */
	function topProduct_limit($loopstr,$valuestr) {
		global $cfg, $db;
		$limit = $this->getCan($valuestr, "limit");   //Limit 数量限制。
		
		$title_len = intval($this->getCan($valuestr, "title_len")); //标题截取长度
		$title_len = $title_len>0 ? $title_len : 20;
		
		$brief_len = intval($this->getCan($valuestr, "brief_len")); //内容简介截取长度		
		$brief_len = $brief_len>0 ? $brief_len : 100;
		
		$type = $this->getCan($valuestr, "type"); //条件类型，		
		$classid = intval($this->getCan($valuestr, "classid")); //所限定的分类
		

		$cfg['commend'] = "commend";
		if ($cfg["commend"])
		$addsql = " and commend like '%{$cfg["commend"]}%' ";
		if($type=='hot'){
			$addsql = " and is_hot=1 ";
		}
		else if($type=='new'){
			$addsql = " and is_new=1 ";
		}
		else if($type=='love'){
			$addsql = " and is_love=1 ";
		}
		
		if($classid>0){
			$addsql.= " and classid in (".allclassids($classid).")";
		}
		
		$thesql = "select * from #@__product where lanstr='{$cfg["lanstr"]}'  $addsql and del=0 and locked=0 order by sortid desc ".$limit;
		
		//exit($thesql);
		$db->dosql($thesql, "in");
		$i = 1;
		while ($rs = $db->GetArray("in")) {
			$cfg['step'] = $i;
			$cfg['display']  = $i ==1 ? "inline" : "none";
			$i = $i+1;
			
			
			
			$p=webpath.product_imagepath;
			$cfg["picurl"]=$p.$rs["smallpic"];		
			$cfg["bigpic"]=$p.$rs["bigpic"];
			$cfg["midpic"]=$p.$rs["midpic"];
			
			$cfg["titles"] = $rs["title"];
			$cfg["title"] = left($rs["title"],  $title_len);
			$cfg["brief"]=left($rs["brief"],  $brief_len);
			
			$cfg["product_sn"] = $rs["product_sn"];
			
				
			if ($cfg["web_tohtml"]) {
				$cfg["theurl"] = $cfg["htmlPath"] . $rs["paths"] . $rs["filename"];
			} else {
				$topclassid = getTopclassid($rs["classidstr"]);
				$cfg["theurl"] = "index.php?topclassid=$topclassid&classid={$rs["classid"]}&id=" . $rs["lanid"];
			}
			$temp.=$this->reLabel2($loopstr);
		}
		return $temp;
	}




function getaboutpic($valuestr){
	global $cfg, $db;
		$id = trim($this->getCan($valuestr, "id"));

		$thesql = "select classname from #@__class where lanid=$id and lanstr='{$cfg["lanstr"]}'";
	
		$rs = $db->GetOne($thesql);

		return $rs['picurl'];	;	
	
}
	/**
	 *
	 * 取得文章列表
	 * @param $loopstr
	 */
	function topArticle($loopstr,$valuestr) {
		global $cfg, $db;
		$limit = $this->getCan($valuestr, "limit");   //Limit 数量限制。
		$type = $this->getCan($valuestr, "type"); //条件类型，		
		$classid = intval($this->getCan($valuestr, "classid")); //所限定的分类
		
		$title_len = intval($this->getCan($valuestr, "title_len")); //标题截取长度
		$title_len = $title_len>0 ? $title_len : 50;
		
		$brief_len = intval($this->getCan($valuestr, "brief_len")); //内容简介截取长度		
		$brief_len = $brief_len>0 ? $brief_len : 100;
		
		$isimg = intval($this->getCan($valuestr, "img")); //内容简介截取长度		
		$isimg = $isimg>0 ? $isimg : 0;
		
		
		//$addsql = " and commend ='commend' ";
		if($type=='hot'){
			$addsql = " and ishot=1 ";
		}
		if($type=='top'){
			$addsql = " and istop=1 ";
		}
		if($classid>0){
			$addsql.=	" and classid in(" . allclassids($classid) . ")";
		}
		if($isimg>0){
			$addsql.=	" and picurl<>'' ";
		}
		$thesql = "select * from #@__article where title<>'' and del=0 $addsql and locked=0 and lanstr='{$cfg["lanstr"]}' order by sortid desc limit $limit";
		//最新的新闻
		if($type=='new'){			
			$addsql = $cid>0 ? "and classid in(" . allclassids($cid) . ")" : '';			
			$thesql = "select lanid,classid,classidstr,title,edittime,paths,filename,file from #@__article where del=0 and locked=0 $addsql and lanstr='{$cfg["lanstr"]}' order by sortid desc limit $limit";
			
		}
		$db->dosql($thesql, "a2");
		$i = 1;
		while ($rs = $db->GetArray("a2")) {
			$topclassid = getTopclassid($rs["classidstr"]);
			if ($cfg["web_tohtml"]) {
				$cfg["theurl"] = $cfg["htmlPath"] . $rs["paths"] . $rs["filename"];
			} else {
				$cfg["theurl"] = "?topclassid=$topclassid&classid=" . $rs["classid"] . "&id=" . $rs["lanid"];
			}
			$url1 = "index.php?topclassid={$topclassid}&classid=".$rs["classid"];
			$url2 = $cfg["htmlPath"].$rs["paths"];			
			$cfg["classurl"]=geturl($url1,$url2);
			
			//步长
			$cfg["step"]  = $i;
			$i++;
			
			
			$thesql1="select * from #@__articles where lanid=".$rs["lanid"]." and lanstr='".$cfg["lanstr"]."'";
			$rs1=$db->GetOne($thesql1);
			
			$cfg["content"] = $rs1["content"];
		
			$cfg["picurl"] = webpath.article_imagepath.$rs["picurl"];
			$cfg["picurl2"] = webpath.article_imagepath.$rs["bigpic"];
			$cfg["titles"] = $rs["title"];
			
			$cfg["title"] = left($rs["title"],  $title_len);
			$cfg["brief"]=left($rs["brief"],  $brief_len);
			
			//$cfg["filestr"] = $this->getFileDownImg("articlefile", $rs["file"]);
			$cfg["file"]=$rs["file"];
			
			$cfg["time"] = date("m-d", $rs["edittime"]);
			$cfg["time1"] = date("Y-m", $rs["edittime"]);
			$cfg["time2"] = date("d", $rs["edittime"]);
			$temp.=$this->reLabel2($loopstr);
		}
		return $temp;
	}

	/**
	 *
	 * 获取 链接列表
	 * @param $loopstr
	 */
	function topLink($valuestr) {

		global $cfg, $db;
		$limit = $this->getCan($valuestr, "limit");
		$classid = $this->getCan($valuestr, "classid");
		$add_sql="";
		if(!empty($classid)){
			$add_sql=" and classid=".$classid;
		}
		$thesql = "select * from #@__links where lanstr='{$cfg["lanstr"]}' ".$add_sql." order by sortid desc ".$limit;
		$db->dosql($thesql, "a2");
		$numsqls = "select * from #@__links where lanstr='{$cfg["lanstr"]}' ".$add_sql." order by sortid desc ".$limit;
		$menusnum = $db->num($numsqls, "allnum");
		$i=1;
		$temp="";
		while ($rs = $db->GetArray("a2")) {
			if($i%12==1){
				//$temp .= '<li><ul>';
			}
			$cfg["title"] = $rs["title"];
			$cfg["url"]   = $rs["url"];
			$cfg['logo']  = webpath . 'userfiles/links/'.$rs['logo'] ;
			//$temp .= '<div class="item"><a href="'.$cfg["url"].'" title="'.$cfg["title"].'" target=""><img src="'.$cfg['logo'].'"></a></div>';
			$temp .= '<a href="'.$cfg["url"].'" title="'.$cfg["title"].'" target="">'.$cfg['title'].'</a>';
			if($i/12==2||$i==$menusnum){
				//$temp .= '</ul></li>';
			}
			//$temp.=$this->reLabel2($loopstr);
			$i++;
		}
		return $temp;
	}
	
	/**
	 *
	 * 获取 链接列表
	 * @param $loopstr
	 */
	function botkeywords($loopstr,$valuestr) {

		global $cfg, $db;
		$limit = $this->getCan($valuestr, "limit");
		//$classid = $this->getCan($valuestr, "classid");
		$thesql = "select * from #@__keylink where lanstr='{$cfg["lanstr"]}' order by ctime desc ";
		$db->dosql($thesql, "a2");
		while ($rs = $db->GetArray("a2")) {
			$cfg["title"] = $rs["word"];
			$cfg["url"]   = $rs["url"];
			//$cfg['logo']  = webpath . 'userfiles/links/'.$rs['logo'] ;
			$temp.=$this->reLabel2($loopstr);
		}
		return $temp;
	}

	
	/**
	 *
	 * 在线客服列表
	 * @param $valuestr
	 */
	function list_qq($valuestr) {
		global $db, $cfg;
		
		$thesql = "select * from #@__myqq where lanstr='{$cfg['lanstr']}' order by sortid asc";
		$db->dosql($thesql, "qq");
		$temp = '';
		//exit($thesql);
		while ($rs = $db->GetArray("qq")) {
			
			/*if($rs['type']=="QQ"){
				 $temp.= '<li><a href="http://wpa.qq.com/msgrd?V=1&amp;Uin='.$rs['account'].'" target="_blank" title="click here ,talk to me" ><img src="'.webpath.'/images/qq.gif"  border="0" align="absmiddle" /></a></li>';
			}
			else if($rs['type']=="MSN"){
				 $temp.=' <li><a href="msnim:chat?contact='.$rs['account'].'" title="click here ,talk to me" ><img src="'.webpath.'/images/msn.gif" align="'.$rs['account'].'" /></a></li>';
			}
			else if($rs['type']=="SKYPE") {
				$temp.='<li><a href="skype:'.$rs['account'].'?chat" title="click here ,talk to me"><img src="'.webpath.'/images/skype.gif" alt="'.$rs['account'].'" /></a></li>';
			}*/
if($rs['type']=="QQ"){
				 $temp.='<LI><a href="tencent://message/?uin='.$rs['account'].'&Site='.$rs['account'].'&Menu=yes" target="_blank"><img src="http://wpa.qq.com/pa?p=1:'.$rs['account'].':1" alt="点击这里给我发送消息" border="0" align="left">&nbsp;'.$rs['title'].'</a></LI>';
			}
			else if($rs['type']=="MSN"){
				 $temp.='<LI><A class=icoTc href="msnim:chat?contact='.$rs['account'].'"><img src="'.sitepath.'web/images/msn.gif" align="left" />'.$rs['title'].'</a></li>';
			}
			else if($rs['type']=="SKYPE") {
				$temp.='<LI><a href="skype:'.$rs['account'].'?call" onclick="return skypeCheck();"><img src="http://mystatus.skype.com/smallclassic/mengsajewel" style="border: none;" alt="Call me!" /></a></li>';
			}
			else if($rs['type']=="wangwang") {
				$temp.='<LI><a target="blank" href="http://amos.im.alisoft.com/msg.aw?v=2&uid='.$rs['account'].'&site=cntaobao&s=1&charset=utf-8"><img border="0" SRC="http://amos.im.alisoft.com/online.aw?v=2&uid='.$rs['account'].'&site=cntaobao&s=1&charset=utf-8" alt="点击这里给我发消息">&nbsp;'.$rs['title'].'</a></li>';
			}
			else if($rs['type']=="ali") {
				$temp.='<LI><a target="_blank" href="http://amos1.sh1.china.alibaba.com/msg.atc?v=1&uid=chenping6023"><img border="0" src="http://amos1.sh1.china.alibaba.com/online.atc?v=1&uid='.$rs['account'].'&s=101" alt="点击这里可以给对方发送消息" algin="left bottom">'.$rs['title'].'</a></li>';
			}
			else if($rs['type']=="hc") {
				$temp.='<LI><iframe src="http://chat.im.hc360.com/hcchat/get.html?hcid='.$rs['account'].'&style=9" width="86" height="21" frameborder="0" scrolling="no" marginWidth="0" marginHeight="0"></iframe></li>';
			}

		}
		return $temp;
	}
	/**
	 *
	 * 在线客服列表
	 * @param $valuestr
	 */
	function list_qq2($valuestr) {
		global $db, $cfg;
		
		$thesql = "select * from #@__myqq where lanstr='{$cfg['lanstr']}' order by sortid asc";
		$db->dosql($thesql, "qq");
		$temp = '';
		//exit($thesql);
		while ($rs = $db->GetArray("qq")) {
			
			/*if($rs['type']=="QQ"){
				 $temp.= '<li><a href="http://wpa.qq.com/msgrd?V=1&amp;Uin='.$rs['account'].'" target="_blank" title="click here ,talk to me" ><img src="'.webpath.'/images/qq.gif"  border="0" align="absmiddle" /></a></li>';
			}
			else if($rs['type']=="MSN"){
				 $temp.=' <li><a href="msnim:chat?contact='.$rs['account'].'" title="click here ,talk to me" ><img src="'.webpath.'/images/msn.gif" align="'.$rs['account'].'" /></a></li>';
			}
			else if($rs['type']=="SKYPE") {
				$temp.='<li><a href="skype:'.$rs['account'].'?chat" title="click here ,talk to me"><img src="'.webpath.'/images/skype.gif" alt="'.$rs['account'].'" /></a></li>';
			}*/
if($rs['type']=="QQ"){
				 $temp.='<LI><A class=icoTc href="tencent://message/?uin='.$rs['account'].'&Site='.$rs['account'].'&Menu=yes" target="_blank"><img src=" http://wpa.qq.com/pa?p=1:'.$rs['account'].':4" alt="952688242" border="0" />'.$rs['title'].'</A></LI>';
			}
			else if($rs['type']=="MSN"){
				 $temp.='<LI><A class=icoTc href="msnim:chat?contact='.$rs['account'].'"><img src="'.sitepath.'web/images/msn.gif" align="'.$rs['account'].'" /></a></li>';
			}
			else if($rs['type']=="SKYPE") {
				$temp.='<LI><A class=icoTc href="skype:'.$rs['account'].'?chat"><img src="'.sitepath.'web/images/sky.png" alt="'.$rs['account'].'" />&nbsp;'.$rs['title'].'</a></li>';
			}
			else if($rs['type']=="wangwang") {
				$temp.='<LI>&nbsp;&nbsp;<a class="alitalk-link" data-uid="'.$rs['account'].'" target="_blank" href="http://amos.alicdn.com/msg.aw?v=2&uid='.$rs['account'].'&site=enaliint&s=22&charset=UTF-8" style="text-decoration:none" ><img border="0" src="http://amos.alicdn.com/online.aw?v=2&uid='.$rs['account'].'&site=enaliint&s=22&charset=UTF-8" alt="Hi, how can I help you?" style="border:none; vertical-align:middle;" /> <span style="font:700 11px/12px tahoma; color:#0066cc;">'.$rs['title'].'</span></a></li>';
			}
			else if($rs['type']=="ali") {
				$temp.='<LI>&nbsp;&nbsp;<a target="_blank" href="http://amos.im.alisoft.com/msg.aw?v=2&uid='.$rs['account'].'&site=cnalichn&s=5"><img border="0" src=" http://amos.im.alisoft.com/online.aw?v=2&uid='.$rs['account'].'&site=cnalichn&s=4" alt="点击这里可以给对方发送消息" algin="left bottom"></a></li>';
			}
			else if($rs['type']=="hc") {
				$temp.='<LI><iframe src="http://chat.im.hc360.com/hcchat/get.html?hcid='.$rs['account'].'&style=9" width="86" height="21" frameborder="0" scrolling="no" marginWidth="0" marginHeight="0"></iframe></li>';
			}

		}
		return $temp;
	}
	
	
	/**
	 * 获取首页轮换广告信息。
	 */
	function getAd($loopstr,$valuestr) {
		global $cfg, $db;
		//广告显示样式 	
		$id = formatnum($this->getCan($valuestr, "id"), 0);
		$sep = $this->getCan($valuestr, "sep");
		$thesql = "SELECT * FROM #@__ad WHERE lanstr='" . $cfg["lanstr"] . "' ORDER BY id DESC LIMIT 1";
		if($id>0) $thesql = "SELECT * FROM #@__ad WHERE lanstr='" . $cfg["lanstr"] . "' and lanid=$id";
		$rs = $db->getOne($thesql);
		$rs["picstr"] = str_replace("\r\n", "\n", $rs["picstr"]);
		$rs["linkstr"] = str_replace("\r\n", "\n", $rs["linkstr"]);			
		$picArr = explode("\n",$rs["picstr"]);
		$linkArr = explode("\n",$rs["linkstr"]);
		$count = count($picArr);
		foreach($picArr as $key=>$pic){
			$picArr[$key] = webpath.ad_imagepath.$pic;
			$linkArr[$key] = $linkArr[$key]!="" ?  $linkArr[$key] : '#';
			if(!instr($linkArr[$key],"http") && $linkArr[$key] !='#'){
				$linkArr[$key]=webpath.$linkArr[$key];
			}
			$cfg['key'] = $key;
			$cfg['sep'] = $key<$count-1 ? $sep : '';
			$cfg['url'] =  $linkArr[$key];
			$cfg['image'] = $picArr[$key] ;			
			$temp.=$this->reLabel2($loopstr); 
		}
		return $temp;
	}
	
	
	/**
	 * 获取首页轮换广告信息。
	 */
	function getAd2($loopstr,$valuestr) {
		global $cfg, $db;
		//广告显示样式 	
		$id = formatnum($this->getCan($valuestr, "id"), 0);
		$sep = $this->getCan($valuestr, "sep");
		$thesql = "SELECT * FROM #@__ad WHERE lanstr='" . $cfg["lanstr"] . "' ORDER BY id DESC LIMIT 1";
		if($id>0) $thesql = "SELECT * FROM #@__ad WHERE lanstr='" . $cfg["lanstr"] . "' and lanid=$id";
		$rs = $db->getOne($thesql);
		$rs["picstr"] = str_replace("\r\n", "\n", $rs["picstr"]);
		$rs["smallstr"] = str_replace("\r\n", "\n", $rs["smallstr"]);
		$rs["smallstr1"] = str_replace("\r\n", "\n", $rs["smallstr1"]);
		$rs["linkstr"] = str_replace("\r\n", "\n", $rs["linkstr"]);			
		$rs["namestr"] = str_replace("\r\n", "\n", $rs["namestr"]);				
		$picArr = explode("\n",$rs["picstr"]);
		$smallArr = explode("\n",$rs["smallstr"]);
		$small1Arr = explode("\n",$rs["smallstr1"]);
		$linkArr = explode("\n",$rs["linkstr"]);
		$nameArr = explode("\n",$rs["namestr"]);
		$count = count($picArr);
		$i = 0;
		foreach($picArr as $key=>$pic){
			$picArr[$key] = webpath.ad_imagepath.$pic;
			$smallArr[$key] = webpath.ad_imagepath.$smallArr[$key];
			$small1Arr[$key] = webpath.ad_imagepath.$small1Arr[$key];
			$linkArr[$key] = $linkArr[$key]!="" ?  $linkArr[$key] : '#';
			if(!instr($linkArr[$key],"http") && $linkArr[$key] !='#'){
				$linkArr[$key]=webpath.$linkArr[$key];
			}
			$cfg['key'] = $key+1;
			$cfg['sep'] = $key<$count-1 ? $sep : '';
			$cfg['url'] =  $linkArr[$key];
			$cfg['adtitle'] =  $nameArr[$key];
			$cfg['image'] = $picArr[$key] ;	
			$cfg['simage'] = $smallArr[$key] ;		
			$cfg['simage1'] = $small1Arr[$key] ;
			$cfg["j"] = $i;
			if($key==0){
				$cfg["st"] =" style='display: block; opacity: 1;'";
			}else{
				 $cfg["st"] =" style='display: none; opacity: 0;'";
			}
			$i++;
			$temp.=$this->reLabel2($loopstr); 
		}
		return $temp;
	}
	
	/**
	 *
	 *  获取网页碎片代码 获取网页碎片内容。
	 * @param $loopstr
	 */
	function getPiece($valuestr) {
		global $cfg, $db;
		$code = trim($this->getCan($valuestr, "code"));

		$thesql = "select * from #@__piece where piece_code='$code' and lanstr='{$cfg["lanstr"]}'";
	
		$rs = $db->GetOne($thesql);

		return $rs['content'];
	}
	
	/**
	 *
	 * 取得浏览历史产品列表
	 * @param $loopstr
	 */
	function topHistory($loopstr,$valuestr) {
		global $cfg, $db;
		$title_len = intval($this->getCan($valuestr, "title_len")); //标题截取长度
		$title_len = $title_len>0 ? $title_len : 20;
		
		$brief_len = intval($this->getCan($valuestr, "brief_len")); //内容简介截取长度		
		$brief_len = $brief_len>0 ? $brief_len : 100;
		
		if (!empty($_COOKIE['YM']['history'])){
			
			$thesql = "select * from #@__product where lanid in (".$_COOKIE['YM']['history'].") and lanstr='{$cfg["lanstr"]}' and del=0 and locked=0 order by sortid desc limit 5";
			$db->dosql($thesql, "in");
			$i = 1;
			while ($rs = $db->GetArray("in")) {
				$cfg['step'] = $i;
				$i = $i+1;

				if(trim($rs["smallpic"]) && file_exists(webroot.product_imagepath.$rs["smallpic"])){
					$cfg["picurl"] = webpath . product_imagepath . $rs["smallpic"];
				}
				$cfg["brief"]=left($rs["brief"],  $brief_len);
				$cfg["product_sn"] = $rs["product_sn"];
				$cfg["titles"] = $rs["title"];
				$cfg["title"] = left($rs["title"],  $title_len);					
				if ($cfg["web_tohtml"]) {
					$cfg["theurl"] = $cfg["htmlPath"] . $rs["paths"] . $rs["filename"];
				} else {
					$topclassid = getTopclassid($rs["classidstr"]);
					$cfg["theurl"] = "index.php?topclassid=$topclassid&classid={$rs["classid"]}&id=" . $rs["lanid"];
				}
				$temp.=$this->reLabel2($loopstr);
			}
		}

		return $temp;
	}
	
	
	


	
	/**
	 *
	 * Enter description here ...
	 * @param $valuestr
	 */
	function hotkeyword() {
		global $db, $cfg;

		$keyword = trim($cfg['hot_keywords'.$cfg['lanstr']]) ? str_replace("，", ",", trim($cfg['hot_keywords'.$cfg['lanstr']])) : '';
		//echo $cfg['hot_keywords'];

		if($keyword){
			$keyword = explode(',',$keyword);

			foreach ($keyword as $k=>$key){
				if($k>7) break;
				$key = trim($key);
				$temp.='<a href="'.webpath.'search.php?keywords='.$key.'&lanstr='.$cfg['lanstr'].'" title="'.$key.'">'.$key.'</a>、';
			}
		}
		return $temp;
	}

	

	/**
	 *
	 * 评论列表
	 */
	function commentlist($loopstr)
	{
		global $cfg,$db;
		$pagesize=20;
		$cfg["pagesize"]=$pagesize;
		$beginid=($cfg["page"]-1)*$pagesize;
		$beginid = $beginid < 0 ? 0 : $beginid;
		
		$prod = $cfg["id"] >0 &&  ($cfg['model'] == 'article' || $cfg['model'] == 'product') ? " classidstr='". $cfg["classidstr"]."' and objid=".$cfg["id"]." and model='".$cfg['model']."' and " : " objid=0 and ";
		$thesql="select id from #@__comment where $prod lanstr='".$cfg["lanstr"]."' AND locked=0";

		$db->dosql($thesql);
		$cfg["allnums"]=$db->nums();

		$limitstr="limit $beginid,$pagesize";
		$thesql="select * from #@__comment where $prod lanstr='".$cfg["lanstr"]."' AND locked=0 order by id desc $limitstr";

		$db->dosql($thesql);
		while($rs=$db->GetArray())
		{

			$cfg["username"]= $rs["username"];
			$cfg["content"]= $rs['content'];
			$temp.=$this->reLabel2($loopstr);
				
		}
		return $temp;

	}
	
	
	/**
	 *
	 * 网站统计
	 */
	function countip() {
		global $cfg;
		switch ($cfg["count_io"]) {
			case 0:
				break;
			case 2:
				return $cfg["count_code"];
				break;
		}
	}
	
	

	/**
	 *
	 * 在线客服
	 */
	function serviceip() {
		global $cfg;
		switch ($cfg["service_io"]) {
			case 0:
				break;
			case 2:
				return $cfg["ec_service".$cfg['lanstr']];
				break;
		}
	}
	function getservice(){
		global $cfg;
		return $cfg["service_io"];
	}
	function topArticle1($valuestr) {
		global $cfg, $db;
		$limit = $this->getCan($valuestr, "limit");   //Limit 数量限制。
		$type = $this->getCan($valuestr, "type"); //条件类型，		
		$classid = intval($this->getCan($valuestr, "classid")); //所限定的分类
		
		$title_len = intval($this->getCan($valuestr, "title_len")); //标题截取长度
		$title_len = $title_len>0 ? $title_len : 50;
		
		$brief_len = intval($this->getCan($valuestr, "brief_len")); //内容简介截取长度		
		$brief_len = $brief_len>0 ? $brief_len : 100;
		
		$isimg = intval($this->getCan($valuestr, "img")); //内容简介截取长度		
		$isimg = $isimg>0 ? $isimg : 0;
		
		
		$addsql = " and commend ='commend' ";
		if($type=='hot'){
			$addsql = " and ishot=1 ";
		}
		if($type=='top'){
			$addsql = " and istop=1 ";
		}
		if($classid>0){
			$addsql.=	" and classid in(" . allclassids($classid) . ")";
		}
		if($isimg>0){
			$addsql.=	" and picurl<>'' ";
		}
		$thesql = "select * from #@__article where title<>'' and del=0 $addsql and locked=0 and lanstr='{$cfg["lanstr"]}' order by sortid desc limit $limit";
		//最新的新闻
		if($type=='new'){			
			$addsql = $cid>0 ? "and classid in(" . allclassids($cid) . ")" : '';			
			$thesql = "select lanid,classid,classidstr,title,edittime,paths,filename,file from #@__article where del=0 and locked=0 $addsql and lanstr='{$cfg["lanstr"]}' order by sortid desc limit $limit";
			
		}
		$db->dosql($thesql, "a2");
		$i = 1;
		$pics="";
		$links="";
		$texts="";
		while ($rs = $db->GetArray("a2")) {
			$topclassid = getTopclassid($rs["classidstr"]);
			if ($cfg["web_tohtml"]) {
				$cfg["theurl"] = $cfg["htmlPath"] . $rs["paths"] . $rs["filename"];
			} else {
				$cfg["theurl"] = "?topclassid=$topclassid&classid=" . $rs["classid"] . "&id=" . $rs["lanid"];
			}
			$url1 = "index.php?topclassid={$topclassid}&classid=".$rs["classid"];
			$url2 = sitepath.$cfg["htmlPath"].$rs["paths"];			
			$cfg["classurl"]=geturl($url1,$url2);
			
			//步长
			$cfg["step"]  = $i;
			$i++;
			
			$cfg["picurl"] = webpath.article_imagepath.$rs["picurl"];
			$cfg["titles"] = $rs["title"];
			
			$cfg["title"] = left($rs["title"],  $title_len);
			$cfg["brief"]=left($rs["brief"],  $brief_len);
			
			//$cfg["filestr"] = $this->getFileDownImg("articlefile", $rs["file"]);
			$cfg["file"]=$rs["file"];
			
			$cfg["time"] = date($cfg["article_formattime" . $cfg["lanstr"]], $rs["edittime"]);
			$pics .= 'imgUrl'.$cfg["step"].'+"|"+';
			$links .= 'imgLink'.$cfg["step"].'+"|"+';
			$texts .= 'imgUrl'.$cfg["step"].'+"|"+';
			
		}
		$temp.='var pics='.trim($pics,"|\"+").';';
	 	$temp.='var links='.trim($links,"|\"+").';';
	 	$temp.='var texts='.trim($texts,"|\"+").';';
		return $temp;
	}


}

?>