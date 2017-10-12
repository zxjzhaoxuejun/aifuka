/* 系统参数 配置 操作 JS 文件 */
function configset(form)
{
	var valuestr=getFormValue(form);

	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	ajax_post("?save=1",valuestr);
	return false;
}

function updateExpire()
{
	ajax_post("?expire=1",'test=test');
	return false;
}

function ajax_posted(sec)
{
	if(sec.indexOf("{ok}")<0)
		sucgoto("<u>"+sec+"</u>","",errgoto_times);
	else
		sucgoto("<b>恭喜，参数更新成功！</b>",0);
}

function configswitch(obj,id)
{
	var objs=J("#settitle input");
	for(var i=0;i<objs.length;i++){
		objs[i].className=objs[i].className.replace("mybuttonOn","mybutton");
	}
	obj.className="mybuttonOn";
	objs=J(".set");
	if(typeof(id)=="undefined"){
		for(var i=0;i<objs.length;i++){objs[i].style.display="";}
	}else{
		for(var i=0;i<objs.length;i++){objs[i].style.display="none";}
		objs[id].style.display="";
	}
}

function connset(form)
{
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	ajax_post("?save=1",valuestr,"conn");
	return false;
}

function ajax_postconn(sec){ajax_posted_def(sec,1,"connset");}