/* The common function javascript file */
var cs_cbgc="#efefef",lanXmlDoc,thefunName,setImged,posted=false;
var promodel=new Array();
promodel[0]=1;
promodel[1]=2;
promodel[2]=3;
var w3c=(document.getElementById)?true:false;
var agt=navigator.userAgent.toLowerCase();
var ie=((agt.indexOf("msie")!=-1)&&(agt.indexOf("opera")==-1)&&(agt.indexOf("omniweb")== -1)); 
function ieBody(){return (document.compatMode&&document.compatMode!="BackCompat")?document.documentElement:document.body;} 
var dtd;
if(document.compatMode == "BackCompat"){dtd=false;}else{dtd=true;}

function clientWidth(){return ieBody().clientWidth;}
function clientHeight(){return ieBody().clientHeight;}
function scrollWidth(){return ieBody().scrollWidth;}
function scrollHeight(){return ieBody().scrollHeight;}
function scrollLeft(){return ie?ieBody().scrollLeft:window.pageXOffset;}
function scrollTop(){return ie?ieBody().scrollTop:window.pageYOffset;}
function getId(id){return document.getElementById(id);}
function getName(str){return document.getElementsByName(str);}
function getTag(str){return document.getElementsByTagName(str);}
function getFid(id){return parent.document.getElementById(id);}
function getFCK(id){var s=FCKeditorAPI.GetInstance(id).GetXHTML(true);if(s!=""&&s!="<br />"){return s;}else{return "";}}
window["myBrowser"]={};(function(){
if(myBrowser.platform) return;
var ua = window.navigator.userAgent;
myBrowser.platform = window.navigator.platform;
myBrowser.firefox = ua.indexOf("Firefox")>0;
myBrowser.opera = typeof(window.opera)=="object";
myBrowser.ie = !myBrowser.opera && ua.indexOf("MSIE")>0;
myBrowser.mozilla = window.navigator.product == "Gecko";
myBrowser.netscape= window.navigator.vendor=="Netscape";
myBrowser.safari= ua.indexOf("Safari")>-1;
if(myBrowser.firefox) var re = /Firefox(\s|\/)(\d+(\.\d+)?)/;
else if(myBrowser.ie) var re = /MSIE( )(\d+(\.\d+)?)/;
else if(myBrowser.opera) var re = /Opera(\s|\/)(\d+(\.\d+)?)/;
else if(myBrowser.netscape) var re = /Netscape(\s|\/)(\d+(\.\d+)?)/;
else if(myBrowser.safari) var re = /Version(\/)(\d+(\.\d+)?)/;
else if(myBrowser.mozilla) var re = /rv(\:)(\d+(\.\d+)?)/;
if("undefined"!=typeof(re)&&re.test(ua))
myBrowser.version = parseFloat(RegExp.$2);
})(); 

var arr=new Array();

/* Email format validate */
function email_ck(obj)
{
	var v=obj.value;
	var n=obj.name;
	var objs = getId("emailsec");	
	var reg=/^[0-9a-zA-Z_\-\.]+@[0-9a-zA-Z_\-]+(\.[0-9a-zA-Z_\-]+)*$/;
	if(reg.test(obj.value)){
		objs.innerHTML="(邮箱是找回密码的唯一途径)";
		return true;
	}else{
		objs.innerHTML="邮箱格式错误!";
		return false;
	}
}

/* password check */
function pwdcheck(obj)
{
	var objs=getId("passwordck1");
	var pwd1 = document.forms["registerform"].elements["pwd1"].value;
	var pwd2=obj.value;
	checkPwd2(pwd2);
	if(pwd1!==pwd2)
	{
		objs.innerHTML="两次输入的密码不一致！";
		return false;
	}else
	{
		return true;	
	}
}
function password_ck(obj)
{
	checkPwd(obj.value);
}

function checkPwd(pwd){
	var obj=getId("passwordck");
	if (pwd == ""){
		obj.innerHTML="";
	}else if (pwd.length < 3){
		obj.innerHTML="太短";
	}else if(!isPassword(pwd) || !/^[^%&]*$/.test(pwd)){
		obj.innerHTML="";
	}else{
		obj.innerHTML="";
	}
}
function checkPwd2(pwd){
	var obj=getId("passwordck1");
	if (pwd == ""){
		obj.innerHTML="";
	}else if (pwd.length < 3){
		obj.innerHTML="太短";
	}else if(!isPassword(pwd) || !/^[^%&]*$/.test(pwd)){
		obj.innerHTML="";
	}else{
		obj.innerHTML="";
	}
}
function isPassword(str){
	if (str.length < 3)return false;
	var len;
	var i;
	len = 0;
	for(i=0;i<str.length;i++){if(str.charCodeAt(i)>255) return false;}
	return true;
}
//返回密码的强度级别 
function checkStrong(pwd){ 
	modes=0; 
	for(i=0;i<pwd.length;i++){ 
		modes|=charMode(pwd.charCodeAt(i)); 
	} 
	return bitTotal(modes);
}
//计算出当前密码当中一共有多少种模式 
function bitTotal(num){ 
	modes=0; 
	for (i=0;i<4;i++){ 
		if (num & 1) modes++; 
		num>>>=1;
	} 
	return modes; 
}
function charMode(iN){ 
	if (iN>=48 && iN <=57) //数字 
		return 1; 
	if (iN>=65 && iN <=90) //大写字母 
		return 2; 
	if (iN>=97 && iN <=122) //小写 
		return 4; 
	else 
		return 8; //特殊字符 
}


function desc_show(name,cases)
{
	var desc=getId(name+"_desc");
	var yes=getId(name+"_yes");
	var no=getId(name+"_no");
	if(desc)desc.style.display="none";
	if(yes)yes.style.display="none";
	if(no)no.style.display="none";
	var o=eval(cases);
	if(o)o.style.display="";
}

function mobile_ck(obj)
{
	var o=obj;
	var v=o.value;
	var objdesc=getId("telsec");	
	if(v.match(/^\d+$/) == null){
		objdesc.innerHTML="手机号应该为纯数字";
		return false;
	}else if(v.length<11){
		objdesc.innerHTML="手机号应该为至少11位";		
		return false;
	}else{
		objdesc.innerHTML="";	
		return true;	
	}
	
}

function selectchange(obj1,obj2)
{
	var v=obj1.value;
	var o=J(obj2)[0];
	if(o){try{o.value=v;o.innerHTML=v;}catch(e){};}
}




function register(form){
	var valuestr=getFormValue(form);
	if(valuestr.checked==false)
	{
		alert("请查看中启莱用户许可协议！");
		return false;
	}
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	if(!posted){ajax_post(webpath+"members.php?action=register_update",valuestr,"_register");posted=true;}
	return false;
}

function register1(){
	$.post("/web/member.php?action=register_update1",$("#form1").serialize(),function(data){
			//alert(data);
			if(data=="success"){
				alert("修改成功！");
				location.href="/web/register.php";
			}else{
				alert(data);
			}
			
		})
}

