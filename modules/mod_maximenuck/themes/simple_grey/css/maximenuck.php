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
	font-size:14px;
	line-height:21px;
	text-align:left;
}

/* container style */
div#<?php echo $id; ?> ul.maximenuck {
	clear:both;
    position : relative;
    z-index:999;
    overflow: visible !important;
    display: block !important;
    float: none !important;
    visibility: visible !important;
	opacity: 1 !important;
    list-style:none;
	padding: 0;
    margin:0 auto;
    zoom:1;
	filter: none;
	background : #666 url(../images/fond_bg.png) top left repeat-x;
    min-height : 34px;
}

div#<?php echo $id; ?>.maximenuckv ul.maximenuck {
	background : #666;
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
	padding : 0 10px;
    margin : 0;
    list-style : none;
    text-align:center;
    cursor: pointer;
	filter: none;
	float: left;
    background : url(../images/separator.png) top right repeat-y;
	min-height : 32px;
}

/** IE 7 only **/
*+html div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1 {
	display: inline !important;
}

/* vertical menu */
div#<?php echo $id; ?>.maximenuckv ul.maximenuck li.maximenuck.level1 {
	display: block !important;
	margin: 0;
	padding: 0;
	text-align: left;
}

div#<?php echo $id; ?>.maximenuckv ul.maximenuck li.maximenuck.level1:hover,
div#<?php echo $id; ?>.maximenuckv ul.maximenuck li.maximenuck.level1.active {
	background : #666 url(../images/fond_bg.png) top left repeat-x;
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
    white-space: normal;
	filter: none;
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
	padding : 0;
    margin : 0;
    background : #666 url(../images/transparent.gif); /* important for hover to work good under IE7 */
	/*width : 180px;*/ /* default width */
	text-align:left;
}

div#<?php echo $id; ?> .maxipushdownck div.floatck {
	margin: 0;
}

div#<?php echo $id; ?> div.maxidrop-main {
	width : 180px; /* default width */
}

/* vertical menu */
div#<?php echo $id; ?>.maximenuckv div.floatck {
	margin : -35px 0 0 98%;
}

/* child blocks position (from level2 to n) */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck div.floatck div.floatck {
	margin : -30px 0 0 98%;
}

/**
** Show/hide sub menu if mootools is off - horizontal style
**/
div#<?php echo $id; ?> ul.maximenuck li:hover div.floatck div.floatck, div#<?php echo $id; ?> ul.maximenuck li:hover div.floatck:hover div.floatck div.floatck, div#<?php echo $id; ?> ul.maximenuck li:hover div.floatck:hover div.floatck:hover div.floatck div.floatck,
div#<?php echo $id; ?> ul.maximenuck li.sfhover div.floatck div.floatck, div#<?php echo $id; ?> ul.maximenuck li.sfhover div.floatck.sfhover div.floatck div.floatck, div#<?php echo $id; ?> ul.maximenuck li.sfhover div.floatck.sfhover div.floatck.sfhover div.floatck div.floatck {
display: none;
}

div#<?php echo $id; ?> ul.maximenuck li.maximenuck:hover > div.floatck, div#<?php echo $id; ?> ul.maximenuck li.maximenuck:hover > div.floatck li.maximenuck:hover > div.floatck, div#<?php echo $id; ?> ul.maximenuck li.maximenuck:hover>  div.floatck li.maximenuck:hover > div.floatck li.maximenuck:hover > div.floatck, div#<?php echo $id; ?> ul.maximenuck li.maximenuck:hover > div.floatck li.maximenuck:hover > div.floatck li.maximenuck:hover > div.floatck li.maximenuck:hover > div.floatck,
div#<?php echo $id; ?> ul.maximenuck li.sfhover > div.floatck, div#<?php echo $id; ?> ul.maximenuck li.sfhover > div.floatck li.sfhover > div.floatck, div#<?php echo $id; ?> ul.maximenuck li.sfhover > div.floatck li.sfhover > div.floatck li.sfhover > div.floatck, div#<?php echo $id; ?> ul.maximenuck li.sfhover > div.floatck li.sfhover > div.floatck li.sfhover > div.floatck li.sfhover > div.floatck {
display: block;
}

div#<?php echo $id; ?> div.maximenuck_mod ul {
display: block;
}

div#<?php echo $id; ?> ul.maximenuck li.maximenuck,
div#<?php echo $id; ?> li.maximenuck {
    background : none;
    list-style : none;
    border : none;
}

/* link image style */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck>a img,
div#<?php echo $id; ?> li.maximenuck>a img {
    margin : 3px;
    border : none;
}

/* img style without link (in separator) */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck img {
    border : none;
}

div#<?php echo $id; ?> ul.maximenuck li a.maximenuck, 
div#<?php echo $id; ?> ul.maximenuck li span.separator,
div#<?php echo $id; ?> li a.maximenuck,
div#<?php echo $id; ?> li span.separator {
    text-decoration : none;
    text-indent : 2px;
    min-height : 27px;
    outline : none;
    background : none;
    border : none;
    padding : 0;
    cursor : pointer;
    color : #1a1a1a;
}

/* separator item */
div#<?php echo $id; ?> ul.maximenuck li span.separator {

}

/**
** active items
**/

/* current item title and description */
div#<?php echo $id; ?> ul.maximenuck li.current>a span.titreck,
div#<?php echo $id; ?> li.current>a span.titreck {
    color : #ccc;
}

