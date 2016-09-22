<?php
header('content-type: text/css');
$id = htmlspecialchars ( $_GET['monid'] , ENT_QUOTES );
?>

.clr {clear:both;visibility : hidden;}



/*---------------------------------------------
---	 	menu container						---
----------------------------------------------*/

/* menu */
div#<?php echo $id; ?> {

}

/* container style */
div#<?php echo $id; ?> ul.maximenuck {
    background :  url(../images/menu_bg.png) top left repeat-x;
    min-height : 55px;
    padding : 0;
    margin : 0;
	clear:both;
	position : relative;
	z-index:999;
	overflow: visible !important;
	display: block !important;
	float: none !important;
	visibility: visible !important;
}

div#<?php echo $id; ?> ul.maximenuck:after {
    content: " ";
    display: block;
    height: 0;
    clear: both;
    visibility: hidden;
    font-size: 0;
}

/*---------------------------------------------
---	 	Root items - level 1				---
----------------------------------------------*/

div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1 {
	display: inline-block !important;
	float: none !important;
	position:static;
    list-style : none;
    border : none;
	vertical-align: middle;
	text-align: left;
	cursor: pointer;
	filter: none;
	margin : 0 2px 0 0;
    padding : 0 20px 0 0;
    height : 55px;
	background :  url(../images/menu_bg.png) top left repeat-x;
}

/** IE 7 only **/
*+html div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1 {
	display: inline !important;
}

div#<?php echo $id; ?>.maximenuckh ul.maximenuck li.maximenuck.level1 {
	background : url(../images/separator.png) right 15px no-repeat;
}

/* vertical menu */
div#<?php echo $id; ?>.maximenuckv ul.maximenuck li.maximenuck.level1 {
	display: block !important;
	margin: 0;
	text-align: left;
}

div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1:hover,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.active {

}

div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1 > a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1 > span.separator {
	display:block;
	float : none !important;
	float : left;
	position:relative;
	text-decoration:none;
	outline : none;
	border : none;
	cursor : pointer;
	magin : 0;
	color : #000;
    font-size : 15px;
    text-transform : uppercase;
    padding : 12px 0 0 0;
	display : block;
    height : 45px;
	white-space: nowrap;
}

/* first level item description */
div#<?php echo $id; ?> ul.maximenuck li.level1 > a span.descck,
div#<?php echo $id; ?> ul.maximenuck li.level1 > span span.descck{
    color : #999;
    margin-top : -4px;
}

/* parent item on mouseover (if subemnus exists) */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.parent:hover,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.parent:hover {

}

/* item color on mouseover */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1:hover > a span.titreck,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.active > a span.titreck,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1:hover > span.separator,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.active > span.separator {
	color: #9d9d9d;
}

/* arrow image for parent item */
div#<?php echo $id; ?>.maximenuckh ul.maximenuck li.level1.parent > a:after,
div#<?php echo $id; ?>.maximenuckh ul.maximenuck li.level1.parent > span.separator:after {
	border-color: #939596 transparent transparent;
	border-style: solid;
	border-width: 7px 6px 0 7px;
	bottom: 6px;
	content: "";
	display: block;
	float: right;
	height: 0;
	left: 50%;
	margin: 0 0 0 -7px;
	position: absolute;
	width: 0;
}

div#<?php echo $id; ?> ul.maximenuck li.level1.parent:hover > a:after,
div#<?php echo $id; ?> ul.maximenuck li.level1.parent:hover > span.separator:after {
}

/* vertical menu */
div#<?php echo $id; ?>.maximenuckv ul.maximenuck li.level1.parent > a:after,
div#<?php echo $id; ?>.maximenuckv ul.maximenuck li.level1.parent > span.separator:after {
	display: inline-block;
	content: "";
	width: 0;
	height: 0;
	border-style: solid;
	border-width: 6px 0 6px 7px;
	border-color: transparent transparent transparent #939596;
	margin: 3px;
	float: right;
}

