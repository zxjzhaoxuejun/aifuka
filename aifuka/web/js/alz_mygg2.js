function initMarquee()
{
	var str=marqueeContent[0];
	document.write('<div id="marqueeBox" style="overflow:hidden;height:'+marqueeHeight+'px" onmouseover="clearInterval(marqueeInterval[0]);" onmouseout="marqueeInterval[0]=setInterval(\'startMarquee()\',marqueeDelay)"><div>'+str+'</div></div>');
	marqueeId++;
	marqueeInterval[0]=setInterval("startMarquee()",marqueeDelay);
}
function startMarquee()
{
	var Box=getId("marqueeBox");
	var str=marqueeContent[marqueeId];
	marqueeId++;
	if(marqueeId>=marqueeContent.length)marqueeId=0;
	if(Box.childNodes.length==1) {
		var nextLine=document.createElement('DIV');
		nextLine.innerHTML=str;
		Box.appendChild(nextLine);
	}
	else
	{
		Box.childNodes[0].innerHTML=str;
		Box.appendChild(Box.childNodes[0]);
		Box.scrollTop=0;
	}
	clearInterval(marqueeInterval[1]);
	marqueeInterval[1]=setInterval("scrollMarquee()",10);
}
function scrollMarquee()
{
	var Box=getId("marqueeBox");
	Box.scrollTop++;
	if(Box.scrollTop%marqueeHeight==marqueeHeight)
	{
		clearInterval(marqueeInterval[1]);
	}
}

function mygg3()
{
	var theScrollTop=getId("mygg3").scrollTop;
	for(i=1;i<=nums;i++)
	{
		eval("var obj"+i+"=getId('mygg3_"+i+"');");
		eval("var top"+i+"=obj"+i+".style.top.replace('px','');");
	}
	
	function Marquee1()
	{
		if(thei<h)
		{
			theScrollTop=theScrollTop+step;
			thei=thei+step;
			getId("mygg3").scrollTop=theScrollTop;
		}
	}
	setInterval(Marquee1,speed1);
		
	function Marquee2()
	{
		thei=0;
		if(theScrollTop>=(nums-1)*h)theScrollTop=0;
	}
	setInterval(Marquee2,speed2);
}