<?php
header('content-type: text/css');
$id = htmlspecialchars($_GET['monid'], ENT_QUOTES);
?>

.clr {clear:both;visibility: hidden;}

/*---------------------------------------------
---	 	menu container						---
----------------------------------------------*/

/* menu */
div#<?php echo $id; ?> {
	font-size:14px;
	line-height:21px;
	text-align:left;
	zoom:1;
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
	background :  #333;
	min-height : 40px;
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
    /*padding : 0;*/
    margin : 0;
    list-style : none;
    text-align:center;
    cursor: pointer;
	filter: none;
	float: left;
}

/** IE 7 only **/
*+html div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1 {
	display: inline !important;
}

/* vertical menu */
div#<?php echo $id; ?>.maximenuckv ul.maximenuck li.maximenuck.level1 {
	display: block !important;
	margin: 0;
	padding: 4px 0px 2px 8px;
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
    background : url(../images/transparent.gif); /* important for hover to work good under IE7 */
	/*width : 180px;*/ /* default width */
	text-align:left;
	margin-top : -5px;
}

div#<?php echo $id; ?> div.maxidrop-main {
	width : 180px; /* default width */
}

/* vertical menu */
div#<?php echo $id; ?>.maximenuckv div.floatck {
	margin : -39px 0 0 90%;
}

div#<?php echo $id; ?> .maxipushdownck div.floatck {
	margin: 0;
}

/* child blocks position (from level2 to n) */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck div.floatck div.floatck {
	margin : -40px 0 0 93%;
}

div#<?php echo $id; ?> .maxipushdownck div.floatck {
	margin: 0;
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

div#<?php echo $id; ?> div.floatck div.maxidrop-main {
    background : url(../images/main_col-r.png) top right repeat-y;
    zoom:1; /* important for IE */
    padding-right: 5px;
	margin: 0 -5px 0 0;
}

div#<?php echo $id; ?> div.floatck div.maxidrop-main2 {
    background : url(../images/main_col.png) top left repeat-y;
    zoom:1; /* important for IE */
    padding-left: 5px;
	margin: 0 0 0 -5px;
}

div#<?php echo $id; ?> div.floatck div.maxidrop-bottom {
    background : url(../images/bottom_col-r.png) top right no-repeat;
    height : 9px;
    padding-right: 9px;
	margin: 0 -5px 0 0;
}

div#<?php echo $id; ?> div.floatck div.maxidrop-bottom2 {
    background : url(../images/bottom_col.png) top left no-repeat;
    height : 9px;
	margin: 0 0 0 -5px;
}

div#<?php echo $id; ?> div.floatck div.maxidrop-top {
    background : url(../images/top_col-r.png) top right no-repeat;
    height : 9px;
    padding-right: 9px;
	margin: 0 -5px 0 0;
}

/* pushdown layout */
div#<?php echo $id; ?> .maxipushdownck div.floatck {
	background: #669e00;
}


div#<?php echo $id; ?> div.floatck div.maxidrop-top2 {
    background : url(../images/top_col.png) top left no-repeat;
    height : 9px;
	margin: 0 0 0 -5px;
}

/*---------------------------------------------
---	 	Module in submenus					---
----------------------------------------------*/

div#<?php echo $id; ?> div.maximenuck_mod ul {
display: block;
}


div#<?php echo $id; ?> ul.maximenuck li.maximenuck,
div#<?php echo $id; ?> li.maximenuck {
    background : none;
    list-style : none;
    border : none;
    padding : 0;
    margin : 0;
}

div#<?php echo $id; ?> ul.maximenuck div {
    /*background : none;
    list-style : none;
    border : none;
    padding : 0;
    margin : 0;*/
}


/* image style */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck img {
    border : none;
    margin : 3px;
}

div#<?php echo $id; ?> ul.maximenuck li a.maximenuck,
div#<?php echo $id; ?> ul.maximenuck li span.separator,
div#<?php echo $id; ?> li a.maximenuck,
div#<?php echo $id; ?> li span.separator {
    text-decoration : none;
    text-indent : 2px;
    outline : none;
    background : none;
    border : none;
    padding : 0;
    margin : 0;
    cursor : pointer;
    color : #efefef;
    text-align : center;
}

div#<?php echo $id; ?> ul.maximenuck li a span {
    text-decoration : none;
    outline : none;
    background : none;
    border : none;
    padding : 0;
    margin : 0;
    cursor : pointer;
    color : #efefef;
}

/* separator item */
div#<?php echo $id; ?> ul.maximenuck li span.separator {

}



/**
** first level items
**/

div#<?php echo $id; ?> ul.maximenuck li.level1 {
    height : 40px;
    margin : 0 10px !important;
}

div#<?php echo $id; ?> ul.maximenuck li.level1>a,
div#<?php echo $id; ?> ul.maximenuck li.level1>span.separator {
    height : 40px;
}

div#<?php echo $id; ?> ul.maximenuck li.level1>a:hover span,
div#<?php echo $id; ?> ul.maximenuck li.level1:hover>a span,
div#<?php echo $id; ?> ul.maximenuck li.level1.sfhover>a span,
div#<?php echo $id; ?> ul.maximenuck li.level1.sfhover>span {
    color : #808080;
}

