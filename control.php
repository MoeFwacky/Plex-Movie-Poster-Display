<?php
function Channel() {
include('config.php');
if(empty($_GET["tv"]) || $_GET["tv"] == $configClientName) {
        $ps = "$pseudochannelMaster";
} else {
	$pseudochannel = substr($pseudochannel, 0, -1);
        $ps = "$pseudochannelTrim" . "_" . $_GET["tv"];
}
	$channel_number = $_GET["num"];
	ob_start();
	echo exec("cd " . "$ps" . " && sudo -u pi /bin/bash manual.sh " . "$channel_number");
	ob_end_clean();
}
function stopAllChannels() {
include('config.php');
if(empty($_GET["tv"]) || $_GET["tv"] == $configClientName) {
        $ps = "$pseudochannelMaster";
} else {
	$pseudochannel = substr($pseudochannel, 0, -1);
        $ps = "$pseudochannelTrim" . "_" . $_GET["tv"];
}
	ob_start();
	echo exec("cd " . "$ps" . " && sudo -u pi /bin/bash stop-all-channels.sh");
	ob_end_clean();
}
function channel_down() {
include('config.php');
if(empty($_GET["tv"]) || $_GET["tv"] == $configClientName) {
        $ps = "$pseudochannelMaster";
} else {
	$pseudochannel = substr($pseudochannel, 0, -1);
        $ps = "$pseudochannelTrim" . "_" . $_GET["tv"];
}
	ob_start();
        echo exec("cd " . "$ps" . " && sudo -u pi /bin/bash channeldown.sh");
	ob_end_clean();
}
function channel_up() {
include('config.php');
if(empty($_GET["tv"]) || $_GET["tv"] == $configClientName) {
        $ps = "$pseudochannelMaster";
} else {
	$pseudochannel = substr($pseudochannel, 0, -1);
        $ps = "$pseudochannelTrim" . "_" . $_GET["tv"];
}
	ob_start();
        echo exec("cd " . "$ps" . " && sudo -u pi /bin/bash channelup.sh");
	ob_end_clean();
}
function databaseUpdate() {
include('config.php');
$ps = "$pseudochannelMaster";
	ob_start();
	echo exec("cd " . "$ps" . " && sudo -u pi /bin/bash globalupdate.sh");
	echo exec("/bin/bash updatexml.sh");
	ob_end_clean();
}
if(isset($_GET['action'])){
	switch($_GET['action']) {
	        case 'stop':
	                stopAllChannels();
	        break;
		case 'channel':
			Channel();
		break;
		case 'down':
			channel_down();
		break;
		case 'up':
			channel_up();
		break;
		case 'update':
			databaseUpdate();
		break;
	}
}

?>
