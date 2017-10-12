function languageorderby(){
	var orderbys=getCookie("orderby");
	if(orderbys=="desc")
		location.href="?orderby=asc";
	else
		location.href="?orderby=desc";
}

function languageck(form)
{
	var keyword=form.keyword.value;
	if(keyword.indexOf("关键字")>0||keyword==""){
		var valuestr=getFormValue(form);
		if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
		if(!posted){ajax_post("?update=1",valuestr);posted=true;}
		return false;
	}
}

function ajax_posted(sec)
{
	if(sec.indexOf("{ok}")>=0){
		sucgoto("<b>"+sec.split("{ok}")[1]+"</b>",0);
	}else{
		sucgoto("<u>Function \"languageck\" Error!</u>","",errgoto_times);
	}
}