div#<?php echo $id; ?> ul.maximenuck li.level1.current>a:hover span,
div#<?php echo $id; ?> ul.maximenuck li.level1.current:hover>a span,
div#<?php echo $id; ?> ul.maximenuck li.level1.current.sfhover>a span {
    color : #efefef;
}


div#<?php echo $id; ?> ul.maximenuck li.level1.current,
div#<?php echo $id; ?> ul.maximenuck li.level1.active {
    background : url(../images/li_level1_right.png) top right no-repeat !important;
    padding : 0 10px 0 0 !important;
}

div#<?php echo $id; ?> ul.maximenuck li.level1.current>a,
div#<?php echo $id; ?> ul.maximenuck li.level1.active>a {
    background : url(../images/li_level1_left.png) top left no-repeat !important;
    padding : 0 0 0 10px !important;
}

div#<?php echo $id; ?> ul.maximenuck li.parent.level1.current>a span.titreck,
div#<?php echo $id; ?> ul.maximenuck li.parent.level1.active>a span.titreck {
    background : url(../images/arrow_verti_active.png) center bottom no-repeat;
    height : 33px;
}

div#<?php echo $id; ?> ul.maximenuck li.parent.level1.current>a span,
div#<?php echo $id; ?> ul.maximenuck li.parent.level1.active>a span {
    color : #efefef;
}


/* first level item title */
div#<?php echo $id; ?> ul.maximenuck li.level1>a span.titreck,
div#<?php echo $id; ?> ul.maximenuck li.level1>span span.titreck{
    padding-top : 5px;
}


/* first level item description */
div#<?php echo $id; ?> ul.maximenuck li.level1>a span.descck {
    margin-top : -3px;
}

/* first level item link */
div#<?php echo $id; ?> ul.maximenuck li.parent.level1>a span.titreck,
div#<?php echo $id; ?> ul.maximenuck li.parent.level1>span span.titreck{
    background : url(../images/arrow_verti.png) center bottom no-repeat;
    height : 33px;
}

/* active parent style level 1 to n */
div#<?php echo $id; ?> ul.maximenuck li.parent.level1 li.parent.active>a span.titreck {
    background : url(../images/arrow_horiz_active.png) 150px 8px no-repeat;
    height : 20px;
}

/* parent style level 1 to n */
div#<?php echo $id; ?> ul.maximenuck li.parent.level1 li.parent>a span.titreck,
div#<?php echo $id; ?> ul.maximenuck li.parent.level1 li.parent.current>a span.titreck{
    background : url(../images/arrow_horiz.png) 150px 8px no-repeat;
    height : 20px;
}



/**
** items title and descriptions
**/

/* item title */
div#<?php echo $id; ?> span.titreck {
    display : block;
    text-transform : none;
    font-weight : normal;
    font-size : 12px;
    line-height : 18px;
    text-decoration : none;
    min-height : 17px;
    float : none !important;
    float : left;
	margin: 0;
}

/* item description */
div#<?php echo $id; ?> span.descck {
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

}

/**
** active items
**/


/* current & active item title and description */
div#<?php echo $id; ?> ul.maximenuck li.current>a span.titreck,
div#<?php echo $id; ?> ul.maximenuck li.active>a span.titreck {
    font-weight : bold;
}

div#<?php echo $id; ?> ul.maximenuck2 li.current>a span.titreck {
    color : #4e7900;
}


/* current item title when mouseover */
div#<?php echo $id; ?> ul.maximenuck li.current>a:hover span.titreck {

}

/* current item description when mouseover */
div#<?php echo $id; ?> ul.maximenuck li.current>a:hover span.descck {

}

/**
** child items
**/



div#<?php echo $id; ?> ul.maximenuck2 li a.maximenuck {
    text-decoration : none;
    margin : 0 auto;
    height : 34px;
    padding : 3px 0 3px 0;
    margin : 0 3px;
    /*width : 165px;*/
	display: block;
	text-align: left;
    background : url(../images/li_level2.png) top left no-repeat !important;
}

div#<?php echo $id; ?> ul.maximenuck2 li.maximenuck>a:hover {
    background : url(../images/li_level2_hover.png) top left no-repeat !important;
}

/* child item block */
div#<?php echo $id; ?> ul.maximenuck ul.maximenuck2,
div#<?php echo $id; ?> ul.maximenuck2 {
    padding : 0;
	margin: 0;
    border : none;
    /*width : 174px;*/ /* important for Chrome and Safari compatibility */
    background : none;
}

div#<?php echo $id; ?> ul.maximenuck2 li.maximenuck {
    /*width : 100%;*/
    padding : 0;
    border : none;
    margin : 0 5px !important;
    background : none;
    display : block;
    float: none;
}


/**
** module style
**/

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
    color : #efefef;
    font-weight : normal;
    text-decoration : underline;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod a:hover {
    text-decoration : none;
}

/* module title */
div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod h3 {
    font-size : 14px;
    width : 100%;
    font-size : 14px;
    font-weight : normal;
    background : #96bf0c;
    margin : 5px 0 0 0;
    padding : 3px 0 3px 0;
    text-indent : 5px;
    color : #eee;
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
    width : 155px;
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
    background : #c6ff5e;
    height : 25px;
    margin :  10px 0 0 1px;
}

div#<?php echo $id; ?> .maxiFancybackground .maxiFancyleft {

}

div#<?php echo $id; ?> .maxiFancybackground .maxiFancyright {

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
