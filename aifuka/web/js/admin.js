var Msg={
	close:function(){
		parent.HHH.close();
	},
	open:function(){
		var a=arguments;

		var p=formatnum(a[4],1);
		if(p){
			parent.HHH.url(a[1],a[2]||900,a[3]||490,a[0]);
		}else{
			parent.HHH.text(a[1],a[2]||900,a[3]||490,a[0]);
		}
	}
}

var DvWnd={
	close:function(){
		parent.HHH.close();
	},
	open:function(){
		var a=arguments;
		var p=formatnum(a[4],1);
		if(p){
			HHH.url(a[1],a[2]||900,a[3]||490,a[0],"white");
		}else{
			HHH.alert(a[1],a[2]||500,a[3]||300,a[0],"white");
		}
	}
}



var tw=0; 
function changecolor(){
        
	$(".twinkle").css("color",tw==0? "green": (tw==1 ? 'blue' : "red"));
	tw==3?tw=0:tw++;
}     

 
var cs_cbgc="#F4FBF4";

var fullheight=0;
var fullheightObj="";



function setfulleight(){
	if(!fullheight||!fullheightObj)return;
	var wh=clientHeight()-60;
	if(wh<fullheight)wh=fullheight;
	fullheightObj.style.height=wh+"px";
}
function pageBottom(){
	var curl=getcookies("rightlocation");
	if(curl!=location.href){
		setcookies("rightcomeurl",curl);
		setcookies("rightlocation",location.href);
	}
	window.onload=function(){
		pageloaded();
		setfulleight();
	}
}




function rsstohtml(){Msg.open("RSS订阅更新",adminpath+"map/?action=rss",400,150);}

function sitemaptohtml(){Msg.open("网站地图Sitemap",adminpath+"map/?action=sitemap",600,350);}

function getdatatypestr(obj)
{
	var str=obj.value;
	var o=getId("datatypestr");
	o.value=str;
}


function adminJsMenu()
{
	menuImg = ["skins/menu1.gif","skins/menu2.gif","skins/menu3.gif"];
	var adminusername=getCookie("admin_username");
	document.write('<script type="text/javascript" src="'+webpath+'js/menudata'+adminusername+'.js"></script>');
}

function searchdo()
{
	var form=getId("myForm");
	var valuestr=getFormValue(form,"search_");
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	valuestr=valuestr.replace("alzCmsDefault","s");
	location.href="?"+valuestr+"1=1";
	return false;
}

function systest(mes)
{
	var obj=getId("screenmes"),err="";
	err=screen.width+"X"+screen.height;
	var w=formatnum(screen.width,0);
	if(w<1024){err+="<span class='red'>您当前的浏览器分辨率过低,可能会得到不可预知的不良效果.</span>";}
	obj.innerHTML=err;
}

function admin_update(form, action){
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	if (typeof(action) == "undefined") { 
   		action = "update";
	}	
	if(!posted){ajax_post("?action=" + action,valuestr,"_update");posted=true;}
	return false;
}

function onlieQQ_config(form){
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	if(!posted){ajax_post("?action=update_config",valuestr,"_update");posted=true;}
	return false;
}

function ajax_post__updateConfig(sec)
{
	ajax_posted_def(sec,1,"");	
}

function ajax_post_update(sec)
{
	ajax_posted_def(sec,1,"admin_update");
}


function toggle(obj,field,lanid)
{
	alert("点亮，或熄灭均需重新进行 一键全新发布网站 操作");

	var val = (obj.innerHTML.match(/1.gif/i)) ? 0 : 1;	
	ajaX("?action=toggle&field="+urlencode(field)+"&val="+val+"&lanid="+lanid,obj);
}



function commenddo(obj,name,key,lanid)
{
	alert("点亮，或熄灭均需重新进行 一键全新发布网站 操作");
	ajaX("?action=commenddo&name="+urlencode(name)+"&key="+key+"&lanid="+lanid,obj);
}