/* arrow image for submenu parent item */
div#<?php echo $id; ?> ul.maximenuck li.level1.parent li.parent > a:after,
div#<?php echo $id; ?> ul.maximenuck li.level1.parent li.parent > span.separator:after,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li.parent:hover > a:after,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li.parent.active > a:after {
	display: inline-block;
	content: "";
	width: 0;
	height: 0;
	border-style: solid;
	border-width: 4px 0 4px 4px;
	border-color: transparent transparent transparent #fff;
	margin: 3px;
	float: right;
}

/* styles for right position */
div#<?php echo $id; ?> ul.maximenuck li.level1.align_right,
div#<?php echo $id; ?> ul.maximenuck li.level1.menu_right {
	float:right !important;
	margin-right:0px !important;
}

div#<?php echo $id; ?> ul.maximenuck li.align_right:not(.fullwidth) div.floatck,
div#<?php echo $id; ?> ul.maximenuck li:not(.fullwidth) div.floatck.fixRight {
	left:auto;
	right:0px;
	top:auto;
}


/* arrow image for submenu parent item to open left */
div#<?php echo $id; ?> ul.maximenuck li.level1.parent div.floatck.fixRight li.parent > a:after,
div#<?php echo $id; ?> ul.maximenuck li.level1.parent div.floatck.fixRight li.parent > span.separator:after,
div#<?php echo $id; ?> ul.maximenuck li.level1.parent.menu_right li.parent > a:after,
div#<?php echo $id; ?> ul.maximenuck li.level1.parent.menu_right li.parent > span.separator:after {
	border-color: transparent #fff transparent transparent;
}

/* margin for right elements that rolls to the left */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck div.floatck div.floatck.fixRight,
div#<?php echo $id; ?> ul.maximenuck li.level1.parent.menu_right div.floatck div.floatck  {
    margin-right : 180px;
}

div#<?php echo $id; ?> ul.maximenuck li div.floatck.fixRight{

}


/*---------------------------------------------
---	 	Sublevel items - level 2 to n		---
----------------------------------------------*/

div#<?php echo $id; ?> ul.maximenuck li div.floatck ul.maximenuck2,
div#<?php echo $id; ?> ul.maximenuck2 {
    z-index:11000;
    clear:left;
    text-align : left;
    background : transparent;
    margin : 0 !important;
    padding : 0 !important;
    border : none !important;
    box-shadow: none !important;
    width : 100%; /* important for Chrome and Safari compatibility */
    position: static !important;
    overflow: visible !important;
    display: block !important;
    float: none !important;
    visibility: visible !important;
}

div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.maximenuck,
div#<?php echo $id; ?> ul.maximenuck2 li.maximenuck {
	text-align : left;
	z-index : 11001;
	padding : 2px 0 0 0;
	margin : 0 5px;
	position: static;
	float:none !important;
	list-style : none;
	display: block !important;
	border-bottom : 2px solid #fff;
	margin : 0 5px;
	background : none;
}

div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.maximenuck:hover,
div#<?php echo $id; ?> ul.maximenuck2 li.maximenuck:hover {
}

/* all links styles */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck span.separator,
div#<?php echo $id; ?> ul.maximenuck2 a,
div#<?php echo $id; ?> ul.maximenuck2 li.maximenuck span.separator {
	display: block;
	padding : 3px 0 3px 0;
	margin : 0 2%;
	display:block;
	float : none !important;
	float : left;
	position:relative;
	text-decoration:none;
	outline : none;
	white-space: normal;
	filter: none;
	width: 96%;
	clear:both;
	text-shadow: none;
	color: #fff;
}

/* submenu link */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li a,
div#<?php echo $id; ?> ul.maximenuck2 li a {

}

div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 a,
div#<?php echo $id; ?> ul.maximenuck2 a {
	display: block;
}

