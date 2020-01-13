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
		$myfile = fopen("psConfig.php", "w") or die("<p color=white>Unable to open file!</p>");
		$txt = "<?php //Pseudo Channel
		\$pseudochannel = '$_POST[pseudochannel]';
		\n//Display Type
		\$DisplayType = '$_POST[DisplayType]';
		\n//Schedule Type
		\$ScheduleType = '$_POST[ScheduleType]';
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
if ($ScheduleType == "portrait" || $_POST['DisplayType'] == "portrait") {
	$portraitstatus = "checked";
	$landscapestatus = "";
} elseif ($ScheduleType =="landscape" || $_POST['ScheduleType'] == "landscape") {
	$portraitstatus = "";
	$landscapesatus = "checked";
} else {
	$portratstatus = "";
	$landscapestatus = "";
}
?>
	</head>
	<body>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<div id="container">
			<div class="container" style="position:absolute;top:60px" scrolling="no">
				<h3 style="color:white" class="gn-icon gn-icon-cog">Settings</h3>
					<div class="container" name="schedulearea" type="text/html";>
						<form method="post">
							<div class="container" name="schedulearea" type="text/html";>
								<label style="padding-left:10px;padding-right:5px;color:white">Pseudo Channel Directory: </label></br>&nbsp;&nbsp;&nbsp;
								<input type="text" style="padding-right:50px" name="pseudochannel" value="<?php echo "$pseudochannelMaster"; ?>"></br></br>
								<label style="padding-left:10px;color:white">Status Screen Display Type:</label></br>
								<a style="padding-left:50px;color:white"><input type="radio" name="DisplayType" value="full" style="padding-left:20px" <?php echo "$fullstatus"; ?> >Full</input></a>
								<a style="padding-left:20px;color:white"><input type="radio" name="DisplayType" value="half" style="padding-left:20px" <?php echo "$halfstatus"; ?> >Half</input></a></br></br>
								<label style="padding-left:10px;color:white">Web Schedule Display Type:</label></br>
								<a style="padding-left:50px;color:white"><input type="radio" name="ScheduleType" value="portrait" style="padding-left:20px" <?php echo "$portraitstatus"; ?> >Portrait</input></a>
								<a style="padding-left:20px;color:white"><input type="radio" name="ScheduleType" value="landscape" style="padding-left:20px" <?php echo "$landscapestatus"; ?> >Landscape</input></a>
							</div>
							
							<div style="padding-left:50px">
								<input class="btn btn-primary"type="submit" value="Save Changes" name='submit' />
							</div>
							<?php if($update == "1") {
								echo "<div class='alert alert-info' style='color:white;padding-left:50px'>Settings File Updated.</div>";
							} ?>
						</form>
					</div>
				<div class="dripdrop" style="color:white;padding-left:10px"></br>
				<a class="dripdrop-title">Plex Server Data</a></br></br>
				<a class="dripdrop-header">IP Address:</a></br>
				<a><?php echo $plexServer; ?></a></br></br>
				<a class="dripdrop-header">Web Port:</a></br>
				<a><?php echo $plexport; ?></a></br></br>
				<a class="dripdrop-header">Web Token:</a></br>
				<a><?php echo $plexToken; ?></a></br></br>
				<?php echo $clientinfodump; ?>
				<div style="">
					<a class="btn btn-primary" style="color:white!important;" href="schedule.php?action=purgefaviconcache&<?php echo $urlstring; ?>">&#8594; Purge Logo Image Cache</a>
				</div>
			</div>
			<ul id="gn-menu" class="gn-menu-main">
				<li class="gn-trigger">
					<a class="gn-icon gn-icon-menu"><span>Menu</span></a>
					<nav class="gn-menu-wrapper">
						<div class="gn-scroller">
							<ul class="gn-menu">
								<li><a href="index.php" class="gn-icon gn-icon-earth">Home</a></li>
								<li><a href="adminConfig.php?<?php echo $urlstring; ?>" class="gn-icon gn-icon-cog">Settings</a></li>
								<?php echo $boxes; ?>
							</ul>
						</div><!-- /gn-scroller -->
					</nav>
				</li>
				<li><a class="codrops-icon" href="schedule.php?action=up?<?php echo $urlstring; ?>">Up</a></li>
				<li><a class="codrops-icon" href="schedule.php?action=down?<?php echo $urlstring; ?>">Down</a></li>
				<li><a class="codrops-icon" href="schedule.php?action=stop?<?php echo $urlstring; ?>">Stop</a></li>
				<li><a class="codrops-icon" href="schedule.php?action=updateweb&<?php echo $urlstring; ?>">Update Web</a></li>
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