function classguidlist(id,other)
{
	var obj=getId("guidlist");
	if(obj)ajaX("?action=guid&other="+other+"&classid="+id,obj);
}

function onekeytohtml(){
	Msg.open("一键更新网站",adminpath+"tohtml/?action=tohtml-onekeytohtml");
}

function date_formation(tablename)
{
	Msg.open("数据结构",adminpath+"admin_data.php?action=date_formation&tablename="+tablename);		
}

function admindo(obj)
{
	var lanidstr=getCheckbox("lanidstr");

	var fun=obj.value;

	var strs=obj.options[obj.selectedIndex].innerHTML;

	obj.options[0].selected=true;
	if(fun==""){return false;}
	if(lanidstr==""&&!admindo_forname(fun)){
		sucgoto("<u>请选择需要操作的数据！</u>","");
		return false;
	}
	var str="确定"+strs+"?";

	if(str==""||confirm(str)){
		if(fun.indexOf("tohtml-")>=0){
			Msg.open(strs,adminpath+"tohtml/?action="+fun.split("tohtml-")[1]+"&lanid="+lanidstr);
		}else{
			ajax_post("?action=admindo&fun="+fun+"&lanid="+lanidstr+"&str="+urlencode(strs),"","_admindo");
		}
	}
}

function adminDoType(fun)
{
	var lanidstr=getCheckbox("lanidstr");
	if(fun==""){return false;}
	if(lanidstr==""&&!admindo_forname(fun)){
		sucgoto("<u>请选择需要操作的数据！</u>","");
		return false;
	}


	if(fun.indexOf("tohtml-")>=0){
		Msg.open("",adminpath+"tohtml/?action="+fun.split("tohtml-")[1]+"&lanid="+lanidstr);
	}else{
		ajax_post("?action=admindo&fun="+fun+"&lanid="+lanidstr+"&str=","","_admindo");
	}
	
}

function toHtmlFile(fun,lanidstr)
{

	if(fun.indexOf("tohtml-")>=0){
		Msg.open("",adminpath+"tohtml/?action="+fun.split("tohtml-")[1]+"&lanid="+lanidstr);
	}else{
		ajax_post("?action=admindo&fun="+fun+"&lanid="+lanidstr+"&str=","_admindo");
	}	
}

function toHtmlDo(lanidstr)
{
	var fun= "tohtml-class-alls";
	if(fun.indexOf("tohtml-")>=0){
		Msg.open("",adminpath+"tohtml/?action="+fun.split("tohtml-")[1]+"&lanid="+lanidstr);
	}else{
		ajax_post("?action=admindo&fun="+fun+"&lanid="+lanidstr+"&str=","_admindo");
	}	
}

function admindo_forname(str)
{
	if(str=="")return false;
	var names="jiaoz_files,tagnums,delclear,reall,delalllocked,refall";
	var arr=names.split(",");
	for(var i=0;i<arr.length;i++){if(str.indexOf(arr[i])>=0)return true;}
	return false;	
}
function ajax_post_admindo(sec){ajax_posted_def(sec,0,"admindo");}

function sort_do(id)
{
	if(!confirm("确定按此顺序排序？"))return false;
	var form=getId("myForm");
	var valuestr=getFormValue(form,"sort_");
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	if(!posted){ajax_post("?action=sort",valuestr,"_sort");posted=true;}
	return false;
}
function ajax_post_sort(sec){ajax_posted_def(sec,0,"sort_do");}

function dosqlform()
{
	if(!confirm("确定执行此句SQL语句？"))return false;
	var sql=getId("sql").value;
	sql=rePlace(sql,"'","~");
	sql=rePlace(sql,"\"","~");
	ajaX("?action=do&sql="+urlencode(sql),"dosqlsec");
	return false;
}