div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li:hover > a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li:hover > h2 a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li:hover > h3 a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li.active > a,
div#<?php echo $id; ?> ul.maximenuck2 li:hover > a,
div#<?php echo $id; ?> ul.maximenuck2 li:hover > h2 a,
div#<?php echo $id; ?> ul.maximenuck2 li:hover > h3 a,
div#<?php echo $id; ?> ul.maximenuck2 li.active > a{
	color: #647334;
}

/* item title level 2 to n when mouseover */
div#<?php echo $id; ?> ul.maximenuck2 li li a:hover span.titreck,
div#<?php echo $id; ?> ul.maximenuck2 li.level1 li.current > a span.titreck,
div#<?php echo $id; ?> ul.maximenuck2 li.level1 li.active > a span.titreck {
	color: #808080;
}

/* link image style */
div#<?php echo $id; ?> li.maximenuck > a img {
	margin : 3px;
	border : none;
}

/* img style without link (in separator) */
div#<?php echo $id; ?> li.maximenuck img {
	border : none;
}

/* item title */
div#<?php echo $id; ?> span.titreck {
	/*display : block;*/
	font-weight : normal;
	text-decoration : none;
	float : none !important;
	float : left;
	line-height : 20px;
	margin: 0;
}

/* item description */
div#<?php echo $id; ?> span.descck {
    color : #d7f670;
    display : block;
    text-transform : none;
    font-size : 10px;
    text-decoration : none;
    min-height : 12px;
    line-height : 12px;
    float : none !important;
    float : left;
    margin-top : -5px;
}

/*--------------------------------------------
---		Submenus						------
---------------------------------------------*/

/* submenus container */
div#<?php echo $id; ?> div.floatck {
	position : absolute;
	display: none;
	filter: none;
	border: 0px solid transparent; /* needed for IE */
	padding : 0 0 3px 0;
	margin : 3px;
	filter: none;
	background : url(../images/transparent.gif); /* important for hover to work good under IE7 */
	/*width : 180px;*/ /* default width */
	text-align:left;
	background : #96c21c;
}

div#<?php echo $id; ?> div.maxidrop-main {
	width : 180px; /* default width */
}

/* vertical menu */
div#<?php echo $id; ?>.maximenuckv div.floatck {
	margin : -55px 0 0 95%;
}

div#<?php echo $id; ?> .maxipushdownck div.floatck {
	margin: 0;
}

/* child blocks position (from level2 to n) */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck div.floatck div.floatck {
	margin : -32px 0 0 99%;
	background : #b2b3b4;
}

div#<?php echo $id; ?> div.floatck div.maxidrop-main {
    zoom:1; /* important for IE */
}

div#<?php echo $id; ?> ul.maximenuck2 li li a span.descck {
    color : #e1e1e1;
}

/**
** Show/hide sub menu if mootools is off - horizontal style
**/
div#<?php echo $id; ?> ul.maximenuck li:hover:not(.maximenuckanimation) div.floatck div.floatck, div#<?php echo $id; ?> ul.maximenuck li:hover:not(.maximenuckanimation) div.floatck:hover div.floatck div.floatck, div#<?php echo $id; ?> ul.maximenuck li:hover:not(.maximenuckanimation) div.floatck:hover div.floatck:hover div.floatck div.floatck,
div#<?php echo $id; ?> ul.maximenuck li.sfhover:not(.maximenuckanimation) div.floatck div.floatck, div#<?php echo $id; ?> ul.maximenuck li.sfhover:not(.maximenuckanimation) div.floatck.sfhover div.floatck div.floatck, div#<?php echo $id; ?> ul.maximenuck li.sfhover:not(.maximenuckanimation) div.floatck.sfhover div.floatck.sfhover div.floatck div.floatck {
display: none;
}

