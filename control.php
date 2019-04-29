<?php
function Channel() {
include('config.php');
if(empty($_GET["tv"])) {
        $ps = "$pseudochannel";
} else {
	$pseudochannel = substr($pseudochannel, 0, -1);
        $ps = "$pseudochannel" . "_" . $_GET["tv"];
}
	$channel_number = $_GET["num"];
	ob_start();
	echo exec("cd " . "$ps" . " && sudo /bin/bash manual.sh " . "$channel_number");
	ob_end_clean();
}
function stopAllChannels() {
include('config.php');
if(empty($_GET["tv"])) {
        $ps = "$pseudochannel";
} else {
	$pseudochannel = substr($pseudochannel, 0, -1);
        $ps = "$pseudochannel" . "_" . $_GET["tv"];
}
	ob_start();
	echo exec("cd " . "$ps" . " && sudo /bin/bash stop-all-channels.sh");
	ob_end_clean();
}
function channel_down() {
include('config.php');
if(empty($_GET["tv"])) {
        $ps = "$pseudochannel";
} else {
	$pseudochannel = substr($pseudochannel, 0, -1);
        $ps = "$pseudochannel" . "_" . $_GET["tv"];
}
	ob_start();
        echo exec("cd " . "$ps" . " && sudo /bin/bash channeldown.sh");
	ob_end_clean();
}
function channel_up() {
include('config.php');
if(empty($_GET["tv"])) {
        $ps = "$pseudochannel";
} else {
	$pseudochannel = substr($pseudochannel, 0, -1);
        $ps = "$pseudochannel" . "_" . $_GET["tv"];
}
	ob_start();
        echo exec("cd " . "$ps" . " && sudo /bin/bash channelup.sh");
	ob_end_clean();
}

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
}

?>