function switchLang(lang,obj){
	if(typeof(obj)=="undefined")obj=getId(lang);
	var objs=J("#switchLang a");for(var i=0;i<objs.length;i++){objs[i].className="mybutton";}
	if(obj)obj.className="mybuttonOn";
	var dis=lang=="all"?"":"none";
	objs=J(".language");for(var i=0;i<objs.length;i++){objs[i].style.display=dis;}
	if(lang!="all"){objs=J("."+lang);for(var i=0;i<objs.length;i++){objs[i].style.display="";}}
}

function adminip(form){
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	if(!posted){ajax_post("?action=save",valuestr,"adminip");posted=true;}
	return false;
}
function ajax_postadminip(sec){ajax_posted_def(sec,"self","adminip");}



function ajax_posted_def(sec,url,funname)
{

	if(sec.indexOf("{ok}")>=0){
		sucgoto("<b>"+sec.split("{ok}")[1]+"</b>",url);
	}else if(sec.indexOf("{err}")>=0){
		sucgoto("<u>"+sec.split("{err}")[1]+"</u>","",errgoto_times);
		posted=false;
	}else if(!Null(funname)){
		sucgoto("<u>Function \""+funname+"\" Error!</u>","",errgoto_times);
		posted=false;
	}else{
		sucgoto("<u>Function \"ajax_posted_def\" Error!</u>","",errgoto_times);
		posted=false;
	}	
}

function diycfgstr(){
	var diycontent=getId("diycontent");
	var diycontents=trim(diycontent.value);
	var diytag=getId("diytag");
	var diytags=trim(diytag.value);
	if(diycontents==""){alert("请输入权限标签文本内容！");diycontent.value="";diycontent.focus();return false;}
	if(diytags==""){alert("请输入权限标签内容！");diytag.value="";diytag.focus();return false;}
	if(!safename(diytag.value)){alert("请输入正确的标签：只能由英文字母、数字或下划线组合，且长度就为2-30个字符！");diytag.value="";diytag.focus();return false;}
	var diycases=getRadio("diycases");
	var diyprefix=getId("diyprefix").value;
	if(diycases==1||confirm_do()){
		ajax_post("?action=diysave","diyprefix="+diyprefix+"&content="+diycontents+"&tag="+diytags+"&cases="+diycases,"_diycfgstr");
	}
	return false;
}
function ajax_post_diycfgstr(sec){ajax_posted_def(sec,0,"diycfgstr");}

function cfgstrclass(obj)
{
	var str=obj.value+"_";
	getId("diyprefix").value=str;
	getId("diyprefixshow").innerHTML=str;
}

function adminuser(form){
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	var adminuser=$("#admin_username").val();
	var admin_password=$("#admin_password").val();
	var admin_password2=$("#admin_password2").val();
	if(!safenames(adminuser.value)){alert("用户名格式错误：包含非法字符或长度在允许范围之外！");adminuser.value="";adminuser.focus();return false;}
	if(admin_password!=admin_password2){alert("两次密码输入不一样！");$("#admin_password").val("");$("#admin_password").focus();return false;}
	
	if(!posted){ajax_post("?action=save",valuestr,"adminuser");posted=true;}
	return false;
}



function users(form){
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	var user_name=form.username;
	var user_password=form.password;
	if(!safenames(user_name.value)){alert("用户名格式错误：包含非法字符或长度在允许范围之外！");user_name.value="";user_name.focus();return false;}
	if(user_password.value!=form.confirm_password.value){alert("两次密码输入不一样！");form.confirm_password.value="";form.confirm_password.focus();return false;}
	if(!posted){ajax_post("?action=update",valuestr,"_update");posted=true;}
	return false;
}



function ajax_postadminuser(sec){ajax_posted_def(sec,0,"adminuser");}

function adminrole(form){
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	if(!posted){ajax_post("?action=save",valuestr,"role");posted=true;}
	return false;
}
function ajax_postrole(sec){ajax_posted_def(sec,"self","adminrole");}