div#<?php echo $id; ?> ul.maximenuck li.maximenuck:hover > div.floatck, div#<?php echo $id; ?> ul.maximenuck li.maximenuck:hover > div.floatck li.maximenuck:hover > div.floatck, div#<?php echo $id; ?> ul.maximenuck li.maximenuck:hover>  div.floatck li.maximenuck:hover > div.floatck li.maximenuck:hover > div.floatck, div#<?php echo $id; ?> ul.maximenuck li.maximenuck:hover > div.floatck li.maximenuck:hover > div.floatck li.maximenuck:hover > div.floatck li.maximenuck:hover > div.floatck,
div#<?php echo $id; ?> ul.maximenuck li.sfhover > div.floatck, div#<?php echo $id; ?> ul.maximenuck li.sfhover > div.floatck li.sfhover > div.floatck, div#<?php echo $id; ?> ul.maximenuck li.sfhover > div.floatck li.sfhover > div.floatck li.sfhover > div.floatck, div#<?php echo $id; ?> ul.maximenuck li.sfhover > div.floatck li.sfhover > div.floatck li.sfhover > div.floatck li.sfhover > div.floatck {
display: block;
}

div#<?php echo $id; ?> div.maximenuck_mod ul {
display: block;
}

/*---------------------------------------------
---	 	Columns management					---
----------------------------------------------*/

div#<?php echo $id; ?> ul.maximenuck li div.floatck div.maximenuck2 {
	/*width : 180px;*/ /* default width */
	margin: 0;
	padding: 0;
}


/* h2 title */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 h2 a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 h2 span.separator,
div#<?php echo $id; ?> ul.maximenuck2 h2 a,
div#<?php echo $id; ?> ul.maximenuck2 h2 span.separator {
	font-size:21px;
	font-weight:400;
	letter-spacing:-1px;
	margin:7px 0 14px 0;
	padding-bottom:14px;
	line-height:21px;
	text-align:left;
}

/* h3 title */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 h3 a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 h3 span.separator,
div#<?php echo $id; ?> ul.maximenuck2 h3 a,
div#<?php echo $id; ?> ul.maximenuck2 h3 span.separator {
	font-size:14px;
	margin:7px 0 14px 0;
	padding-bottom:7px;
	line-height:21px;
	text-align:left;
}
    
/* paragraph */
div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li p,
div#<?php echo $id; ?> ul.maximenuck2 li p {
	line-height:18px;
	margin:0 0 10px 0;
	font-size:12px;
	text-align:left;
}




/* image shadow with specific class */
div#<?php echo $id; ?> .imgshadow { /* Better style on light background */
	background:#FFFFFF !important;
	padding:4px;
	border:1px solid #777777;
	margin-top:5px;
	-moz-box-shadow:0px 0px 5px #666666;
	-webkit-box-shadow:0px 0px 5px #666666;
	box-shadow:0px 0px 5px #666666;
}

/* blackbox style */
div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.blackbox,
div#<?php echo $id; ?> ul.maximenuck2 li.blackbox {
	background-color:#333333 !important;
	color: #eeeeee;
	text-shadow: 1px 1px 1px #000;
	padding:4px 6px 4px 6px !important;
	margin: 0px 4px 4px 4px !important;
	-moz-border-radius: 5px;
    -webkit-border-radius: 5px;
    border-radius: 5px;
	-webkit-box-shadow:inset 0 0 3px #000000;
	-moz-box-shadow:inset 0 0 3px #000000;
	box-shadow:inset 0 0 3px #000000;
}

div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.blackbox:hover,
div#<?php echo $id; ?> ul.maximenuck2 li.blackbox:hover {
	background-color:#333333 !important;
}

div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.blackbox a,
div#<?php echo $id; ?> ul.maximenuck2 li.blackbox a {
	color: #fff;
	text-shadow: 1px 1px 1px #000;
	display: inline !important;
}

div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.blackbox:hover > a,
div#<?php echo $id; ?> ul.maximenuck2 li.blackbox:hover > a{
	text-decoration: underline;
}

/* greybox style */
div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.greybox,
div#<?php echo $id; ?> ul.maximenuck2 li.greybox {
	background:#f0f0f0 !important;
	border:1px solid #bbbbbb;
	padding: 4px 6px 4px 6px !important;
	margin: 0px 4px 4px 4px !important;
	-moz-border-radius: 5px;
    -webkit-border-radius: 5px;
    -khtml-border-radius: 5px;
    border-radius: 5px;
}

