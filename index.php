<!DOCTYPE html>
<?php
session_start();
include('./control.php');
include('./config.php');
$tvlocations = glob($pseudochannelTrim . "*", GLOB_ONLYDIR);
foreach ($tvlocations as $tvbox) {
        if ($tvbox . "/"  == $pseudochannelMaster) {
                $boxname = $configClientName;
                $boxes .= "<li><a href='schedule.php?tv=$boxname' class='gn-icon gn-icon-videos'>TV: $boxname</a></li>";
        } else {
		$boxname = trim($tvbox, $pseudochannelTrim . "_");
		$boxes .= "<li><a href='schedule.php?tv=$boxname' class='gn-icon gn-icon-videos'>TV: $boxname</a></li>";
	}
}
$clientcount = 1;
foreach ($clientsxml->Server as $key => $xmlarray) {
	$clientinfodump .= "<a class='dripdrop-title'>Plex Client #$clientcount</a></br><a class='dripdrop-header'>Name:</a></br><a href='schedule.php?tv=$xmlarray[name]' style='color:white'> $xmlarray[name] </a></br></br>";
	$clientinfodump .= "<a class='dripdrop-header'>Local IP Address:</a></br><a> $xmlarray[address] </a></br></br>";
	$clientinfodump .= "<a class='dripdrop-header'>Unique Identifier</a></br><a> $xmlarray[machineIdentifier] </a></br></br>";
	$clientcount = $clientcount + 1;
	}
?>
<html lang="en" class="no-js" style="height:100%">
	<head>
		<style type="text/css">a {text-decoration: none}</style>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0;">
		<title>FakeTV Guide and Control</title>
		<meta name="description" content="A page that works with Pseudo Channel and Plex to display now playing data and allow viewing and navigation of Pseudo Channel schedules" />
		<link rel="shortcut icon" href="../favicon.ico">
		<link rel="stylesheet" type="text/css" href="css/normalize.css" />
		<link rel="stylesheet" type="text/css" href="css/demo.css" />
		<link rel="stylesheet" type="text/css" href="css/component.css" />
		<link rel="apple-touch-icon" sizes="180x180" href="assets/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="assets/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="assets/favicon-16x16.png">
		<link rel="manifest" href="assets/site.webmanifest">
		<link rel="mask-icon" href="assets/safari-pinned-tab.svg" color="#5bbad5">
		<link rel="shortcut icon" href="assets/favicon.ico">
		<meta name="msapplication-TileColor" content="#2b5797">
		<meta name="msapplication-config" content="assets/browserconfig.xml">
		<meta name="theme-color" content="#ffffff">
		<script src="js/modernizr.custom.js"></script>
		<script
	    src="https://code.jquery.com/jquery-2.2.4.min.js"
	    integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
	    crossorigin="anonymous">
	    </script>
		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
		<script>
		        $(document).ready(
		            function() {
		                setInterval(function() {
		                    $.getJSON('getData.php',function(data) {
		                        $.each(data, function(key, val) {
		                            $('#'+key).html(val);
		                        });
		                    });
		                }, 1000);
		            });
		</script>
		<script language="JavaScript">
		function channel() {
			<?php $id="$ch_file"; ?>
		}
		setInterval(autorefresh_div, 3000);
		function httpGet(theUrl)
		{
			var xmlHttp = new XMLHttpRequest();
			xmlHttp.open( "GET", theUrl, false );
			xmlHttp.send( null );
			return xmlHttp.responseText;
		}
		</script>
	<?php if (!empty($_POST)) {
		$myfile = fopen("psConfig.php", "w") or die("Unable to open file!");
		$txt = "<?php //Pseudo Channel
		\$pseudochannel = '$_POST[pseudochannel]';
		\n//Display Type
		\$DisplayType = '$_POST[DisplayType]';
		?>
		";
		echo  $txt;
		fwrite($myfile, $txt);
		fclose($myfile);
		$update = "1";

	} ?>
<?php include_once('config.php');
if ($DisplayType == "half" || $_POST['DisplayType'] == "half") {
        $halfstatus = "checked";
        $fullstatus = "";
} elseif ($DisplayType =="full" || $_POST['DisplayType'] == "full") {
	$halfstatus = "";
	$fullstatus = "checked";
} else {
	$halfstatus = "";
	$fullstatus = "";
}
?>
	</head>
	<body>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<div id="container">
			<div class="container" style="position:absolute;top:60px" scrolling="no"><h3 style="color:white" class="gn-icon gn-icon-earth">Pseudo Channel Web Interface</h3>
			<div class="container" name="schedulearea" type="text/html";>
			<div class="container" name="schedulearea" type="text/html";>
			</div>
			</div>
			</div>
			<?php if($update == "1") {
				echo "<div class='alert alert-info' style='color:white;padding-left:50px'>Settings File Updated.</div>";
			} ?>
			</form>
			</div></br></br></br></br></br></br>
			<div class="dripdrop-header" style="color:white;padding-left:10px">
			<p>Welcome to Pseudo Channel, your homebrew television network.</p></div>
			<div class="dripdrop-paragraph" style="color:white;padding-left:10px"><ul>
			<li>Use this web interface to control your Pseudo Channel instance.</li>
			<li>Click the menu icon in the top left to access the Now Playing pages.</li>
			<li>Use the buttons at the top of the page to flip through your channels or to turn it off.</li></ul>
			<p class="dripdrop-header">What is Pseudo Channel?</p>
			<ul><li>Pseudo Channel is a near-recreation of the classic broadcast television experience using your Plex Server as a base.</li>
			<li>Use Pseudo Channel to schedule shows and random movie blocks with user-defined commercials in between.</li></ul></div>
			<div class="dripdrop-header" style="color:white;padding-left:10px"><p><a style="color:white" href="https://discord.gg/aPaybPp">Join us on Discord</a></div>
			<div class="dripdrop-paragraph" style="color:white;padding-left:10px"><ul><li>Join the <u><a style="color:white" href="https://discord.gg/aPaybPp">FakeTV Discord Server</a></u> for operating help and ongoing development updates and discussion.</li></ul>
			</div>
			<div class="dripdrop" style="color:white;padding-left:10px"></br>
			<a class="dripdrop-header">Plex Server: </a><a><?php echo $plexServer; ?>:<?php echo $plexport; ?></a></br>
			<a class="dripdrop-header">Pseudo Channel: </a><a><?php echo $pseudochannelMaster; ?></a></div>
			<ul id="gn-menu" class="gn-menu-main">
				<li class="gn-trigger">
					<a class="gn-icon gn-icon-menu"><span>Menu</span></a>
					<nav class="gn-menu-wrapper">
						<div class="gn-scroller">
							<ul class="gn-menu">
								<li><a href="index.php" class="gn-icon gn-icon-help">Home</a></li>
								<li><a href="adminConfig.php?<?php echo $urlstring; ?>" class="gn-icon gn-icon-cog">Settings</a></li>
								<?php echo $boxes; ?>
							</ul>
						</div><!-- /gn-scroller -->
					</nav>
				</li>
				<li></li>
			</ul>
		</div><!-- /container -->
		<script src="js/classie.js"></script>
		<script src="js/gnmenu.js"></script>
		<script>
			new gnMenu( document.getElementById( 'gn-menu' ) );
		</script>
	</body>
</html>
