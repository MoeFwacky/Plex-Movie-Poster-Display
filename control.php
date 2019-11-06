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
	echo exec("ps aux | grep '[m]anual.sh'", $o);
	if(count($o) <= 0){
		echo exec("cd " . "$ps" . " && sudo -u $user /bin/bash manual.sh " . "$channel_number > /dev/null 2>/dev/null &");
	}
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
	echo exec("ps aux | grep '[s]top-all-channels.sh'", $o);
	if(count($o) <= 0){
		echo exec("cd " . "$ps" . " && sudo -u $user /bin/bash stop-all-channels.sh > /dev/null 2>/dev/null &");
	}
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
	echo exec("ps aux | grep '[c]hanneldown.sh'", $o);
	if(count($o) <= 0){
        echo exec("cd " . "$ps" . " && sudo -u $user /bin/bash channeldown.sh > /dev/null 2>/dev/null &");
    }
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
	echo exec("ps aux | grep '[c]hannelup.sh'", $o);
	if(count($o) <= 0){
        echo exec("cd " . "$ps" . " && sudo -u $user /bin/bash channelup.sh > /dev/null 2>/dev/null &");
    }
	ob_end_clean();
}
function update_web() {
include('config.php');
if(empty($_GET["tv"]) || $_GET["tv"] == $configClientName) {
        $ps = "$pseudochannelMaster";
} else {
	$pseudochannel = substr($pseudochannel, 0, -1);
        $ps = "$pseudochannelTrim" . "_" . $_GET["tv"];
}
	ob_start();
    echo exec("ps aux | grep '[u]pdateweb.sh'", $o);
    //error_log(print_r(count($o), TRUE)); 
    if(count($o) <= 0){
    	echo exec("cd " . "$ps" . " && sudo -u $user /bin/bash updateweb.sh > /dev/null 2>/dev/null &");
    }
	ob_end_clean();
}
function purge_favicon_cache() {
	ob_start();
        echo exec("sudo rm -rf ./logos");
	ob_end_clean();
}
function databaseUpdate() {
include('config.php');
$ps = "$pseudochannelMaster";
	ob_start();
	echo exec("cd " . "$ps" . " && sudo -u $user /bin/bash globalupdate.sh");
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
		case 'updateweb':
			update_web();
		break;
		case 'purgefaviconcache':
			purge_favicon_cache();
		break;
	}
}

?>