div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.greybox:hover,
div#<?php echo $id; ?> ul.maximenuck2 li.greybox:hover {
	background:#ffffff !important;
	border:1px solid #aaaaaa;
}

/**
** child items
**/

div#<?php echo $id; ?> ul.maximenuck2 li a span.descck {
    margin-top : -4px;
}


div#<?php echo $id; ?>.maximenuckh ul.maximenuck li.level1 div.maxidrop-top {
	height : 11px;
	margin-top: -11px;
	background : url(../images/top_arrow_level1.png) 15px bottom no-repeat;
}

div#<?php echo $id; ?>.maximenuckv ul.maximenuck li.level1 > div.floatck > div.maxidrop-top {
	height : 20px;
    width : 20px;
    position : absolute;
    margin-left : -10px;
    margin-top : 6px;
	background : url(../images/top_arrow_level1.png) left top no-repeat;
	transform:rotate(-90deg);
	-ms-transform:rotate(-90deg); /* IE 9 */
	-webkit-transform:rotate(-90deg); /* Opera, Chrome, and Safari */
}

div#<?php echo $id; ?> ul.maximenuck li:not(.fullwidth) ul.maximenuck2 div.maxidrop-top,
div#<?php echo $id; ?> ul.maximenuck2 div.maxidrop-top{
    height : 20px;
    width : 10px;
    position : absolute;
    margin-left : -10px;
    margin-top : 6px;
    background : url(../images/left_arrow_level1.png) left top no-repeat;
}

div#<?php echo $id; ?> ul.maximenuck ul.maximenuck2 div.fixRight>div.maxidrop-top {
    height : 20px;
    width : 10px;
    position : absolute;
    margin-right : -10px;
    margin-left : 180px;
    margin-top : 2px;
    float : right;
    background : url(../images/right_arrow_level1.png) right top no-repeat;
}


div#<?php echo $id; ?> ul.maximenuck2 li.maximenuck {
    /*width : 100%;*/
}

div#<?php echo $id; ?> ul.maximenuck2 li.maximenuck>a:hover {
    background : url(../images/li_level1nhover_bg.png) left 2px repeat-x;
}

/* child item container  */
div#<?php echo $id; ?> ul.maximenuck li div.floatck {
    padding-top : 3px;
}

/*---------------------------------------------
---	 	Module in submenus					---
----------------------------------------------*/

/* module title */
div#<?php echo $id; ?> ul.maximenuck div.maximenuck_mod > div > h3,
div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod > div > h3 {
	width : 100%;
	font-weight : bold;
	font-size: 16px;
}

div#<?php echo $id; ?> div.maximenuck_mod {
    width : 100%;
    padding : 0;
    overflow : hidden;
    white-space : normal;
}

div#<?php echo $id; ?> div.maximenuck_mod div.moduletable {
    border : none;
    background : none;
}

