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
$pseudochannelTrim = rtrim($pseudochannel,'/');
$plexClientName = $configClientName;
if (isset($_SESSION['tv'])) {
	$plexClientName = $_SESSION['tv'];
        if ($_SESSION['tv'] != $configClientName) {
		$pseudochannel = $pseudochannelTrim . "_" . $_SESSION['tv'] . "/";
		$pseudochannel = trim($pseudochannel);
	}
}


//Get Other Plex Client Data from Plex API
$clientsurl = "http://" . $plexServer . ":" . $plexport . "/clients?X-Plex-Token=" . $plexToken;
$getclientsxml = file_get_contents($clientsurl);
$clientsxml = simplexml_load_string($getclientsxml);
foreach($clientsxml->Server as $key => $clientdata) {
		$clientname = $clientdata[name];
		if($clientname == $plexClientName) {
			$plexClientIP = $clientdata[address];
			$plexClient = trim($plexClientIP);
			$plexClientUID = $clientdata[machineIdentifier];
		}
}
?>
