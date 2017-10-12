function admin_class(form){
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	if(!posted){ajax_post("?action=update",valuestr,"_class");posted=true;}
	return false;
}
function ajax_post_class(sec){ajax_posted_def(sec,1,"admin_class");}

function classlistto(lanid,topclassid,lid)
{

	if(lanid==0)return;
	var bigclass=topclassid==lanid;
	var getlayer=ajax("?action=getlayer&lanid="+topclassid);//得到某个类别的最大级数
	if(getlayer<1)return;//无子类为0，不再跳转
	if(lid>=getlayer)return;//栏目大于等于最大层级，不再跳转
	if(bigclass){	
		location.href="?fid="+lanid;
	}else{

		var classids=ajax("?action=getallclassids&lanid="+lanid);
		var classlist=J(".classlist");
		for(var i=0;i<classlist.length;i++){classlist[i].style.display="none";}
		classlist=classids.split(",");
		for(var i=0;i<classlist.length;i++){
			obj=getId("class_"+classlist[i]);
			if(obj)obj.style.display="";
		}
		setCookie("classlistid",lanid);
		var classidstr=ajax("?action=getclassidstr&lanid="+lanid);
		classguidlist(classidstr);		
	}
}

function classguidlist(id,a)
{
	if(id==0)return;
	if(Null(a))a="";
	var obj=getId("guidlist");
	if(obj)ajaX("?action=guid&a="+a+"&classidstr="+id,"guidlist");
}

function normaluser(io)
{
	if(!io)return;
	var obj=J(".admin_table tr");
	for(var i=0;i<obj.length;i++)
	{
		if(obj[i].className.indexOf("normaluser")<0){obj[i].style.display="none";}
	}
}