<?php
session_start();
include('./psConfig.php');
$get_plex_token = array();
$plexServer_url = array();
$getConfig = array();
$clientsxml = array();
$clientdata = array();
//Pseudo Channel Config File
$pseudoConfig = $pseudochannel . "pseudo_config.py";
$getConfig = file_get_contents($pseudoConfig);
$getConfig = explode("\n",$getConfig);

//Get Plex data from plex_token file in Pseudo Channel
$plex_token = $pseudochannel . "plex_token.py";
$get_token = file_get_contents($plex_token);
$get_plex_token = explode("\n", $get_token);
$baseurl = $get_plex_token['1'];
$baseurl = str_replace("'","",$baseurl);
$baseurl = str_replace("baseurl = ","",$baseurl);
$plexServer_url = parse_url($baseurl);
$plexServer = $plexServer_url['host'];
$plexport = $plexServer_url['port'];
$token = $get_plex_token['0'];
$token = str_replace("'","",$token);
$plexToken = str_replace("token = ","",$token);

//Get Client Name from Pseudo Channel Config
$configClientName = $getConfig[40];
$configClientName = trim($configClientName, 'plexClients = ["');
$configClientName = str_replace('"]','',$configClientName);
$configClientName = trim($configClientName);
$pseudochannelMaster = $pseudochannel;
$pseudochannelMaster = trim($pseudochannelMaster);
$pseudochannelTrim = rtrim($pseudochannel,'/');
$plexClientName = $configClientName;
$user = "pi";
$user = $pseudochannel;
$user = strstr($user, "/home/"); //gets all text from needle on
$user = strstr($user, "/channels", true); //gets all text before needle
$userpatharr = explode('/home/', $user);
if(count($userpatharr) > 1){
	$user = $userpatharr[1];
}else {
	$user = 'pi';
}

if (isset($_SESSION['tv'])) {
	$plexClientName = $_SESSION['tv'];
	$urlstring = "tv=" . $_SESSION['tv'] . "&";
        if ($_SESSION['tv'] != $configClientName) {
		$pseudochannel = $pseudochannelTrim . "_" . $_SESSION['tv'] . "/";
		$pseudochannel = trim($pseudochannel);
	}
} else {
	$urlstring = "";
}
if (isset($_SESSION['size'])) {
	$DisplayType=$_SESSION['size'];
} else {
	$_SESSION['size'] = $DisplayType;
}
//Get Other Plex Client Data from Plex API
$clientsurl = "http://" . $plexServer . ":" . $plexport . "/clients?X-Plex-Token=" . $plexToken;
$getclientsxml = file_get_contents($clientsurl);
$clientsxml = simplexml_load_string($getclientsxml);
foreach($clientsxml->Server as $key => $clientdata) {
		$clientname = $clientdata['name'];
		if($clientname == $plexClientName) {
			$plexClientIP = $clientdata['address'];
			$plexClient = trim($plexClientIP);
			$plexClientUID = $clientdata['machineIdentifier'];
		}
}
?>
