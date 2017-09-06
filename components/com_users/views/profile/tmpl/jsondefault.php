<?php
$response=new stdClass();
$response->user=JFactory::getUser();
$response->id=3;
$response->fbId=3;
$response->name="new name";
$response->street="new street";
$response->city="new city";
$response->accessToken=JFactory::getSession()->getToken();
$response->token=JFactory::getSession()->getToken();
echo json_encode($response);
die;
?>