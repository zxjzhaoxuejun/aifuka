function count_refs(id){
	pageloading("<u>数据更新中，请稍候……</u>");
	ajax_post(adminpath+"count/?action="+id,"","_refsed");
}
function ajax_post_refsed(sec){
	pageloading("<b>恭喜，数据更新成功！</b>");
	location.href=location.href;
}

function count_ref(){
	if(!confirm("确定更新统计数据？")){return;}
	pageloading("<u>网站统计数据更新中，请稍候……</u>");
	ajax_post(adminpath+"count/?action=ref","","_countref");
}
function ajax_post_countref(sec){
	pageloading("<b>恭喜，统计数据更新成功！</b>");
	sleep(1,"pageloaded");
}

function count_clear(){
	if(!confirm("确定清空统计数据？")){return;}
	if(!confirm("此操作将清空所有统计数据且不可恢复，再次确定清空统计数据？")){return;}
	pageloading("<u>网站统计数据清空中，请稍候……</u>");
	ajax_post(adminpath+"count/?action=clear","","_countclear");
}
function ajax_post_countclear(sec){
	pageloading("<b>恭喜，统计数据清空完毕！</b>");
	sleep(1,"pageloaded");
}

function countview(id){Msg.open("统计详情查看",adminpath+"count/?action=countview&id="+id);}

function setsearch(form){
	var valuestr=getFormValue(form);
	if(valuestr.indexOf("{false}")>=0||valuestr=="")return false;
	if(!posted){ajax_post("?action=setsearchsave",valuestr,"_setsearch");posted=true;}
	return false;
}
function ajax_post_setsearch(sec){ajax_posted_def(sec,0,"setsearch");}