function ajax_post_register(sec){
	sucgoto("<b>"+sec+"</b>","my_info.php",3);
//	window.location.href="my_info.php";
//	goto(webpath+"my_info.php");
//	if(sec.indexOf("{ok}")>0)
//	{
//		alert(sec);
		//sucgoto("<b>"+sec+"</b>","",3);
		//goto(webpath+"my_info.php");
//	}

}
function register_edit(form){
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	if(!posted){ajax_post(webpath+"members.php?action=register_edit",valuestr,"_edituser");posted=true;}
	return false;
}
function ajax_post_edituser(sec){
//	alert(sec);
    window.location.reload();
}


function userLoginform(form)
{
	var valuestr=getFormValue(form);
alert(valuestr);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	ajax_post(webpath+"members.php?action=logincheck",valuestr);
	return false;
}

function ajax_posted(sec)
{
	if(sec.indexOf("{ok}")<0)
		sucgoto("<u>"+"用户名或密码错误！"+"</u>","",1);
	else
		//window.location.href="admin_index.php";
		goto(webpath+"my_info.php");
}

/*
function userLoginform(form)
{
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	if(!posted){ajax_post(webpath+"members.php?action=logincheck",valuestr,"_checklogin");posted=true;}
	return false;	
}

function ajax_post_checklogin(sec)
{
	if(sec.indexOf("{ok}")<0)
		sucgoto("<u>"+"用户名或密码错误！"+"</u>","","");
	else
		goto(webpath+"index.php?topclassid=8&classid=8&lanstr=zh_cn");
}
*/



function user_quit(){
	ajax("members.php?action=username_quit");
	//location.href="index.php";
	goto("my_info.php");
}
function job_update(form)
{
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	if(!posted){ajax_post(incpath+"job.php?action=job_update",valuestr,"_update");posted=true;}
	return false;		
}
function safenames(str,ck){

	var charset="~!#$%^&*()+|\\=-{}[];:\"'<>?/,. ";
	var temp;
	if(typeof(ck)=="undefined")ck=true;	
	for(var i=0;i<charset.length;i++)
	{
		temp=charset.charAt(i);
		if(ck){
			str=rePlace(str,temp,"");
		}else{
			if(str.indexOf(temp)>=0)return false;
		}
	}
	if(ck&&(str.length>100||str.length<1))return false;
	return str;
}
function callServer(username)
{

	var sec=getId("usersec");
	if(username.value=="")
	{
		sec.innerHTML="用户名不能为空";
		return false;
	}else if(!safenames(username.value,false))
	{
		if(typeof(sec)=="object")sec.innerHTML="用户名格式错误：包含非法字符或长度在允许范围之外！";		
		username.focus();
		return false;
	}
	ajax(webpath+"members.php?action=check_username&username="+username.value,"usersec");
	return true;
}
function diyformsubmit(form){	
	
	var valuestr=getFormValue(form);
	
	
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	if(!posted){ajax_post(incpath+"setValue.php?action=diyformsave",valuestr,"_diyform");posted=true;}
	return false;
}



function admin_member(form){
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	var user_name=form.username;
	if(!safenames(user_name.value)){alert("用户名格式错误：包含非法字符或长度在允许范围之外！");user_name.value="";user_name.focus();return false;}
	if(!posted){ajax_post(incpath+"setValue.php?action=userLogin",valuestr,"_userLogin");posted=true;}
	return false;
}
function admin_dumpsql(form)
{
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	if(!posted){ajax_post(adminpath+"admin_data.php?action=dumpsql",valuestr,"_dumpsql");posted=true;}
	return false;		
}

function ajax_post_diyform(sec){
	if(sec=="errcode"){
		posted=false;
		sucgoto("<u>"+text_coderight+"</u>","");
	}else{
		sucgoto("<b>"+sec+"</b>",0);
	}
}

function ajax_post_userLogin(sec){
	if(sec=="errcode"){
		posted=false;
		sucgoto("<u>"+text_coderight+"</u>","");
	}else{
		sucgoto("<b>"+sec+"</b>",0);
	}
}

function msg()
{
	sucgoto("<u>"+"很抱歉，请等待管理员审核之后再查看。"+"</u>","","2");
}

function divIo(id)
{
	var obj=getId(id);
	if(obj){
		var dis=obj.style.display;
		dis=dis=="none"?"":"none";
		obj.style.display=dis;
	}
}

