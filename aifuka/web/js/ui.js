webio();

function webio(){ajax_post(webpath+"include/webio.php?lanstr="+lanstr,"","_webio");}
function ajax_post_webio(sec){if(sec!="")location.href=sec;}

function countip(){
	if(!count_io)return;;
	var curl=urlencode(document.referrer);
	var screeninfo=screen.width+"x"+screen.height+"x"+screen.colorDepth||screen.pixelDepth;
	var data = new Date();
	var time=parseInt(data.getTime()/1000);
	ajaX(webpath+"count/?time="+time+"&screeninfo="+screeninfo+"&curl="+curl+"&url="+urlencode(location.href)+"&lanstr="+lanstr,"countip","&nbsp;");
}

function web_modelgoto(){if(web_model=="php"&&!page_on("/web/"))location.href=webpath;}

function getClick(dbname,lanid,lanstr,id){ajaX(webpath+"include/getValue.php?action=click&dbname="+dbname+"&lanid="+lanid+"&lanstr="+lanstr,getId(id));}



function feedbackContent(classid,lanstr)
{
	var page=getPra("page");

	ajaX(webpath+"include/feedback.php?action=getContent&classid="+classid+"&lanstr="+lanstr+"&page="+page,"feedbackcontent");
}

function faq_co(obj)
{
	var t=J(".faq_title");
	var c=J(".faq_content");
	for(var i=0;i<c.length;i++){c[i].style.display="none";t[i].style.fontWeight="";}
	var tr=obj.getElementsByTagName("tr");
	tr[0].style.fontWeight="bold";	
	tr[1].style.display="";
}

function searchform(form,text)
{
	var k=form.keyword;
	if(k.value==text||k.value==""){
		alert(text);
		k.value="";
		k.focus();
		return false;
	}
}

function showMylist(id)
{
	var obj=getId("lists_"+id),olddis;
	if(obj){
		olddis=obj.style.display;
		dis=olddis=="none"?"":"none";
		obj.style.display=dis;
	}
}

function setMyList(classid,classidstr)
{
	var obj=J("#list_"+classid+" a")[0],i,a;
	if(obj)obj.className+=" on";
	
	var arr=classidstr.split(",");
	for(i=0;i<arr.length;i++)
	{
		obj=getId("lists_"+arr[i]);
		if(obj)obj.style.display="";
		a=J("#list_"+arr[i]+" a")[0];
		obj=getId("list_"+arr[i]);
		if(a&&obj.className=="mylist_1")a.className+=" on";
	}
}

function formatStr(c){
	var obj,i,objtd;
	obj=J("."+c+" table"),i;
	for(i=0;i<obj.length;i++){
		if(obj[i].border!="0"&&obj[i].border!=""){obj[i].className+=" myborder";}
	}
}

function T(id,n){getId(id).style.fontSize=n+"px";}

function tagClick(tag){ajaX(webpath+"include/setValue.php?action=tagClick&tag="+tag);}

function myTx1(){
	var n=formatnum(getPra("i"),0);
	var titleobj=J(".tx1_title")[0],i,str="",str2="";
	if(!titleobj)return;
	var titles=titleobj.innerHTML.split("|");
	for(i=0;i<titles.length;i++){
		str2=titles[i].replace(/<[^>]*>/gi,"");
		str+="<a onmouseover='myTx1_show("+i+");'><b></b><u>"+str2+"</u><p></p></a>";
	}
	titleobj.innerHTML=str;
	myTx1_show(n);
}
function myTx1_show(n)
{
	var titleobj=J(".tx1_title td a"),contentobj=J(".tx1_content"),i,dis;
	if(!contentobj||!contentobj)return;
	for(i=0;i<titleobj.length;i++){
		dis=i==n?"on":"";
		if(titleobj[i])titleobj[i].className=dis;
	}
	for(i=0;i<contentobj.length;i++){
		dis=i==n?"":"none";
		if(contentobj[i])contentobj[i].style.display=dis;
	}
}

function hotkeyword(obj)
{
	var o=J(".clear")[0];
	o.value=obj.innerHTML;
}



function  addFav() {   //加入收藏夹   
             if  (document.all) {   
                window.external.addFavorite('http://www.hailianchina.com', company);   
            }   
             else   if  (window.sidebar) {   
            window.sidebar.addPanel(company, 'http://www.hailianchina.com',  "" );   
            }   
        }   
function SetHome(obj){   
    try{   
        obj.style.behavior='url(#default#homepage)';   
        obj.setHomePage('http://www.hailianchina.com');   
    }catch(e){   
        if(window.netscape){   
            try{   
                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");   
            }catch(e){   
                alert("抱歉，此操作被浏览器拒绝！\n\n请在浏览器地址栏输入“about:config”并回车然后将[signed.applets.codebase_principal_support]设置为'true'");   
            };   
        }else{   
            alert("抱歉，您所使用的浏览器无法完成此操作。\n\n您需要手动将'http://www.jm-ky.com/'设置为首页。");   
        };   
    };   
};   
