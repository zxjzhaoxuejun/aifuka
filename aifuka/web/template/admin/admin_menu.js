var data = [
{txt:0,menu:[
	{ico:menuImg[0],group:false,exist:true,txt:'<a target="right" onclick="rightSrc(this);" href="admin_main.php">{$t tag="7" /}</a>'},
	{ico:menuImg[0],group:false,exist:true,txt:'<a target="right" onclick="rightSrc(this);" href="admin_set.php">{$t tag="8" /}</a>'},
	{ico:menuImg[0],group:false,exist:true,txt:'<a target="right" onclick="rightSrc(this);" href="class/?c=1">网站栏目列表</a>'},
	{ico:menuImg[0],group:false,exist:true,txt:'<a target="right" onclick="rightSrc(this);" href="ad/?c=1">网站特效管理</a>'},
	{ico:menuImg[0],group:false,exist:true,txt:'<a target="right" onclick="rightSrc(this);" href="form/?c=1">表单管理系统</a>'},
	{ico:menuImg[0],group:false,exist:true,txt:'<a target="right" onclick="rightSrc(this);" href="tag/?c=1">Tag标签管理</a>'},
	{ico:menuImg[0],group:false,exist:true,txt:'网站更新',menu:[
		{ico:menuImg[0],group:false,exist:true,txt:'<a href="javascript:onekeytohtml();">一键更新</a>'},
		{ico:menuImg[0],group:false,exist:true,txt:'<a href="javascript:rsstohtml();">RSS订阅</a>'}
	]},
	{ico:menuImg[0],group:false,exist:true,txt:'系统插件',menu:[
		{ico:menuImg[0],group:false,exist:true,txt:'<a target="right" onclick="rightSrc(this);" href="plugins/?id=wnl">万年历</a>'},
		{ico:menuImg[0],group:false,exist:true,txt:'<a target="right" onclick="rightSrc(this);" href="plugins/?id=olcode">常用在线通讯代码</a>'},
		{ico:menuImg[0],group:false,exist:true,txt:'<a target="right" onclick="rightSrc(this);" href="plugins/?id=sycx">实用查询</a>'},
		{ico:menuImg[0],group:false,exist:true,txt:'<a target="right" onclick="rightSrc(this);" href="plugins/?id=googlemap">Google地图</a>'},
		{ico:menuImg[0],group:false,exist:true,txt:'<a target="right" onclick="javascript:adminmenu3(\'plugins\');">休闲游戏</a>',menu:[
			{ico:menuImg[0],group:false,exist:true,txt:'<a target="right" onclick="rightSrc(this);" href="plugins/?id=xiangqi">象棋</a>'}
		]}
	]}
]},

{txt:1,menu:[
	{ico:menuImg[2],group:false,exist:true,txt:'<a target="right" onclick="rightSrc(this);" href="article/?c=1">查看所有{$getdbnum model="article" /}</a>'},
	{ico:menuImg[2],group:false,exist:true,txt:'<a target="right" onclick="rightSrc(this);" href="article/?c=1" class="product_locked">查看所有2{$getdbnum model="article" /}</a>'}
]},
{txt:2,menu:[{ico:menuImg[1],group:false,exist:false,txt:"小强王子"}]},
{txt:3,menu:[{ico:menuImg[1],group:false,exist:true,txt:"丛林守护者"}]},
{txt:4,menu:[{ico:menuImg[1],group:false,exist:false,txt:"小强王子"}]},
{txt:5,menu:[{ico:menuImg[1],group:false,exist:false,txt:"小强王子"}]},
{txt:6,menu:[{ico:menuImg[1],group:false,exist:false,txt:"小强王子"}]},
{txt:7,menu:[{ico:menuImg[1],group:false,exist:false,txt:"小强王子"}]}
];