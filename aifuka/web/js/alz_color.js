var defColor2="";
function colorSelect(id,defColor)
{
	document.write('<style type="text/css">');
	document.write('#colorSelectObj_'+id+'{ position:relative;display:inline-block;cursor:pointer;width:15px;height:15px; border:1px solid #ccc; float:left;margin:1px 0 0 3px}');
	document.write('#colorSelect_'+id+'{ width:120px; border:1px solid #ccc;position:absolute; padding:3px;top:20px;left:0px;background:#ffffff;}');
	document.write('.colorSelect div{ background:#efefef; color:#000000; height:20px; line-height:20px;}');
	document.write('.colorSelect div #nowcolor_'+id+'{ width:13px; height:13px; display:inline-block; margin-left:3px; border:1px solid #cccccc;}');
	document.write('.colorSelect div #nowcolor2_'+id+'{ padding:3px; line-height:22px;}');
	document.write('.colorSelect p{ width:12px; height:12px; text-align:center;line-height:12px; background:#efefef; float:left; margin:3px; cursor:pointer; border:1px solid #ccc;}');
	document.write('.colorSelect p.color_on{ border:1px solid red;}');
	document.write('</style>');
	document.write('<span id="colorSelectObj_'+id+'" onclick="getColor(this,\''+id+'\',\''+defColor+'\');" style="background:'+defColor+';"><div id="colorSelect_'+id+'" style="display:none"></div></span>');	
}

function getColor(obj,id,defColor)
{
	var colorDiv=getId("colorSelect_"+id);
	if(colorDiv)
	{
		colorDiv.style.display="";
		var colorlist=new Array();
		var colorBody="";
		colorlist[0]="#000000";colorlist[1]="#993300";colorlist[2]="#333300";colorlist[3]="#003300";
		colorlist[4]="#003366";colorlist[5]="#000080";colorlist[6]="#333399";colorlist[7]="#333333";
		colorlist[8]="#800000";colorlist[9]="#FF6600";colorlist[10]="#808000";colorlist[11]="#008000";
		colorlist[12]="#008080";colorlist[13]="#0000FF";colorlist[14]="#666699";colorlist[15]="#808080";
		colorlist[16]="#FF0000";colorlist[17]="#FF9900";colorlist[18]="#99CC00";colorlist[19]="#339966";
		colorlist[20]="#33CCCC";colorlist[21]="#3366FF";colorlist[22]="#800080";colorlist[23]="#999999";
		colorlist[24]="#FF00FF";colorlist[25]="#FFCC00";colorlist[26]="#FFFF00";colorlist[27]="#00FF00";
		colorlist[28]="#00FFFF";colorlist[29]="#00CCFF";colorlist[30]="#993366";colorlist[31]="#CCCCCC";
		colorlist[32]="#FF99CC";colorlist[33]="#FFCC99";colorlist[34]="#FFFF99";colorlist[35]="#CCFFCC";
		colorlist[36]="#CCFFFF";colorlist[37]="#99CCFF";colorlist[38]="#CC99FF";colorlist[39]="#FFFFFF";
		colorBody="<div class='colorSelect'>";
		colorBody+="<div><span id='nowcolor_"+id+"'></span><span id='nowcolor2_"+id+"'></span></div>";
		for(var i=0;i<colorlist.length;i++)
		{
			colorBody+="<p style='background:"+colorlist[i]+";' onmouseover='colorChang(this,\""+colorlist[i]+"\",\""+id+"\");'></p>";
		}
		colorBody+="</div><div class='cboth'></div>";
		colorDiv.innerHTML=colorBody;
		var nowcolor=getId("nowcolor_"+id);
		var nowcolor2=getId("nowcolor2_"+id);
		if(defColor2=="")
		{
			nowcolor.style.backgroundColor=""+defColor+"";
			nowcolor2.innerHTML=defColor;
		}else{
			nowcolor.style.backgroundColor=""+defColor2+"";
			nowcolor2.innerHTML=defColor2;	
		}
	}
}

function colorChang(obj,thecolor,id)
{
	var colorDiv=getId("colorSelect_"+id);
	var secobj=getId(id);
	var colorSelectObj=getId("colorSelectObj_"+id);
	if(colorDiv)
	{
		var nowcolor=getId("nowcolor_"+id);
		var nowcolor2=getId("nowcolor2_"+id);
		nowcolor.style.backgroundColor=""+thecolor+"";
		nowcolor2.innerHTML=thecolor;
		obj.className="color_on";
		obj.onmouseout=function()
		{
			obj.className="";
		}
		colorSelectObj.onmouseup=function()
		{
			if(secobj)secobj.value=thecolor;
			colorSelectObj.style.backgroundColor=""+thecolor+"";
			colorDiv.innerHTML="";
			colorDiv.style.display="none";
			defColor2=thecolor;
		}
	}
}