div#<?php echo $id; ?> div.maximenuck_mod  fieldset{
    width : 160px;
    padding : 0;
    margin : 0 0 0 3px;
    overflow : hidden;
    background : transparent;
    border : none;
    display : block;
    float : none;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod a {
    border : none;
    margin : 0;
    padding : 0;
    display : inline;
    background : transparent;
    color : #647334;
    font-weight : normal;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod a:hover {
    color : #FFF;
}

/* module title */
div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod h3 {
    font-size : 14px;
    width : 100%;
    font-size : 14px;
    font-weight : normal;
    background : none;
    margin : 5px 0 0 0;
    padding : 3px 0 3px 0;
    text-indent : 5px;
    color : #d7f670;
    border-bottom : 2px dotted #d7f670;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod ul {
    margin : 0;
    padding : 0;
    width : 100%;
    background : none;
    border : none;
    text-align : left;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod ul li,
div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod ul li:hover,
div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod ul li.sfhover,
div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod ul li.parent-activesfhover {
    margin : 0 0 0 15px;
    padding : 0;
    background : none;
    border : none;
    text-align : left;
    font-size : 11px;
    float : none;
    display : block;
    line-height : 20px;
    white-space : normal;
}

/* login module */
div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod #form-login ul {
    left : 0;
    margin : 0;
    padding : 0;
    width : 100%;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod #form-login ul li {
    margin : 2px 0;
    padding : 0 5px;
    height : 20px;
    background : transparent;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod form,
div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod h4,
div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod p,
div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod div {
    left : 0;
    margin : 0;
    padding : 0;
    color : #eee;
    background : transparent;
    border : none;
    float : none;
    position : static;
    height : 100%;
    width : 100%;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod form label {
    left : 0;
    margin : 0;
    padding : 0;
    color : #eee;
    background : transparent;
    border : none;
    float : none;
    position : static;
    height : 100%;
}

/*---------------------------------------------
---	 	Fancy styles (floating cursor)		---
----------------------------------------------*/

div#<?php echo $id; ?> .maxiFancybackground {
	position: absolute;
    top : 0;
    list-style : none;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
	z-index: -1;
}

div#<?php echo $id; ?> .maxiFancybackground .maxiFancycenter {
    border-top : #96bf0c 2px solid;
    margin-right : 20px;
}

div#<?php echo $id; ?> .maxiFancybackground .maxiFancyleft {

}

div#<?php echo $id; ?> .maxiFancybackground .maxiFancyright {

}

div#<?php echo $id; ?> span.maxiclose {
    color: #fff;
}

/*---------------------------------------------
---	 	Button to close on click			---
----------------------------------------------*/

div#<?php echo $id; ?> span.maxiclose {
    color: #fff;
}

/*---------------------------------------------
---	 Stop the dropdown                  ---
----------------------------------------------*/

div#<?php echo $id; ?> ul.maximenuck li.maximenuck.nodropdown div.floatck,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck div.floatck li.maximenuck.nodropdown div.floatck,
div#<?php echo $id; ?> .maxipushdownck div.floatck div.floatck {
	position: static;
	background:  none;
	border: none;
	left: auto;
	margin: 3px;
	moz-box-shadow: none;
	-webkit-box-shadow: none;
	box-shadow: none;
	display: block !important;
}

div#<?php echo $id; ?> ul.maximenuck li.level1.parent ul.maximenuck2 li.maximenuck.nodropdown li.maximenuck,
div#<?php echo $id; ?> .maxipushdownck ul.maximenuck2 li.maximenuck.nodropdown li.maximenuck {
	background: none;
	text-indent: 5px;
}

div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.parent ul.maximenuck2 li.maximenuck.parent.nodropdown > a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.parent ul.maximenuck2 li.maximenuck.parent.nodropdown > span.separator,
div#<?php echo $id; ?> .maxipushdownck ul.maximenuck2 li.maximenuck.parent.nodropdown > a,
div#<?php echo $id; ?> .maxipushdownck ul.maximenuck2 li.maximenuck.parent.nodropdown > span.separator {
	background:  none;
}

/* remove the arrow image for parent item */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.parent ul.maximenuck2 li.parent.nodropdown > *:after,
div#<?php echo $id; ?> .maxipushdownck ul.maximenuck2 li.parent > *:after {
	display: none;
}

div#<?php echo $id; ?> li.maximenuck.nodropdown > div.floatck > div.maxidrop-main {
	width: auto;
}

/*---------------------------------------------
---	 Full width				                ---
----------------------------------------------*/

div#<?php echo $id; ?>.maximenuckh li.fullwidth > div.floatck {
	margin: 0;
	padding: 0;
	width: auto !important;
	left: 0;
	right: 0;
}

div#<?php echo $id; ?>.maximenuckv li.fullwidth > div.floatck {
	margin: 0 0 0 -5px;
	padding: 0;
	top: 0;
	bottom: 0;
	left: 100%;
}
div#<?php echo $id; ?> li.fullwidth > div.floatck > div.maxidrop-main {
	width: auto;
}