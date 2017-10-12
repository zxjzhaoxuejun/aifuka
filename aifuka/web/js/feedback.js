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

function htmlpageto(page)
{
	var pageon=getHtmlPageon();
	var p=pagenums-pageon+1;
	var p2=pagenums-page+1;
	if(p2>pagenums||p2<1){return false;}
	if(location.href.indexOf("/"+page_prefix+p)<1){
		location.href+=page_prefix+p2+"/";
	}else{
		location.href=location.href.replace("/"+page_prefix+p,"/"+page_prefix+p2);
	}
}
function getHtmlPageon()
{
	var oRegex=new RegExp("\/"+page_prefix+"([^\/]+)","i");
	var oMatch=oRegex.exec(location.href);
	if(oMatch&&oMatch.length>0)
		page=urldecode(oMatch[1]);
	else
		page=pagenums;
	return pagenums-page+1;
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

/////
function ajax(url,secid){

	if(url.indexOf("?")>=0){url=url+"&alzCmsDefault="+Math.random();}else{url=url+"?alzCmsDefault="+Math.random();}
	var responseStr=J.ajax({url:url,async:false}).responseText;
	if(getId(secid)){getId(secid).innerHTML=responseStr;}
	
	return responseStr;
}

function isTelephone(obj)// 正则判断
{ 

   // var pattern=/(^(\d{2}-)?\d{2,4}-(\d{5,8})$)|(^(\(\d{2}\))?\d{2,4}-(\d{5,8})$)/;
   var pattern=/^\d{6,16}$/;
    if(pattern.test(obj)) { 
       return true; 
    } 
    else { 
       return false; 
    } 
} 
function isEmail(email){
	var patrn= /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
	if (!patrn.exec(email)) return false;
	return true;
}
 

//添加留言
function diyformsubmit(form){
	var lanstr=getPageLanStr();
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	var username = trim(document.getElementById("username").value);	
	if(!username){
		if(lanstr=="zh_cn"){
			alert("联系人不能为空！");
		}else{
			alert("The contact man is required！");
		}
	}
    
	var telephone = document.getElementById("telephone").value;
	 if(!isTelephone(telephone)) {
		alert(phone_vali);
		return false;	
	}
	var email = document.getElementById("email").value;
	 if(!isEmail(email)) {
		alert(email_vali);
		return false;	
	}
	if(!posted){ajax_post(incpath+"setValue.php?action=diyformsave",valuestr,"_diyform");posted=true;}
	return false;
}
//添加留言
function diyformsubmit1(form){
	var lanstr=getPageLanStr();
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	var username = trim(document.getElementById("username").value);	
	alert("fda");
	if(!username){
		if(lanstr=="zh_cn"){
			alert("联系人不能为空！");
		}else{
			alert("The contact man is required！");
		}
	}
    
	var email = document.getElementById("email").value;
	 if(!isEmail(email)) {
		alert(email_vali);
		return false;	
	}
	var telephone = document.getElementById("telephone").value;
	 if(!isTelephone(telephone)) {
		alert(phone_vali);
		return false;	
	}
	if(!posted){ajax_post(incpath+"setValue.php?action=diyformsave",valuestr,"_diyform");posted=true;}
	return false;
}
function ajax_post(url,urlcs,id){

	if(Null(id))id="ed";
	var xmlhttp=ajax_driv();
	if(url.indexOf("alzCmsDefault")<0){
		if(url.indexOf("?")<0){url+="?alzCmsDefault="+Math.random();}else{url+="&alzCmsDefault="+Math.random();}
	}
	xmlhttp.open("post",url,true);
	xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlhttp.onreadystatechange=function(){
		//if(xmlhttp.readyState<4){ 

		//	try{eval("ajax_posting"+id+"()");
		//	}catch(e){lock_button();}
		//}
		if(xmlhttp.readyState==4){
			var response=xmlhttp.responseText;
			var msg = response.split(';');
			try{eval("ajax_post"+id+"(response)");}catch(e){sucgoto(msg[0],0);}
		}
	}
	urlcs=rtrim(urlcs,"&");	
	xmlhttp.send(urlcs);
}

function ajax_post_diyform(sec){
	var msg = sec.split(';');
	
	alert(msg[0]);
	document.location.reload() 
}
//获取留言
function feedbackContent(classid,lanstr)
{
	var page=getPra("page");
	ajaX(webpath+"include/feedback.php?action=getContent&classid="+classid+"&lanstr="+lanstr+"&page="+page,"feedbackcontent");
}

function ajaX(url,secid,str)
{

	var xmlhttp=ajax_driv(),obj;
	if(url.indexOf("?")>=0)	{url+="&alzCmsDefault="+Math.random();}else{url+="?alzCmsDefault="+Math.random();}

	xmlhttp.open("get",url,true);
	if(typeof(secid)=="object"){obj=secid;}else{obj=getId(secid);}
	if(Null(str))str="<img src='"+webpath+"images/loading3.gif' />";

	xmlhttp.onreadystatechange=function()
	{
		if(xmlhttp.readyState<4&&obj){	obj.innerHTML=str;}		
		if(xmlhttp.readyState==4)
		{		
			var response=xmlhttp.responseText;

			if(obj)obj.innerHTML=response;
		}
	};		
	xmlhttp.send(null);	
}
function getFormValue(form,postName,andstr,andstr2){
	if(Null(postName))postName="post_";
	if(Null(andstr))andstr="&";
	if(Null(andstr2))andstr2=",";
	var funstr="alzCmsDefault=1&",tags=form.elements,checkboxed="|",oldvalue="",newvalue="";
	for(var i=0;i<tags.length;i++){
		var obj=tags[i];

		var theclassName=obj.className;	
		if(theclassName.indexOf(postName)>=0){
			var ckstr=theclassName.split(postName)[1];
			if(ckstr!=""&&obj.value==""){
				alert(ckstr);
				obj.focus();
				posted=false;
				return "{false}";
			}else{
				switch(obj.type){
					case "checkbox":
						if(obj.checked){
							if(checkboxed.indexOf("|"+obj.name+"|")<0)funstr+=obj.name+"="+getCheckbox(obj.name,andstr2)+andstr;
							checkboxed+=obj.name+"|";
						}
						break;
					case "radio":
						if(obj.checked){funstr+=obj.name+"="+urlencode(obj.value)+andstr;}
						break;
					default:
						if(funstr.indexOf("&"+obj.name+"=")>=0)//同名参数
						{
							oldvalue=getCan(funstr,obj.name);
							newvalue=oldvalue+andstr2+urlencode(obj.value);
							funstr=funstr.replace("&"+obj.name+"="+oldvalue,"&"+obj.name+"="+newvalue);
						}else{
							funstr+=obj.name+"="+urlencode(obj.value)+andstr;
						}
				}
			}		
		}
		if(theclassName.indexOf("fckEdit_")>=0){
			var fckValue=getFCK(obj.id);			
			if(fckValue=="null"||fckValue=="false"||fckValue===null||fckValue===false){
				alert("编辑器尚未加载完毕，请稍侯……\r\n==========================================\r\n1、若长时间出现此信息，请刷新页面；\r\n2、确认编辑内容不单为“null”、“false”。");
				return "{false}";
			}else{funstr+=obj.name+"="+urlencode(fckValue)+andstr;}
		}
	}
	funstr = funstr.replace(/\+/g, "[$add]");
	return funstr;
}

function getFormValue2(form,postName,andstr){
	var value=getFormValue(form,postName,andstr);
	var funstr=rePlace(value,postName+"=","");
	funstr=trim(funstr,",");
	return funstr;
}