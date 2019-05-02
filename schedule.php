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
	</head>
	<body>
		<p style="top:100px"><?php echo $plexClientName; ?></p>
		<?php
		if (isset($_GET['ch'])) {
			$id= "ch" . $_GET['ch'];
		} else {
			$id="rightnow";
		}
		if (isset($_GET['tv'])) {
			$_SESSION['tv'] = $_GET['tv'];
			$urlstring = "tv=" . $_GET['tv'] . "&";
			$_SESSION['urlstring'] = $urlstring;
		} else {
			$_SESSION['tv'] = $plexClientName;
		}
		?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<div id="container">
			<div class="container" style="position:absolute;top:60px" scrolling="no"><p style="color:white" id="nowplaying" class="container">Please Stand By<? php echo $plexClientName; ?></p>
			<div id="<?php echo $id; ?>" class="container" name="schedulearea" type="text/html";></div>
			<ul id="gn-menu" class="gn-menu-main">
				<li class="gn-trigger">
					<a class="gn-icon gn-icon-menu"><span>Menu</span></a>
					<nav class="gn-menu-wrapper">
						<div class="gn-scroller">
							<ul class="gn-menu">
								<li><a href="index.php" class="gn-icon gn-icon-help">Home</a></li>
								<li><a href="adminConfig.php?<?php echo $urlstring;?>" class="gn-icon gn-icon-cog">Settings</a></li>
								<?php echo $boxes; ?>
							</ul>
						</div><!-- /gn-scroller -->
					</nav>
				</li>
				<li><a class="codrops-icon" href="schedule.php?action=up&<?php echo $urlstring; ?>">Up</a></li>
				<li><a class="codrops-icon" href="schedule.php?action=down&<?php echo $urlstring; ?>">Down</a></li>
				<li><a class="codrops-icon" href="schedule.php?action=stop&<?php echo $urlstring; ?>">Stop</a></li>
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
