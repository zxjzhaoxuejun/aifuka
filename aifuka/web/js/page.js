//页数 
var pagenums;

function inhtmlpageto(page)
{
	var pageon=getInHtmlPageon();
	if(page>pagenums||page<1){return false;}
	if(page==1){location.href=location.href.replace("/"+page_prefix+pageon,"");return;}
	if(location.href.indexOf("/"+page_prefix+pageon)<1){
		location.href+=page_prefix+page+"/";
	}else{
		location.href=location.href.replace("/"+page_prefix+pageon,"/"+page_prefix+page);
	}
}


function getInHtmlPageon()
{
	var oRegex=new RegExp("\/"+page_prefix+"([^\/]+)","i");
	var oMatch=oRegex.exec(location.href);
	if(oMatch&&oMatch.length>0)
		page=urldecode(oMatch[1]);
	else
		page=1;
	return page;
}


//从静态location 获取当前页
function getHtmlPageon()
{
	var oRegex=new RegExp("\/"+page_prefix+"([^\/]+)","i");
	var oMatch= oRegex.exec(location.href);
	
	if(oMatch&&oMatch.length>0)
		page=urldecode(oMatch[1]);
	else
		page=1;
	return page;
	
	//return pagenums-page+1;
}

function getPageLanStr()
{
	if(location.search.indexOf("lanstr")>0)
	{
		return getPra("lanstr");
	}else{
		var oRegex=new RegExp("\/html([^\/]+)","i");
		var oMatch=oRegex.exec(location.href);
		if(oMatch&&oMatch.length>0)
			lanstr=urldecode(oMatch[1]);
		else
			lanstr="zh_cn";
		return lanstr;
	}
}

function formatpageurl(page,pagestr)
{
	if(page<1)page=1;
	if(page>pagenums)page=pagenums;
	var url=location.href;
	var urlpage=getPra(pagestr);
	if(urlpage==""){
		var s=url.indexOf("?")>=0?"&":"?";
		url+=s+pagestr+"="+page;
	}else{
		if(page==urlpage)return "";
		url=url.replace(pagestr+"="+urlpage,pagestr+"="+page);
	}
	return url;
}

/**
 * 页码跳转
*/
function pageto(page){
	var url=formatpageurl(page,"page");

	if(url!="")location.href=url;
	return;
}
function inpageto(page){
	var url=formatpageurl(page,"inpage");
	if(url!="")location.href=url;
	return;
}

/**
 * 静态页面，页面跳转
 */
function htmlpageto(p2)
{
	var pageon=getHtmlPageon();	
    
	if(location.href.indexOf("/"+page_prefix+pageon)<1){
		location.href+=page_prefix+p2+"/";
	}else{
		location.href=location.href.replace("/"+page_prefix+pageon,"/"+page_prefix+p2);
	}
	
	
}

//显示页码
function page(allnums,pagesize,desc,pageto){
	
	if(isNaN(allnums)&&allnums!="")allnums=ajax(webpath+"include/getValue.php?action=getDatanums&lanstr="+lanstr+"&model="+allnums);
	if(Null(pageto))pageto="pageto";
	var can=pageto.replace("to","");
	loadlanXml("page","page_");
	
	desc = formatnum(desc,1);
	
	
	if(allnums<1){
		document.write("<div class='norecord'>"+lang("norecord")+"</div>");
		return;
	}
	pagenums=Math.ceil(allnums/pagesize);//总页数
	var group_pernum=10,pageon;
	
	if(pageto=="htmlpageto"){
		pageon = getHtmlPageon();
	}else if(pageto=="inhtmlpageto"){
		pageon=getInHtmlPageon();
	}else{
		pageon=formatnum(getPra(can),1);//当前页
	}

	
	var groups=Math.ceil(pagenums/group_pernum) //一共分多少组
	var groupon=Math.ceil(pageon/group_pernum) //当前页码位于多少组
	prepage=pageon-1;
	nextpage=pageon+1;
	pregroup=pageon-group_pernum;
	nextgroup=pageon+group_pernum;
	if(prepage<1){prepage=1;}
	if(nextpage>pagenums){nextpage=pagenums;}
	if(pregroup<1){pregroup=1;}
	if(nextgroup>pagenums){nextgroup=pagenums;}	
	
	
//	var funstr="<table class='btn text2' border='0' cellpadding='0' cellspacing='0' align='center'><tr>";
	var funstr="";
	funstr+="<td>";
	
	if(pageon==1){
	//	funstr+="<a class='page_no' title='"+t(1)+"'>"+t(16)+"</a>";
		funstr+="<a class='no_pre' title='"+t(3)+"'>"+t(14)+"</a>";
	}else{
		//funstr+="<a href='javascript:"+pageto+"(1);' title='"+t(1)+"'>"+t(16)+"</a>";//首页
		funstr+="<a href='javascript:"+pageto+"("+prepage+");' title='"+t(3)+"'>"+t(14)+"</a>";//上一页
	}
//	funstr+="</td><td>";
	if(groups>1&&pageon>group_pernum){
	//	funstr+="<a href='javascript:"+pageto+"("+pregroup+");' title='"+t(11)+"'>"+t(18)+"</a>";//上一组
	}
	
	for(var i=(groupon-1)*group_pernum+1;i<=groupon*group_pernum;i++){
		if(i>pagenums){break;}
		if(i==pageon)		
         		funstr+="<a class='current' title='"+t(9)+i+t(8)+"'>"+i+"</a>";//转到某页
        else 
				funstr+="<a id='p"+i+"' href='javascript:"+pageto+"("+i+");' title='"+t(9)+i+t(8)+"'>"+i+"</a>";//转到某页
	}
	
	if(groupon<groups&&pageon!=pagenums){
	//	funstr+="<a href='javascript:"+pageto+"("+nextgroup+");' title='"+t(12)+"'>"+t(19)+"</a>"; //下一组
	}
	//funstr+="</td><td>";
	if(pageon==pagenums){
		funstr+="<a class='no_next' title='"+t(4)+"'>"+t(15)+"</a>";
		//funstr+="<a class='page_no' title='"+t(2)+"'>"+t(17)+"</a>";
	}else{
		funstr+="<a href='javascript:"+pageto+"("+nextpage+");' title='"+t(4)+"'>"+t(15)+"</a>";//下一页
	//	funstr+="<a href='javascript:"+pageto+"("+pagenums+");' title='"+t(2)+"'>"+t(17)+"</a>";//尾页
	}
//	funstr+="</td>";
///	if(desc)funstr+="<td>&nbsp;Pages:"+pageon+'/'+pagenums+'&nbsp;totals&nbsp;'+allnums+"&nbsp;Turn to&nbsp;";
//	if(desc)funstr+="<input onKeyUp='onlynum(this,1);' id='pagetonums' size='2' value='"+pageon+"' />&nbsp;<input type='button' value='"+t(10)+"' class='mybutton' onclick='"+pageto+"(getId(\"pagetonums\").value);' /></td>";
	funstr+="</tr></table>";

	document.write(funstr);
	var obj = getId("p"+pageon);
	if(obj)obj.className="page_on";
}