function adminmenu(n){
	if(typeof(n)=="undefined"){
		showleft(1);
		rightSrc();
		n=formatnum(getCookie("adminmenuon"),0);
	}
	var i,o,objs,menunums;
	o=getId("admin_menu");
	objs=o.getElementsByTagName("li");
	menunums=objs.length;	
	for(i=0;i<menunums;i++){
		objs[i].className=objs[i].className.replace(" on","");
		o=getId("admin_menu"+i);
		if(o)o.style.display="none";
	}
	objs[n].className+=" on";
	o=getId("admin_menu"+n);
	o.style.display="";
	setCookie("adminmenuon",n);
}

function adminmenu2(n)
{
	var obj=J(".adminlist2");
	if(obj){for(var i=0;i<obj.length;i++){if(obj[i])obj[i].style.display="none";}}
	if(Null(n))n=formatnum(getCookie("adminlist_n"),0);
	
	obj=J(".adminlist_"+n);
	if(obj){
		for(var i=0;i<obj.length;i++){
			if(obj[i])obj[i].style.display="";
		}
	}
	setCookie("adminlist_n",n);
	adminmenu3(0)
}

function adminmenu3(n)
{
	var obj=J(".adminlist3");
	if(obj){for(var i=0;i<obj.length;i++){if(obj[i])obj[i].style.display="none";}}
	if(Null(n))n=formatnum(getCookie("adminlist_n"),0);
	obj=J(".adminlist_"+n);
	if(obj){for(var i=0;i<obj.length;i++){if(obj[i])obj[i].style.display="";}}
	setCookie("adminlist_n",n);
	adminmenu4(0)
}

function adminmenu4(m)
{
	var obj=J(".adminlist4");
	if(obj){for(var i=0;i<obj.length;i++){if(obj[i])obj[i].style.display="none";}}
	if(Null(m))m=formatnum(getCookie("adminlist_m"),0);
	obj=J(".adminlist_"+m);
	if(obj){for(var i=0;i<obj.length;i++){if(obj[i])obj[i].style.display="";}}
	setCookie("adminlist_m",m);
}

function buttonbgcc(obj){
	obj.className="mybuttonOn";
	obj.onmouseout=function(){obj.className="mybutton";}	
}

function quit(){
	ajax("admin_login.php?action=quit");
	location.href="index.php";
}

function refleft()
{
	pageloading("<u>菜单更新中，请稍候……</u>");
	ajax_post("admin_left.php?ref=1","","_refleft");
}
function ajax_post_refleft(sec){
	pageloading("<b>菜单更新完毕！</b>");
	getId("left").src="admin_left.php?c=1";
}

function refright()
{
	pageloading("<u>当前页面刷新中，请稍候……</u>");
	ajax_post(webpath+"include/setValue.php?action=refright","","_refright");
}
function ajax_post_refright(sec){
	pageloading("<b>当前页面刷新完毕！</b>");
	getId("right").src=sec;
}

function showleft(n){
	var left=getId("admin_left")||getFid("admin_left");
	var text=getId("showleft")||getFid("showleft");
	if(left&&text){
		loadlanXml("adminindex","index_");
		var olddis=left.style.display,dis,co=true;
		if(typeof(n)!="undefined"){dis=getCookie("adminleft");co=false;}else{dis=olddis==""?"none":"";}
		var txt=dis==""?t(8):t(9);
		left.style.display=dis;
		text.innerHTML=txt;
		if(co)setCookie("adminleft",dis);
	}
}

function rightSrc(obj){
	var url;
	if(obj){
		url=obj.href;
	}else{
		url=getCookie("adminrighturl");
	}
	if(!Null(url))
	{
		if(!obj)getFid("right").src=urldecode(url);
		setCookie("adminrighturl",url);
	}
}

function pageloading(str)
{
	if(Null(str))str="<b>页面加载中，请稍候……</b>";
	var obj=parent.document.getElementById("pageloding");
	if(obj){obj.style.display="";obj.innerHTML=str;}
	sleep(1,"pageloaded");
}
function pageloaded()
{
	var obj=top.document.getElementById("pageloding");
	if(obj){obj.style.display="none";obj.innerHTML="";}
	window.status='';
}