function ajax_driv(){
	var xmlhttp = null;
	if (window.ActiveXObject){
		var versions = ['Microsoft.XMLHTTP', 'MSXML6.XMLHTTP', 'MSXML5.XMLHTTP', 'MSXML4.XMLHTTP', 'MSXML3.XMLHTTP', 'MSXML2.XMLHTTP', 'MSXML.XMLHTTP'];
		for (var i = 0; i < versions.length; i ++ ){
			try{
			  xmlhttp = new ActiveXObject(versions[i]);
			  break;
			}catch (ex){
				continue;
			}
		}
	}else{
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

function menuco(thisobj,id){
	var obj=getId(id);
	if(obj){
		var olddis=obj.style.display;
		obj.style.display="";
		thisobj.onmouseout=function(){obj.style.display=olddis;};
	}
}

function selectIo(io,doc)
{
	if(myBrowser.ie){var v=formatnum(myBrowser.version,0);if(v>6)return;}
	var dis;
	if(Null(doc))doc=document;
	if(io)dis="";else dis="none";
	var selects=doc.getElementsByTagName("Select"); 
	for(var i=0;i<selects.length;i++){selects[i].style.display=dis;} 
}

function setbanner(id){
	var src;
	var obj=getId("banner");
	if(id=="index"){src=webpath+"images/bannerindex.gif";}
	else if(id=="job"){src=webpath+"images/bannerjob.gif";}
	else{
		var sec=ajax(webpath+"fun/getValue.asp?sql=select banner from alz_class where classid="+id);
		if(sec!="")	{src=webpath+"images/banner/"+sec;}
		else{src=webpath+"images/banner1.gif";}
	}
	obj.style.background="url('"+src+"') center";
}

function setheadpic(obj){
	var v=obj.value;
	var img=getId("headpic");
	img.src=webpath+"images/head/"+v+".gif";
}

function sucgoto(mes,url,s){

	pageloaded();
	if(Null(url))url="";
	if(typeof(s)=="undefined"||s=="")s=sucgoto_times;
	if(s==0){goto(url);return;}
	var loop,times=1;
	if(mes!=""){
		sucgotoObj=getId("sucgoto");		
		if(!sucgotoObj){alert("ERROR-ALZ-JS-SUCGOTOS");}		
		sucgotoObj.innerHTML="<div class='sucgoto'><div class='tit'><div>系统提示：</div></div><h1>"+mes+"</h1><h2><a href='#' title='"+text_clickgo+"'>&gt;&gt; <div>"+text_jumping+"：<span id='gototime'>"+s+"</span> "+text_seconds+" </div> &lt;&lt;</a></h2></div>";
		var tW=sucgotoObj.clientWidth;
		var tH=sucgotoObj.clientHeight;
		sucgotoObj.style.display="none";
		var pW=clientWidth();
		var pH=clientHeight();
		var pTop=scrollTop();
		sucgotoObj.style.top=(pH-tH)/2+pTop+"px";
		sucgotoObj.style.left=(pW-tW)/2+"px";
		sucgotoObj.style.display="";
	} 
	function DelayS(){
		if((url!=""||url=="0")&&times>=s){
			goto(url);
			clearInterval(loop);
			return;
		}else if(times>=s){
			if(sucgotoObj){if(sucgotoObj)sucgotoObj.style.display="none";}
			unlock_button();
			clearInterval(loop);
			return;
		}
		getId("gototime").innerHTML=s-times;
		times++;
	}
	loop=setInterval(DelayS,1000);
	sucgotoObj.onclick=function(){times=1;clearInterval(loop);goto(url);if(sucgotoObj){sucgotoObj.innerHTML="";sucgotoObj.style.display="none";};unlock_button();return;}
	document.onkeydown=function(e){
		times=1;e=window.event||e;if(e.keyCode==116)return;clearInterval(loop);goto(url);if(sucgotoObj){sucgotoObj.innerHTML="";sucgotoObj.style.display="none";};unlock_button();return;
	}
}

function sucgotos(mes,url,s){
	pageloaded();
	s=formatnum(s,sucgoto_times);
	var loop,times=1;
	if(mes!=""){
		sucgotoObj=getId("sucgotos");
		if(!sucgotoObj){alert("ERROR-ALZ-JS-SUCGOTOS");}
		var pW=clientWidth();
		var pH=clientHeight();
		var pTop=scrollTop();
		var tW=sucgotoObj.clientWidth;
		var tH=sucgotoObj.clientHeight;
		sucgotoObj.style.top=(pH-tH)/2+pTop+"px";
		sucgotoObj.style.left=(pW-tW)/2+"px";		
	} 
	function DelayS(){
		if((url!=""||url=="0")&&times>=s){
			goto(url);
			clearInterval(loop);
			return;
		}else if(times>=s){
			if(sucgotoObj){if(sucgotoObj)sucgotoObj.style.display="none";}
			unlock_button();
			clearInterval(loop);
			return;
		}
		getId("gototime").innerHTML=s-times;
		times++;
	}
	if(s==1){DelayS();}else{loop=setInterval(DelayS,1000);}
	sucgotoObj.onclick=function(){times=1;clearInterval(loop);goto(url);if(sucgotoObj){sucgotoObj.innerHTML="";sucgotoObj.style.display="none";};unlock_button();return;}
	document.onkeydown=function(e){
		times=1;e=window.event||e;if(e.keyCode==116)return;clearInterval(loop);goto(url);if(sucgotoObj){sucgotoObj.innerHTML="";sucgotoObj.style.display="none";};unlock_button();return;
	}
}

function goto(url){
	switch(url){
		case "":break;
		case null:break;
		case "self":location.href=location.href.split("?")[0];break;
		case 0:location.href=location.href.split("#")[0];break;
		case "0":location.href=location.href.split("#")[0];break;
		case 1:history.go(-1);break;
		case "1":history.go(-1);break;
		case 2:history.go(-2);break;
		case "2":history.go(-2);break;
		default:location.href=url;
	}
}

function pageScollTop(){
	var sH=scrollHeight();
	var pageScollTop=formatnum(getCookie("pageScollTop"),0);
	if(pageScollTop!=0){
		try{ieBody().scrollTop=pageScollTop;}catch(e){window.pageYOffset=pageScollTop;};
		setCookie("pageScollTop",0);
	}
}

function bgcc(e,tags,color,overtag){
	if(Null(color))color=cs_cbgc;
	if(Null(overtag)){overtag=tags=="TR"?"TABLE":tags;}
	if(!e.target)e.target = e.srcElement;
	var el = e.target;
	if(!el)return;
	if(el.nodeName!=tags){
		while(el.parentNode&&el.parentNode.tagName!=tags&&el.nodeName!=overtag)el=el.parentNode;
		if(el.nodeName!=overtag)el=el.parentNode;
	}
	if(el&&el.nodeName==tags){
		el.style.backgroundColor=color;
		el.onmouseout=function(){el.style.backgroundColor="";};
	}
}

function inputc(e,color){
	if(Null(color))color="#D9FAD9";
	if(!e.target)e.target = e.srcElement;
	var el = e.target;
	if(!el)return;
	if(el.nodeName=="INPUT"&&(el.type=="text"||el.type=="password")||el.nodeName=="TEXTAREA"){
		var theclear=false;
		if(el.className.indexOf("clear")>=0)theclear=true;		
		var inputdefvalue=el.value;		
		el.onfocus=function(){
			if(theclear)el.value="";
			el.style.backgroundColor=color;
		}
		el.onblur=function(){
			if(theclear&&el.value=="")el.value=inputdefvalue;
			el.style.backgroundColor="";
			var reg=/function\((.*)\){(.*)}/i;
			var matchs=reg.exec(el.className);
			if(matchs&&matchs[1]=="onBlur")
			{
				var fun=matchs[2];
				eval(fun);
			}
		}
	}
}

function loadImg(srcstr){
	var imgArr=srcstr.split(",");
	for(i=0;i<imgArr.length;i++){eval("var imgs"+i+"=new Image();imgs"+i+".src=imgArr["+i+"];");}
}

function target(id,cases){
	var obj=getId(id);
	if(obj){
		var objs=obj.getElementsByTagName("a");	
		if(objs){for(i=0;i<objs.length;i++){objs[i].target=cases;}}
	}
}

function noright(){noright2(window.document);}
function noright2(obj){
	obj.oncontextmenu = function(){return false;}
	obj.ondragstart = function(){return false;}
	obj.onselectstart = function(){return false;}
	obj.onselect = function(){obj.selection.empty();}
	obj.oncopy = function(){obj.selection.empty();}
	obj.onbeforecopy = function(){return false;}
}

function haveright(){haveright2(window.document);}
function haveright2(obj){
	obj.oncontextmenu = function(){return true;}
	obj.ondragstart = function(){return true;}
	obj.onselectstart = function(){return true;}
	obj.onbeforecopy = function(){return true;}
}

function lang(lanstr){
	if(lanstr.indexOf("?")>0)
		return eval(lanstr.split("?")[0]);
	else
		return eval(lanstr);
}

function Null(str){
	if(str=="0")return false;
	if(typeof(str)=="undefined"||str=="")
		return true;
	else
		return false;
}

function formatnum(nums,err){
	if(nums=="0")return 0;
	if(typeof(nums)=="undefined"||nums==""||isNaN(nums))
		return parseInt(err);
	else
		return parseInt(nums);
}

function pageloading(){
	var objload=getId("pageloading");
	var pagewidth=clientWidth();
	var pageheight=clientHeight();
	var pagescrollTop=scrollTop();
	if(objload){
		objload.style.top=(pageheight-objload.clientHeight+pagescrollTop)/2-30;
		objload.style.left=(pagewidth-objload.clientWidth)/2;
	}
}

function pageloaded(){
	var obj=J("#pageloading")[0];
	if(obj){obj.style.display="none";}
}

function minheight(id,h){
	var obj=getId(id);
	if(obj){
		var h2=obj.offsetHeight;
		if(h2>h){obj.style.height="auto";}else{obj.style.height=h+"px";}
	}
}

//得到URL参数值
function getPra(paramName){
	var oRegex=new RegExp("[\?&]"+paramName+"=([^&]+)","i") ;
	var oMatch=oRegex.exec(location.search);
	if(oMatch&&oMatch.length>0)
		return urldecode(oMatch[1]);
	else
		return "";
}

function getCan(valuestr,paramName){
	var oRegex=new RegExp("[\?&]"+paramName+"=([^&]+)","i") ;
	var oMatch=oRegex.exec(valuestr);
	if(oMatch&&oMatch.length>0)
		return urldecode(oMatch[1]);
	else
		return "";
}

function switchdiv(namestr,n,a,b){
	for(var i=a;i<=b;i++){ getId(namestr+i).style.display = "none";}
	getId(namestr+n).style.display = "block";
}

//数字格式控制
function onlynum(obj,def){
	def=formatnum(def,0);
	var num=obj.value.replace(/\D/g,'');
	obj.value=formatnum(num,def);
}

//搜索
function Search_check(str1,str2){
	if (getId("keyword").value=="" || getId("keyword").value==str1){
		alert(str2);
		getId("keyword").focus();
		return false;
	}
}

//关键字写入录入框
function ctag(obj,str,types){
	if(getId(str).value==""){
		getId(str).value=obj.title;
	}else{
		getId(str).value=getId(str).value+ "," +obj.title
		if(types==1||obj.title==""){getId(str).value=obj.title;}
	}
}



function switchTab(n,a,b,str){
	for(var i=a;i<=b;i++){
		if(i!=n){
			getId("ContentBody"+n).style.display = "block";
			getId("ContentBody"+i).style.display = "none";
			getId("ListTitle"+i).className = str+"off";
			getId("ListTitle"+n).className = str+"on";
		}
	}
	if(getId("ShowAll")){getId("ShowAll").className = str+"off";}
}

//选择操作（typeid: 1全选 2反选 3取消）
function sel(name,typeid){ 
	var obj=getName(name)
	if(typeid==1){
		for(i=0;i<obj.length;i++){obj[i].checked=true;}
		if(getId("ids"))getId("ids").checked=true;
	}else if(typeid==3){
		for(i=0;i<obj.length;i++){obj[i].checked=false;}	
		if(getId("ids"))getId("ids").checked=false;
	}else{
		for(i=0;i<obj.length;i++){if(obj[i].checked){obj[i].checked=false;}else{obj[i].checked=true;}}	
	}
}

//层显示与关闭
function showmenu(menuid){
	var obj=getId("menuid"+menuid);
	var olddisplay="";
	if(obj){olddisplay=obj.style.display;}
	var obj2=J(".smallclass");
	for(var i=0;i<obj2.length;i++){obj2[i].style.display="none";}	
	if(olddisplay == "none"){obj.style.display = "";}
	else{obj.style.display = "none";}
}

//翻页跳转
function topage(obj){
	var jmpurl=obj.value;
	if(jmpurl!=''){location.href=jmpurl;}else{	this.selectedindex=0;}
}

//操作判断
function confirm_do(){return confirm("确定执行？");}

function autoIframe(obj,cases){
	obj.style.display="";
	function autoIframeLoop(){
		try{
			var bHeight=obj.contentWindow.document.body.scrollHeight;
			var dHeight=obj.contentWindow.document.documentElement.scrollHeight;
			var height=Math.max(bHeight,dHeight);
			obj.height=height;
		}catch(e){}
	}
	if(!!cases){setInterval(autoIframeLoop,200);}else{autoIframeLoop();}
	var obj2=getId("Iframeload");	   
	if(obj2){obj2.style.display="none";}
}

function sucmsg(mes,url){
	if(mes!=""){alert(mes);}
	if(url!=""){location.href=url;}
}

//拖动对象
var move_obj={o:null,z:0,x:0,y:0}
function move_(e){ 
    e=window.event||e;
    var oDragHandle = e.target || event.srcElement; 
    var topElement = "HTML"; 
    while (oDragHandle.tagName != topElement && oDragHandle.className != "alt_"){
		oDragHandle =oDragHandle.parentNode||oDragHandle.parentElement;
	} 
    if (oDragHandle.className=="alt_"){ 
        isdrag = true; 
        move_obj.o = oDragHandle;
        move_obj.z=move_obj.o.style.zIndex;
        move_obj.o.style.zIndex=999;
        y = e.clientY-parseInt(move_obj.o.style.top+0); 
        x = e.clientX-parseInt(move_obj.o.style.left+0); 
        document.onmousemove=function moveMouse(e){
            e=window.event||e;
            if (move_obj.o)with(move_obj.o.style){
                var yy=e.clientY-y;if (yy<0)yy=0;else if (yy+move_obj.o.offsetHeight>document.body.clientHeight)yy=document.body.clientHeight-move_obj.o.offsetHeight;
                var xx=e.clientX-x;if (xx<0)xx=0;else if (xx+move_obj.o.offsetWidth>=document.body.clientWidth)xx=document.body.clientWidth-move_obj.o.offsetWidth;
                top=yy+"px";left=xx+"px";return false;
            }
        } 
        move_obj.o.onmouseup=function(){if(move_obj.o){document.onmousemove=null;move_obj.o.style.zIndex=move_obj.z;}move_obj.o=null;}
        return false; 
    } 
}

function showtime(){
	if(lanstr=="zh_cn"){
		t_1="星期一";
		t_2="星期二";
		t_3="星期三";
		t_4="星期四";
		t_5="星期五";
		t_6="星期六";
		t_7="星期日";
		t_8="年";
		t_9="月";
		t_10="日";
	}else{
		t_1="monday";
		t_2="tuesday";
		t_3="wednesday";
		t_4="thursday";
		t_5="friday";
		t_6="saturday";
		t_7="sunday";
		t_8="-";
		t_9="-";
		t_10="";
	}
	var d = new Date();
	var iYear = d.getFullYear();
	var iMonth = d.getMonth();
	var iDay = d.getDate();
	var sWeek = d.getDay();
	var sHour  = d.getHours();
	var sMinute = d.getMinutes();
	var sSecond = d.getSeconds();
	if(sWeek == 0) sWeek = "<font color='#ff0000'>"+t_7+"</font>";
	if(sWeek == 1) sWeek = t_1;
	if(sWeek == 2) sWeek = t_2;
	if(sWeek == 3) sWeek = t_3;
	if(sWeek == 4) sWeek = t_4;
	if(sWeek == 5) sWeek = t_5;
	if(sWeek == 6) sWeek = "<font color='#ff0000'>"+t_6+"</font>";
	if(sHour <= 9) sHour = "0" + sHour;
	if(sMinute <= 9) sMinute = "0" + sMinute;
	if(sSecond <= 9) sSecond = "0" + sSecond;
	sClock = iYear + t_8 + (iMonth + 1) + t_9 + iDay + t_10 + " " + sHour + ":" + sMinute + ":" + sSecond + "　"+sWeek;
	getId("clock").innerHTML = sClock;
	setTimeout("showtime()", 1000);
}

function rollleft(speed,idname,step){
	step=formatnum(step,1);
	var demo=getId(idname);
	var demo1=getId(idname+"1");
	var demo2=getId(idname+"2");
	demo2.innerHTML=demo1.innerHTML;
	function Marquee(){if(demo2.offsetWidth-demo.scrollLeft<=0){demo.scrollLeft-=demo1.offsetWidth;}else{demo.scrollLeft+=step;}}
	var MyMarRoll=setInterval(Marquee,speed);
	demo.onmouseover=function(){clearInterval(MyMarRoll);}
	demo.onmouseout=function(){MyMarRoll=setInterval(Marquee,speed);}	
}

function rollright(speed,idname,step){
	step=formatnum(step,1);
	var demo=getId(idname);
	var demo1=getId(idname+"1");
	var demo2=getId(idname+"2");
	demo2.innerHTML=demo1.innerHTML;
	function Marquee(){if(demo.scrollLeft<=0){demo.scrollLeft+=demo1.offsetWidth;}else{demo.scrollLeft-=step;}}
	var MyMarRoll=setInterval(Marquee,speed);
	demo.onmouseover=function(){clearInterval(MyMarRoll);}
	demo.onmouseout=function(){MyMarRoll=setInterval(Marquee,speed);}	
}
/*
function rollup(speed,idname){
	var demo=getId(idname); 
	var demo1=getId(idname+"1"); 
	var demo2=getId(idname+"2"); 

	demo2.innerHTML=demo1.innerHTML;
	var h1=demo.clientHeight;
	var h2=demo.scrollHeight/2;
	var h=h1>h2?h1:h2;
	function Marquee(){if(h<=demo.scrollTop){demo.scrollTop=0;}else{demo.scrollTop++;}}
	var MyMar=setInterval(Marquee,speed) 
	demo.onmouseover=function(){clearInterval(MyMar);} 
	demo.onmouseout=function(){MyMar=setInterval(Marquee,speed);}
}
*/
function rollup(speed,idname){
	var speed=20
	var demo=getId(idname);
	var demo1=getId(idname+"1"); 
	var demo2=getId(idname+"2"); 
		demo2.innerHTML=demo1.innerHTML;
	function Marquee(){
	if(demo2.offsetTop-demo.scrollTop<=0)
	demo.scrollTop-=demo1.offsetHeight
	else{
	demo.scrollTop++
	}
	}
	var MyMar=setInterval(Marquee,speed)
	demo.onmouseover=function() {clearInterval(MyMar)}
	demo.onmouseout=function() {MyMar=setInterval(Marquee,speed)}
}
function picView(This,url,e,w,h){	
	if(getId("picViewObj"))document.body.removeChild(getId("picViewObj"));
	w=formatnum(w,300);
	h=formatnum(h,300);
	if(Null(url))url=This.src;
	var pW=clientWidth();
	var pH=clientHeight();
	var sTop=scrollTop();
	var obj=document.createElement("img");
	document.body.appendChild(obj);
	obj.id="picViewObj";
	obj.style.display="none";
	obj.style.position="absolute";
	obj.style.border="1px solid #ccc";
	setImged=false;
	if(!page_on("alz_admin")){try{imgPlay(obj,5);}catch(e){}}
	This.onmousemove=function(e){
		obj.style.display="";
		e=window.event||e;
		var x=e.clientX,y=e.clientY,theTop=parseInt(y+sTop);
		setImg(obj,w,h,url);
		if(!setImged){
			obj.src=webpath+"images/loading.gif";
			obj.width=32;
			obj.height=32;
			obj.style.left=x+20+"px";
			obj.style.top=theTop+20+"px";
		}else{
			var theW=obj.width,theH=obj.height;
			if(theW+x>pW-50){obj.style.left=x-theW-20+"px";}else{obj.style.left=x+20+"px";}
			if(theH+y>pH-20){obj.style.top=sTop+pH-theH+"px";}else{obj.style.top=theTop+20+"px";}
		}
	}
	This.onmouseout=function(){document.body.removeChild(obj);setImged=false;}
}

//图片按比例缩放
function setImg(obj,w,h,url){
	w=formatnum(w,300),h=formatnum(h,300);
	if(Null(url))url=obj.src;
	var image=new Image(),ww,hh;
	image.onload=function(){
		setImged=true,obj.onload=null,obj.src=url;
		var wBh=image.width/image.height;

		if(wBh>=w/h)
		{
			ww=image.width>=w?w:image.width;
			hh=ww/wBh;
		}else
		{
			hh=image.height>=h?h:image.height;ww=hh*wBh;
		}
		obj.width=ww,obj.height=hh;
	}
	image.src=url;
}

//图片——链接类型 1为无 2为大图片 3为自定义
function linktypes(typeid){if(typeid=="3"){getId("link").style.display="";}else{getId("link").style.display="none";}}

function setCookie(name,value){
	var Days = 30;
	var exp = new Date();
	exp.setTime(exp.getTime() + Days*86400000);
	document.cookie=prefix+name+"="+urlencode(value)+";expires="+exp.toGMTString()+";path=/";
}
function setcookies(name,value){
	var Days = 30;
	var exp = new Date();
	exp.setTime(exp.getTime() + Days*86400000);
	document.cookie=prefix+name+"="+urlencode(value)+";expires="+exp.toGMTString()+";path=/";
}

function getCookie(name){
	var arr,reg=new RegExp("(^| )"+prefix+name+"=([^;]*)(;|$)");
	if(arr=document.cookie.match(reg)) return urldecode(arr[2]);
	else return '';
}
function getcookies(name){
	var arr,reg=new RegExp("(^| )"+prefix+name+"=([^;]*)(;|$)");
	if(arr=document.cookie.match(reg)) return urldecode(arr[2]);
	else return '';
}
   
function changemodel(types){
	if(types=="news"){
		var obj=getId("newslist");
		if(obj)	{if(obj.className=="newslist"){obj.className="newslist2";setCookie("newsmodel","2");}else{obj.className="newslist";setCookie("newsmodel","1");}}
	}else{
		var obj2=getId("pro_list");	
		if(types=="pro1"){if(obj2){obj2.className="pro_list1";setCookie("pro_model","pro_list1");changemodel2(1);}}
		if(types=="pro2"){if(obj2){obj2.className="pro_list2";setCookie("pro_model","pro_list2");changemodel2(2);}}
		if(types=="pro3"){if(obj2){obj2.className="pro_list3";setCookie("pro_model","pro_list3");changemodel2(3);}}
	}
}

function changemodel2(id){
	for(i=1;i<=3;i++){getId("promodel_"+i).src=getId("promodel_"+i).src.replace("on.gif",".gif");}
	getId("promodel_"+id).src=getId("promodel_"+id).src.replace(".gif","on.gif");
}

function setProModel(){
	var proClassName=getCookie("pro_model");
	if(proClassName)
		getId("pro_list").className=proClassName;
	else
		getId("pro_list").className="pro_list"+promodel[0];
	if(proClassName=="pro_list"+promodel[2])
	{		
		getId("promodel_"+promodel[0]).src=webpath+"images/view_mode_a.gif";
		getId("promodel_"+promodel[1]).src=webpath+"images/view_mode_b.gif";
		getId("promodel_"+promodel[2]).src=webpath+"images/view_mode_con.gif";
	}
	else if(proClassName=="pro_list"+promodel[1])
	{
		getId("promodel_"+promodel[0]).src=webpath+"images/view_mode_a.gif";
		getId("promodel_"+promodel[1]).src=webpath+"images/view_mode_bon.gif";
		getId("promodel_"+promodel[2]).src=webpath+"images/view_mode_c.gif";
	}
	else
	{
		getId("promodel_"+promodel[0]).src=webpath+"images/view_mode_aon.gif";
		getId("promodel_"+promodel[1]).src=webpath+"images/view_mode_b.gif";
		getId("promodel_"+promodel[2]).src=webpath+"images/view_mode_c.gif";
	}
}

function changeorder(types){
	var orders=getCookie(types+"_order_type");
	if(orders=="1")
	{
		setCookie(types+"_order_type",2);
		history.go(0);
	}else{
		setCookie(types+"_order_type",1);
		history.go(0);
	}	
}

function setProOrder(){
	var orders=getCookie("pro_order_type");
	var pro_order=getId("pro_order");
	if(orders==1){pro_order.src="images/view_mode_d.gif";}else{pro_order.src="images/view_mode_don.gif";}
}

function comefrom(typeid){	if(typeid&&typeid==1){setCookie("pro_list_url",location.href);}else{setCookie("new_list_url",location.href);}}

function backtolist(typeid){
	var url1=urldecode(getCookie("pro_list_url"));
	var url2=urldecode(getCookie("new_list_url"));
	if(typeid&&typeid==1&&url1!=""){location.href=url1;}else if(typeid&&typeid==2&&url2!=""){location.href=url2;}else{location.href="../../";}
}

function picclick(id,path){ajax2(path+"picclick.asp","id="+id+"&add=1");}

function randomChar(m,types){
	var tmp="";
	var str="0123456789poiuytrewqasdfghjklmnbvcxzQWERTYUIOPLKJHGFDSAZXCVBNM";
	var str1="0123456789poiuytrewqasdfghjklmnbvcxzQWERTYUIOPLKJHGFDSAZXCVBNM";
	if(types=="num"){str="0123456789";str1="0123456789";}
	if(types=="Num"){str="0123456789";str1="123456789";}
	if(types=="videobg"){str="0123456789";str1="12345";}
	for(var i=0;i<m;i++){
		if(i==0){tmp += str1.charAt(Math.ceil(Math.random()*100000000)%str1.length);}else{tmp += str.charAt(Math.ceil(Math.random()*100000000)%str.length);}
	}
	return tmp;
}

function get_Code(path){
	var Dv_CodeFile = path+"alz_admin/alz_GetCode.asp?t="+Math.random();
	var codeimg=getId("codeimg");
	if(codeimg)codeimg.src=Dv_CodeFile;
}

function submitForm(formID){ 
	if(window.event.ctrlKey&&window.event.keyCode==13){document.getElementById(formID).submit();} 
}

function copy(obj){
	obj.select();
	js=obj.createTextRange();
	js.execCommand("Copy");
}

function setFile(title,path,secid,other){
	DvWnd.open("文件管理",'alz_admin/file/index.asp?path='+path+'&secid='+secid+'&other='+other+'&_i='+Math.random());
}

function ding(typeid,id){
	ajaX(webpath+"fun/comment/ding.asp?typeid="+typeid+"&id="+id+"&add=1","dingnum","<img src='"+webpath+"images/loading3.gif' />");
	var dingA=J("#ding a")[0];
	if(dingA){dingA.innerHTML="谢谢参与";}
}

function ping(typeid,id,clicked){
	if(!!clicked){
		DvWnd.open("发表评论",webpath+"fun/comment/index.asp?add=1&typeid="+typeid+"&id="+id+"&lan="+cmslan,550,280);
	}else{
		ajaX(webpath+"fun/comment/ping.asp?typeid="+typeid+"&id="+id,"pingnum","<img src='"+webpath+"images/loading3.gif' />");
		var pingA=J("#ping a")[0];
		if(pingA){pingA.innerHTML="谢谢参与";}
	}
}

function seachtype(typeid){
	var obj=getId("keyword");
	var objform=getId("searchform");
	if(typeid==2){
		if(objform){objform.action="http://www.google.cn/custom";objform.target="_blank";}	
		if(obj)obj.name="q";
	}else if(typeid==3){
		if(objform){objform.action="http://www.baidu.com/baidu";objform.target="_blank";}
		if(obj)obj.name="word";
	}else{
		if(objform){objform.action="search.asp";objform.target="";}
		if(obj)obj.name="keyword";
	}
}

function vPic(id){
	var obj=getId(id),altStr;
	if(lan){altStr="点击放大"}else{altStr="Click to enlarge"}
	if(obj){
		obj.onmouseover=function(){
			var objImg=obj.getElementsByTagName("img"),srcStr="";
			for(var i=0;i<objImg.length;i++){
				if(objImg[i].src.indexOf("loading")<1){
					srcStr+=objImg[i].src+",";
					objImg[i].className+=" zoomIn";
					objImg[i].alt=altStr;
					eval("var obj"+i+"=objImg["+i+"]");
					eval("obj"+i+".onclick=function(){toBig(obj"+i+",srcStr,"+i+");}");
				}
			}
		}
	}
}
function toBig(obj,srcStr,nowI,addUrl){
	var nowSrc=obj.src,pW,pH;
	if(Null(srcStr))srcStr=nowSrc;
	if(typeof(nowI)=="undefined"){
		if(srcStr.indexOf(",")>0){
			var picArr=srcStr.split(",");
			for(var i=0;i<picArr.length;i++){if(nowSrc.indexOf(picArr[i])>=0)nowI=i;}
		}else nowI=0;
	}
	if(Null(addUrl))addUrl="";
	setCookie("viewPicCs",lanstr+"{$}"+addUrl+"{$}"+nowI+"{$}"+nowSrc+"{$}"+srcStr);
	pW=screen.width;
	pH=screen.height;
	window.showModalDialog(webpath+"zoom/",self,"dialogWidth:"+pW+"px;dialogHeight:"+pH+"px;help:no;resizable:yes;status:no;scroll:no");
}

function returnimgcode(theimg){
	var imghtml="";
	if(theimg[1]!="")imghtml='<a href="'+theimg[1]+'" target="_blank">';
	imghtml+='<img src="'+theimg[0]+'" />';
	if(theimg[1]!="")imghtml+='</a>';
	return imghtml;
}

function modifyimage(loadarea,imgindex){
	var filterstring="progid:DXImageTransform.Microsoft.GradientWipe(GradientSize=1.0 duration=0.7)";
	var imgobj=getId(loadarea);
	if(imgobj.filters && window.createPopup){
		imgobj.style.filter=filterstring;
		imgobj.filters[0].Apply();
	}
	imgobj.innerHTML=returnimgcode(proimgs[imgindex]);
	if(imgobj.filters && window.createPopup)imgobj.filters[0].Play();
	return false;
}


function page_on(url){
	if(location.href.indexOf(url)>=0)
		return true;
	else
		return false;
}

function loadlanXml(model,prefix)
{
	var isChrome = window.navigator.userAgent.indexOf("Chrome") !== -1;
	if(isChrome){
		var xmlhttp = new window.XMLHttpRequest();
        xmlhttp.open("GET", lanxmlpath+model+"/"+lanstr+".xml", false);
        xmlhttp.send(null);	
		thefunName=prefix;	
        lanXmlDoc = xmlhttp.responseXML;
	}
	else {
   	   lanXmlDoc=newXmlObj();
	   thefunName=prefix;	
	   lanXmlDoc.load(lanxmlpath+model+"/"+lanstr+".xml");
	}
	return lanXmlDoc;
}

function newXmlObj(){
	var dom;
	if(ie){dom=new ActiveXObject("MSXML2.DOMDocument");}else{dom=document.implementation.createDocument("","",null);}
	if(dom){dom.async=false;return dom;}else{return false;}	
}
function t(lanstr){
	if(typeof(thefunName)!="undefined"&&thefunName!="")lanstr=thefunName+lanstr;
	if(typeof(lanXmlDoc)!="object")	{return "[NO:"+lanstr+"/]";}
	try{
		if(lanstr.indexOf("/")>=0){	
			var lanstrArr,uboundArr,funstr=lanXmlDoc;
			lanstrArr=lanstr.split("/");		
			uboundArr=lanstrArr.length-1;		
			for(var i=0;i<=uboundArr;i++){
				if(uboundArr!=i){funstr=funstr.getElementsByTagName(lanstrArr[i]).item(0);}
				else{
					if(ie){funstr=funstr.getElementsByTagName(lanstrArr[uboundArr]).item(0).text;}
					else{funstr=funstr.getElementsByTagName(lanstrArr[uboundArr]).item(0).textContent;}
				}
			}
		}
		else
		{
			if(ie){funstr=lanXmlDoc.getElementsByTagName(lanstr).item(0).text;}
			else{funstr=lanXmlDoc.getElementsByTagName(lanstr).item(0).textContent;}
		}
		return funstr;
	}
	catch(e){return "["+lanstr+"]";}
}

function chang_img(url){
	var obj=getId("big_img");
	imgPlay(obj,5);
	setImg(obj,500,500,webpath+"images/propic/"+url);
}

function imgPlay(obj,cases){
	var transitions=new Array;
	transitions[0]="progid:DXImageTransform.Microsoft.Fade(duration=1)";
	transitions[1]="progid:DXImageTransform.Microsoft.Blinds(duration=1,bands=20)";
	transitions[2]="progid:DXImageTransform.Microsoft.Checkerboard(duration=1,squaresX=20,squaresY=20)";
	transitions[3]="progid:DXImageTransform.Microsoft.Strips(duration=1,motion=rightdown)";
	transitions[4]="progid:DXImageTransform.Microsoft.Barn(duration=1,orientation=vertical)";
	transitions[5]="progid:DXImageTransform.Microsoft.GradientWipe(duration=0.5,GradientSize=1.0)";
	transitions[6]="progid:DXImageTransform.Microsoft.Iris(duration=1,motion=out)";
	transitions[7]="progid:DXImageTransform.Microsoft.Wheel(duration=1,spokes=12)";
	transitions[8]="progid:DXImageTransform.Microsoft.Pixelate(maxSquare=10,duration=1)";
	transitions[9]="progid:DXImageTransform.Microsoft.RadialWipe(duration=1,wipeStyle=clock)";
	transitions[10]="progid:DXImageTransform.Microsoft.RandomBars(duration=1,orientation=vertical)";
	transitions[11]="progid:DXImageTransform.Microsoft.Slide(duration=1,slideStyle=push)";
	transitions[12]="progid:DXImageTransform.Microsoft.RandomDissolve(duration=1,orientation=vertical)";
	transitions[13]="progid:DXImageTransform.Microsoft.Spiral(duration=1,gridSizeX=40,gridSizeY=40)";
	transitions[14]="progid:DXImageTransform.Microsoft.Stretch(duration=1,stretchStyle=push)";
	var i=formatnum(cases,-1);
	if(i<0){i=sjs(transitions.length-1);}else{i=cases;}
	obj.style.filter=transitions[i];obj.filters[0].Apply();obj.filters[0].Play();
}

function isEmail(email){
	var patrn= /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
	if (!patrn.exec(email)) return false;
	return true;
}

function safename(str){
	var patrn=/^[0-9a-zA-Z_@!\.-]{2,30}$/;
	if(!patrn.exec(str))return false;
	return true;
}



function setHome(obj){obj.style.behavior="url(#default#homepage)";obj.setHomePage(""+weburl+"");return false;}
function setSc(){window.external.addFavorite(''+weburl+'',''+company+'');}
function rePlace(str,str1,str2){while(str.indexOf(str1)>=0){str=str.replace(str1,str2);};return str;}
function sjs(i){return Math.floor(Math.random()*(i+1));}
Number.prototype.toFixed = function(d){
	var s=this+"";if(!d)d=0;
	if(s.indexOf(".")==-1)s+=".";s+=new Array(d+1).join("0");
	if (new RegExp("^(-|\\+)?(\\d+(\\.\\d{0,"+ (d+1) +"})?)\\d*$").test(s))
	{
		var s="0"+ RegExp.$2, pm=RegExp.$1, a=RegExp.$3.length, b=true;
		if (a==d+2){a=s.match(/\d/g); if (parseInt(a[a.length-1])>4)
		{
			for(var i=a.length-2; i>=0; i--) {a[i] = parseInt(a[i])+1;
			if(a[i]==10){a[i]=0; b=i!=1;} else break;}
		}
		s=a.join("").replace(new RegExp("(\\d+)(\\d{"+d+"})\\d$"),"$1.$2");
	}
	if(b)s=s.substr(1);return (pm+s).replace(/\.$/, "");} return this+"";
}
function showObj(obj,ww,hh){
	ww=formatnum(ww,0),hh=formatnum(hh,0);
	var w=parseInt(obj.style.width);
	var h=parseInt(obj.style.height);
	var speed,temp,add;
	if(ww>w&&hh>h){speed=5;temp=0;add=true}else{speed=-5;temp=100;add=false;}
	function loops(){
		temp+=speed;
		if(add){speed++;temp=temp>100?100:temp;}else{speed--;temp=temp<1?0:temp;}		
		w=ww*temp/100;
		h=hh*temp/100;
		obj.style.width=w+"px";
		obj.style.height=h+"px";
		if(temp==100||temp==0){clearInterval(loop);return;}
	}
	var loop=setInterval(loops,10);
}

function changeDiv(n){
	var objArr=J(".changeDiv");
	var objArrs=J(".changeDivs");
	if(Null(n)&&n!=0){
		//var nowN=formatnum(getCookie("changeDivId"),0);
		//for(i=0;i<objArr.length;i++){objArr[i].className=objArr[i].className.replace(" on","");}
		//objArr[nowN].className+=" on";	
		for(i=0;i<objArrs.length;i++){objArrs[i].style.display="none";}
		//objArrs[nowN].style.display="";	
	}else{
		for(i=0;i<objArr.length;i++){objArr[i].className=objArr[i].className.replace(" on","");}
		objArr[n].className+=" on";	
		for(i=0;i<objArrs.length;i++){objArrs[i].style.display="none";}
		objArrs[n].style.display="";	
		setCookie("changeDivId",n);
	}
}

function selectto(obj){location.href=obj.value;}

function urlencode(str)
{
	str=encodeURI(str);
	str=rePlace(str,"&","%26");
	return str;
}

function urldecode(str)
{
	str=rePlace(str,"%26","&");
	str=decodeURI(str);
	return str;
}

String.prototype.trim = function(){return trim(this);}
String.prototype.ltrim = function(){return lTrim(this);}
String.prototype.rtrim = function(){return rtrim(this);}
function ltrim(str,del){
    for(var i=0;i<str.length;i++){if(str.charAt(i)!=" "&&str.charAt(i)!="　"&&str.charAt(i)!=del)break;}
    str=str.substring(i,str.length);
    return str;
}
function rtrim(str,del){
    for(var i=str.length-1;i>=0;i--){if(str.charAt(i)!=" "&&str.charAt(i)!="　"&&str.charAt(i)!=del)break;}
    str=str.substring(0,i+1);
    return str;
}
function trim(str,del){
    if(Null(del))del="";
	return ltrim(rtrim(str,del),del);
}


function formatname(name)
{
	name=rePlace(name,"%","");
	name=rePlace(name," ","");
	name=urlencode(name);
	name=rePlace(name,"%","-");
	return name;
}

function unformatname(name)
{
	name=rePlace(name,"-","%");
	name=decodeURI(name);
	return name;
}

function lang(str){return eval("text_"+str);}

function help(e,obj,id,type,width,height)
{
	loadlanXml("help","help_");
	var str=t(id);
	var formatids=",22,23,24,";
	if(formatids.indexOf(","+id+",")>=0)str=formathtml(str);
	switch(type)
	{
		case "alert":
			obj.title="点击查看帮助";
			obj.onclick=function(){alert(str);}
			break;
		case "DvWnd":
			obj.title="点击查看";
			obj.onclick=function(){DvWnd.open(""+text_help+"",str,width,height,0);}
			break;
		default:
			selectIo(0);
			var pW=clientWidth();
			var pH=clientHeight();
			e=window.event||e;
			var div=document.createElement("div"),x,y,sTop=scrollTop();
			div.innerHTML=str;
			document.body.appendChild(div);
			div.style.position="absolute";
			div.className="helpdiv";
			width=formatnum(width,450);
			width=width>0?width:450;
			if(div.clientWidth>width){div.style.width=width+"px";}
			var oW=div.clientWidth;
			var oH=div.clientHeight;
			function reset(e){
				e=window.event||e;
				x=e.clientX;
				y=e.clientY;
				div.style.left=x+15+"px";
				div.style.top=y+sTop+25+"px";
				if(y+oH>=pH-25){div.style.top=pH-oH+sTop-2+"px";}
				if(x+oW>=pW-15){div.style.left=x-oW-10+"px";}
			}
			reset(e);
			obj.onmousemove=function(e){reset(e);}
			obj.onmouseout=function(){document.body.removeChild(div);selectIo(1);}			
	}
}

function selectIo(io,p)
{
	if(myBrowser.ie&&myBrowser.version<7){
		var s,dis,i;
		s=getTag("select");
		dis=io?"":0;
		for(i=0;i<s.length;i++){s[i].style.width=dis;}
	}
}

function formatLen(o,len){
	var obj=J(o);
	if(!obj)return;
	var temp="";
	for(var i=0;i<obj.length;i++){
		temp=formatLenOne(obj[i],len);
		if(obj[i].innerHTML.length>temp.length)temp+="...";
		obj[i].innerHTML=temp;
	}	
}
function formatLenOne(obj,len)
{
	if(!obj)return;
	var div=getId("formatLenObj");
	/*
	if(!div)return;
	var l=obj.innerHTML.length,temp="",ll;
	for(var i=0;i<l;i++){
		temp+=obj.innerHTML.substring(i,i+1);
		div.innerHTML=temp;
		ll=div.scrollWidth;
		if(ll>len)return temp;
	}*/
	return obj.innerHTML;
}

//产品询价
function xiazai(url_d){

		art.dialog.open("/web/pass.php?url_d="+url_d, {title: '请输入下载密码',width:'570px',height:'250px',lock:true});
}

function sleep(s,fun)
{
	function loop(){s--;if(s<=0){clearInterval(loops);eval(fun+"()");}}
	var loops=setInterval(loop,1000);
}

function refCode(obj)
{
	if(typeof(obj)!="object"){obj=getId(obj);}
	obj.src=obj.src+"?i="+Math.random();	
}

function formathtml(str)
{
	str = str.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
	return str;
}

function admin_opimize(tablename)
{
	var id="_edit";
	var url=adminpath+"admin_data.php?action=opimize&tablename="+tablename;
	var xmlhttp=ajax_driv();
	if(url.indexOf("?")>=0)	{url+="&alzCmsDefault="+Math.random();}else{url+="?alzCmsDefault="+Math.random();}
	xmlhttp.open("get",url,true);
	xmlhttp.onreadystatechange=function()
	{
		if(xmlhttp.readyState==4)
		{		
			var response=xmlhttp.responseText;

			try{eval("ajax_post"+id+"(response)");}catch(e){sucgoto(response,0);}
		}
	};		
	xmlhttp.send(null);	

}
function ajax_post_edit(sec)
{
	ajax_posted_def(sec,"admin_data.php","admin_opimize");
}

function admin_repair(tablename)
{
	var id="_edit";
	var url=adminpath+"admin_data.php?action=repair&tablename="+tablename;
	var xmlhttp=ajax_driv();
	if(url.indexOf("?")>=0)	{url+="&alzCmsDefault="+Math.random();}else{url+="?alzCmsDefault="+Math.random();}

	xmlhttp.open("get",url,true);
	xmlhttp.onreadystatechange=function()
	{
		if(xmlhttp.readyState==4)
		{		
			var response=xmlhttp.responseText;

			try{eval("ajax_post"+id+"(response)");}catch(e){sucgoto(response,0);}
		}
	};		
	xmlhttp.send(null);		
}




function setHome(obj){obj.style.behavior="url(#default#homepage)";obj.setHomePage(""+weburl+"");return false;}
function setSc(){window.external.addFavorite(''+weburl+'',''+company+'');}
function rePlace(str,str1,str2){while(str.indexOf(str1)>=0){str=str.replace(str1,str2);};return str;}
function sjs(i){return Math.floor(Math.random()*(i+1));}
Number.prototype.toFixed = function(d){
	var s=this+"";if(!d)d=0;
	if(s.indexOf(".")==-1)s+=".";s+=new Array(d+1).join("0");
	if (new RegExp("^(-|\\+)?(\\d+(\\.\\d{0,"+ (d+1) +"})?)\\d*$").test(s))
	{
		var s="0"+ RegExp.$2, pm=RegExp.$1, a=RegExp.$3.length, b=true;
		if (a==d+2){a=s.match(/\d/g); if (parseInt(a[a.length-1])>4)
		{
			for(var i=a.length-2; i>=0; i--) {a[i] = parseInt(a[i])+1;
			if(a[i]==10){a[i]=0; b=i!=1;} else break;}
		}
		s=a.join("").replace(new RegExp("(\\d+)(\\d{"+d+"})\\d$"),"$1.$2");
	}
	if(b)s=s.substr(1);return (pm+s).replace(/\.$/, "");} return this+"";
}

function setMenuOn(menuid){var obj=getId("menu_"+menuid);if(obj){obj.className+=" on";}}
function setLeftOn(menuid){var obj=getId("cate_"+menuid);if(obj){obj.className+=" on";}}
function setMainMenuOn(menuid){var obj=getId("mainmenu_"+menuid);if(obj){obj.className+=" mainon";}}