/* current item title when mouseover */
div#<?php echo $id; ?> ul.maximenuck li.current>a:hover span.titreck,
div#<?php echo $id; ?> li.current>a:hover span.titreck {

}

/* current item description when mouseover */
div#<?php echo $id; ?> ul.maximenuck li.current>a:hover span.descck,
div#<?php echo $id; ?> li.current>a:hover span.descck {

}

/* active parent title */
div#<?php echo $id; ?> ul.maximenuck li.active>a span.titreck,
div#<?php echo $id; ?> li.active>a span.titreck {
    color : #ccc;
}

/* active parent description */
div#<?php echo $id; ?> ul.maximenuck li.active>a span.descck,
div#<?php echo $id; ?> li.active>a span.descck {

}

/**
** first level items
**/


/* first level item title */
div#<?php echo $id; ?> ul.maximenuck li.level1>a span.titreck {
    color : #333;
}

/* first level item description */
div#<?php echo $id; ?> ul.maximenuck li.level1>a span.descck,
div#<?php echo $id; ?> ul.maximenuck li.level1>span.separator span.descck {
    color : #efefef;
}

/* first level item link */
div#<?php echo $id; ?>.maximenuckh ul.maximenuck li.parent.level1>a,
div#<?php echo $id; ?>.maximenuckh ul.maximenuck li.parent.level1>span {
    background : url(../images/maxi_arrow0.png) bottom right no-repeat;
}

/* parent style level 0 */
div#<?php echo $id; ?>.maximenuckv ul.maximenuck li.parent.level1 > a,
div#<?php echo $id; ?> ul.maximenuck li.parent.level1 li.parent {
    background : url(../images/maxi_arrow1.png) center right no-repeat;
}

/**
** items title and descriptions
**/

/* item title */
div#<?php echo $id; ?> span.titreck {
    color : #1a1a1a;
    display : block;
    text-transform : none;
    font-weight : normal;
    font-size : 14px;
    line-height : 18px;
    text-decoration : none;
    min-height : 17px;
    float : none !important;
    float : left;
}

/* item description */
div#<?php echo $id; ?> span.descck {
    color : #c0c0c0;
    display : block;
    text-transform : none;
    font-size : 10px;
    text-decoration : none;
    height : 12px;
    line-height : 12px;
    float : none !important;
    float : left;
}

/* item title when mouseover */
div#<?php echo $id; ?> ul.maximenuck  a:hover span.titreck {
    color : #ddd;
}

/**
** child items
**/

/* child item title */
div#<?php echo $id; ?> ul.maximenuck2  a.maximenuck {
    /*width : 100%;*/
}

div#<?php echo $id; ?> ul.maximenuck2 li a.maximenuck,
div#<?php echo $id; ?> ul.maximenuck2 li span.separator {
    text-decoration : none;
    border-bottom : 1px solid #505050;
   width : 96%;
    margin : 0 2%;
    padding : 3px 0 3px 0;
	display: block;
	text-align: left;
}

/* child item block */
div#<?php echo $id; ?> ul.maximenuck ul.maximenuck2,
div#<?php echo $id; ?> ul.maximenuck2 {
    margin : 3px 0 0 0;
    padding : 0;
    border : none;
    width: 100%; /* important for Chrome and Safari compatibility */
}

div#<?php echo $id; ?> ul.maximenuck2 li.maximenuck {
    width: 100%;
    padding : 2px 0 0 0;
    border : none;
    margin : 0;
    background : none;
    display : block;
    float: none;
}

/* child item container  */
div#<?php echo $id; ?> ul.maximenuck li div.floatck,
div#<?php echo $id; ?> div.floatck {
    background : #666;
    border : 1px solid #dde2e3;
}

/**
** module style
**/

div#<?php echo $id; ?> div.maximenuck_mod {
    width : 100%;
    padding : 0;
    
    color : #ddd;
    white-space : normal;
}

div#<?php echo $id; ?> div.maximenuck_mod div.moduletable {
    border : none;
    background : none;
}

div#<?php echo $id; ?> div.maximenuck_mod  fieldset{
    width : 100%;
    padding : 0;
    margin : 0 auto;
    
    background : #666;
    border : none;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod a {
    border : none;
    margin : 0;
    padding : 0;
    display : inline;
    background : #666;
    color : #efefef;
    font-weight : normal;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod a:hover {
    color : #FFF;
}

/* module title */
div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod h3 {
    font-size : 14px;
    width : 100%;
    color : #aaa;
    font-size : 14px;
    font-weight : normal;
    background : #444;
    margin : 5px 0 0 0;
    padding : 3px 0 3px 0;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod ul {
    margin : 0;
    padding : 0;
    width : 100%;
    background : none;
    border : none;
    text-align : left;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod li {
    margin : 0 0 0 15px;
    padding : 0;
    width : 100%;
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


/**
** columns width & child position
**/

/* child blocks position (from level2 to n) */

/* margin for overflown elements that rolls to the left */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck div.floatck div.floatck.fixRight  {
    margin-right : 180px;
}

/**
** fancy parameters
**/

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
    background: #b9bdbe;
    height : 34px;
}

div#<?php echo $id; ?> .maxiFancybackground .maxiFancyleft {

}

div#<?php echo $id; ?> .maxiFancybackground .maxiFancyright {

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