function did(src,url){

	if(Null(url))url=0;
	if(!confirm_do())return;
	var sec=ajax(src);
	if(sec.indexOf("{ok}")>=0){
		sucgoto("<b>"+sec.split("{ok}")[1]+"</b>",url);
	}else if(sec.indexOf("{err}")>=0){
		sucgoto("<u>"+sec.split("{err}")[1]+"</u>","",errgoto_times);
	}else{
		sucgoto("<u>Function \"did\" Error!</u>","",errgoto_times);
	}
	posted=false;
}

function adminstr_co(id)
{
	var obj=J(".cfgstr_c");
	var dis=id==0?"":"none";
	for(var i=0;i<obj.length;i++){obj[i].style.display=dis;}
	obj=getId("adminstr_"+id);
	if(obj)obj.style.display="";	
}

function clearcache(id,path){
	if(Null(path))path=adminpath;
	pageloading("<u>缓存清除中，请稍候……</u>");
//	ajaX(path+"tohtml/?action=alz-toindex");
	ajax_post(path+"admin_clearcache.php?type="+id,"","_clearcache");
}
function ajax_post_clearcache(sec){
	setcookies("adminHeight",clientHeight()-138);
	pageloading("<b>缓存清除完毕！</b>"+sec);
	sleep(1,"pageloaded");
}

function setorderby2(obj,id)
{
	var o=getId(id);
	if(!o)return;
	switch(obj.value)
	{
		case "order by sortid,lanid":
			o.value="order by sortid desc,lanid desc";
			break;
		case "order by edittime,lanid":
			o.value="order by edittime desc,lanid desc";
			break;
		case "order by click,lanid":
			o.value="order by click desc,lanid desc";
			break;
		case "order by sortid desc,lanid desc":
			o.value="order by sortid,lanid";
			break;
		case "order by edittime desc,lanid desc":
			o.value="order by edittime,lanid";
			break;
		case "order by click desc,lanid desc":
			o.value="order by click,lanid";
			break;
	}
}

/**
 * 
 */
function loadingbar(id,n,m){
	var num=getId(id+"_num");
	var ing=getId(id+"_ing");
	
	var value=parseInt(n/m*100)+"%";
	num.innerHTML=value;
	ing.style.width=value;
}

function tag_link_to(ser)
{
	var sec=getId("tag_link_to_str");
	if(sec){
		switch(ser)
		{
			case "Baidu":
				sec.innerHTML="地址：http://www.baidu.com/s?wd={tag}+site:www.xxx.com&ie=utf-8";
				break;
			case "Google":
				sec.innerHTML="地址：http://www.google.cn/search?q={tag}&sitesearch=www.xxx.com&ie=utf-8";
				break;
			case "Yahoo":
				sec.innerHTML="地址：http://search.cn.yahoo.com/search?p={tag}&vs=www.xxx.com&ie=utf-8";
				break;
		}
	}
}

function bgcc_iptc(e,tag)
{
	if(Null(tag))tag="TR";
	bgcc(e,tag);
	inputc(e,"#D9FAD9");
}

function pluginsGet()
{
	ajax_post(plugins_url,"","_plugins");
}
function ajax_post_plugins(sec)
{
	var content=getId("content");
	if(sec.indexOf("{ok}")<0){sec="返回错误：请确认插件接口地址填写正确！";}else{sec=sec.replace("{ok}","");}	
	content.innerHTML=sec;
}

function tochildclass(obj)
{
	var url=obj.value;
	location.href=url;
}

function count_apireset(){
	var api1=getId("count_api1"),api2=getId("count_api2"),api3=getId("count_api3");
	if(api1)api1.value='http://www.ip.cn/getip.php?action=queryip&ip_url=[ip]';
	if(api2)api2.value='gb2312';
	if(api3)api3.value='来自：(.*)';
}
//中文