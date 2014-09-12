<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '../library/');
require_once 'GClient/Google_Client.php';
require_once 'GClient/contrib/Google_DriveService.php';
$client = new Google_Client();
$client->setApplicationName("CloudSync");
$client->setUseObjects(true);
$client->setClientId(CLIENTID);
$client->setClientSecret(CLIENTSECRET);
$client->setRedirectUri($_SERVER['HTTP_HOST'].'/cloud.php');
$client->setDeveloperKey(DEVELOPERKEY);
$client->setScopes(array('https://www.googleapis.com/auth/drive'));
//, 'https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/userinfo.email'
?>