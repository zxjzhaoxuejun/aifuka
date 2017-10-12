// JavaScript Document
/*加入收藏夹*/ 
function addfavorite(title){ 
	var url = "http://"+window.location.host;
    try{ 
		window.external.addfavorite(url,title); 
     }catch (e){ 
       try{ 
           window.sidebar.addPanel(title, url, ""); 
       }catch (e){ 
            alert("加入收藏失败，请使用ctrl+d进行添加"); 
       } 
    } 
} 

function checkMsgForm(){
	
	if(document.msgform.msg_title.value==""){
		alert("Please enter a message title!");
		return false;
	}else if(document.msgform.msg_contact.value==""){
		alert("Please enter a contact!");
		return false;
	}else if(document.msgform.msg_tel.value==""){
		alert("Please enter a contact phone!");
		return false;
	}else{

		return true; 
	}
}


function checkSearchForm(){
	if(document.searchform.keywords.value==""){
		alert("Please enter a keyword to search!");
		return false;
	}else{
		return true;
	}
}
//切换图片
function changePic(obj){
	$("#bigpic > img").attr("src",obj.src);
}

/* 去掉子类别第一顶部线条*/
$(document).ready(function(){
	$(".class_list > ul > li > ul > li:first").css("border-top","0");
});


	$(function(){
		//友情链接
		$('#link_scroll').kxbdSuperMarquee({
			isMarquee:true,
			isEqual:true,
			scrollDelay:30,
			duration:30,
			
			direction:'up'
		});
		
		//荣誉客户
		$('#icon_scroll').kxbdSuperMarquee({
			isMarquee:true,
			isEqual:true,
			scrollDelay:30,
			duration:30,
			
			direction:'up'
		});
		
	});


