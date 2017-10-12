var floatObj,pageTopAd,theTop=100;
myAdFloat();
function myAdFloat()
{
	floatObj=J(".floatAds");
	moveAdDiv();
}
function moveAdDiv()
{
	var tt=50;
	pageTopAd=scrollTop();
	pageTopAd=pageTopAd-floatObj[0].offsetTop+theTop;
	pageTopAd=floatObj[0].offsetTop+pageTopAd/10;
	if (pageTopAd < theTop) pageTopAd = theTop;
	floatObj[0].style.top = pageTopAd+"px";
	tt=10;
	setTimeout(moveAdDiv,tt);
}