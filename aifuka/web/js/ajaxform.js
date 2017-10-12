function setTagIo(id,value){
	//alert(id);
	if(value=="0"){
		var obj=getId(id);		
		if(obj)  obj.style.display="none";
		
	}

	
}

function setSelect(id,value){
	var obj=getId(id);
	if(obj){for(var i=0;i<obj.options.length;i++){if(obj.options[i].value==value)obj.options[i].selected=true;}}
}

function delSelect(id,value){
	var obj=getId(id);
	if(obj){for(var i=0;i<obj.options.length;i++){if(obj.options[i].value==value)obj.options.remove(i);}}
}

function setRadio(name,value){
	var obj=getName(name);
	if(obj){for(var i=0;i<obj.length;i++){if(obj[i].type=="radio"){if(obj[i].value==value){obj[i].checked=true;return;}}}}
}

function setCheckbox(name,value,str){
	if(Null(str))str=",";
	value=str+value+str;
	var obj=getName(name);
	if(obj){for(var i=0;i<obj.length;i++){if(obj[i].type=="checkbox"){if(value.indexOf(str+obj[i].value+str)>=0){obj[i].checked=true;}}}}
}

function ajaX(url,secid,str)
{

	var xmlhttp=ajax_driv(),obj;
	if(url.indexOf("?")>=0)	{url+="&alzCmsDefault="+Math.random();}else{url+="?alzCmsDefault="+Math.random();}

	xmlhttp.open("get",url,true);
	if(typeof(secid)=="object"){obj=secid;}else{obj=getId(secid);}
	if(Null(str))str="<img src='"+adminpath+"skins/loading3.gif' />";

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

function ajax_post(url,urlcs,id){
	if(Null(id))id="ed";
	var xmlhttp=ajax_driv();
	if(url.indexOf("alzCmsDefault")<0){
		if(url.indexOf("?")<0){url+="?alzCmsDefault="+Math.random();}else{url+="&alzCmsDefault="+Math.random();}
	}

	xmlhttp.open("post",url,true);
	xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlhttp.onreadystatechange=function(){
		if(xmlhttp.readyState<4){ 

			try{eval("ajax_posting"+id+"()");
			}catch(e){lock_button();}
		}
		if(xmlhttp.readyState==4){
			var response=xmlhttp.responseText;
			try{eval("ajax_post"+id+"(response)");}catch(e){sucgoto(response,0);}
		}
	}
	urlcs=rtrim(urlcs,"&");	
	xmlhttp.send(urlcs);
}


function ajaxcheck_username(url,secid,str)
{

	var xmlhttp=ajax_driv(),obj;
	if(url.indexOf("?")>=0)	{url+="&alzCmsDefault="+Math.random();}else{url+="?alzCmsDefault="+Math.random();}

	xmlhttp.open("get",url,true);
	if(typeof(secid)=="object"){obj=secid;}else{obj=getId(secid);}
	if(Null(str))str="<img src='"+adminpath+"skins/loading3.gif' />";

	xmlhttp.onreadystatechange=function()
	{
		if(xmlhttp.readyState<4&&obj){	obj.innerHTML=str;}		
		if(xmlhttp.readyState==4)
		{		
			var response=xmlhttp.responseText;
			if(response.indexOf("{err}")>=0)
			{
				if(obj)obj.innerHTML="<b style='color:red'>"+text_memberd+"</b>";	
			}else{
				if(obj)obj.innerHTML="<img src='"+sitepath+"images/alz_check_right.gif'>"	
				
			}
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

function ajax(url,secid){
	if(url.indexOf("?")>=0){url=url+"&alzCmsDefault="+Math.random();}else{url=url+"?alzCmsDefault="+Math.random();}
	var J=jQuery.noConflict();
	var responseStr=J.ajax({url:url,async:false}).responseText;
	if(getId(secid)){getId(secid).innerHTML=responseStr;}
	//alert(responseStr);
	return responseStr;
}

function ajax2(url,urlcs){
	if(Null(urlcs)){urlcs="alz=1"}
	var img=new Image();
	img.src=url+"?"+urlcs+"&alzCmsDefault="+Math.random();
}

function getRadio(name){
	var obj=getName(name);
	var re="";
	if(obj){for(i=0;i<obj.length;i++){if(obj[i].checked)re=obj[i].value;}}
	return re;
}

function getCheckbox(thename,andstr){
	if(Null(andstr))andstr=",";
	var checkboxArr=getTag("input");
	var funstr="",funstr2="";
	for(var i=0;i<checkboxArr.length;i++)
	{
		if(checkboxArr[i].type=="checkbox")
		{
			if(checkboxArr[i].name==thename)
			{
				if(funstr!="")funstr2=andstr;
				if(checkboxArr[i].checked){funstr+=funstr2+checkboxArr[i].value};	
			}
		}
	}
	return funstr;
}

function lock_button(str){
	if(typeof(lan_savemes)=="undefined")lan_savemes="";
	if(Null(str))str="<img src='"+adminpath+"skins/loading1.gif' align='absmiddle' /> 数据处理中，请稍侯……";
	var obj1=getId("submit1");
	var obj2=getId("submit2");
	var obj3=getId("submiting");
	if(obj1){obj1.style.display="none";}
	if(obj2){obj2.style.display="none";}
	if(obj3){obj3.innerHTML=str;}
}

function unlock_button(){
	var obj1=getId("submit1");
	var obj2=getId("submit2");
	var obj3=getId("submiting");
	if(obj1){obj1.style.display="";}
	if(obj2){obj2.style.display="";}
	if(obj3){obj3.innerHTML="";}
}

