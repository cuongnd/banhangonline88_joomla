<?
$lang=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);

if		($lang=='de')	{include 'lang/de.php';}
elseif	($lang=='el')	{include 'lang/el.php';}
elseif	($lang=='es')	{include 'lang/es.php';}
elseif	($lang=='fr')	{include 'lang/fr.php';}
elseif	($lang=='hu')	{include 'lang/hu.php';}
elseif	($lang=='it')	{include 'lang/it.php';}
elseif	($lang=='jp')	{include 'lang/jp.php';}
elseif	($lang=='pt')	{include 'lang/pt.php';}

else	{include 'lang/en.php';}
?>