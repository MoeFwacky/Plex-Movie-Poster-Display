<!DOCTYPE html>
<?php
$ch_file="";
include('./control.php');
include('./config.php');
$tvlocations = glob($pseudochannelTrim . "*", GLOB_ONLYDIR);
$boxes = '';
foreach ($tvlocations as $tvbox) {
	if ($tvbox . "/"  == $pseudochannelMaster) {
		$boxname = $configClientName;
		$boxes .= "<li><a href='schedule.php?tv=$boxname' class='gn-icon gn-icon-videos'>TV: $boxname</a></li>";
	} else {
		$boxname = explode("_", $tvbox);
		$boxes .= "<li><a href='schedule.php?tv=$boxname[1]' class='gn-icon gn-icon-videos'>TV: $boxname[1]</a></li>";
	}
}
?>
<html lang="en" class="no-js" style="height:100%">
	<head>
            <script
            src="https://code.jquery.com/jquery-2.2.4.min.js"
            integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
            crossorigin="anonymous">
            </script>
                <script>
                        const queryString = window.location.search;
                        const urlParams = new URLSearchParams(queryString);
                        const tv = urlParams.get('tv');
			if (tv != "null") {
				topbar = "topbar.php?tv="+tv;
                        	getdata = "getData.php?tv="+tv;
				getclock = "getClock.php?tv="+tv;
			} else {
				topbar = "topbar.php";
				getdata = "getData.php";
				getclock = "getClock.php";
			}
                        $(document).ready( function() {
                                $("#topbar").load(topbar);
				$.getJSON(getdata,function(data) {
					$.each(data, function(key,val) {
						$('#'+key).html(val);
					});
				});
                        });
                        $(document).ready(
                            function() {
                                setInterval(function() {
                                    $.getJSON(getdata,function(data) {
                                        $.each(data, function(key, val) {
                                            $('#'+key).html(val);
                                        });
                                    });
                                }, 60000);
                            });
			$(document).ready(
				function() {
					setInterval(function() {
						$.getJSON(getclock,function(data) {
							$.each(data, function(key, val) {
								$('#'+key).html(val);
							});
						});
					}, 5000);
				});
                </script>
		<style type="text/css">a {text-decoration: none}</style>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script src="js/classie.js"></script>
		<script src="js/gnmenu.js"></script>
		<script src="js/modernizr.custom.js"></script>
		<script>
		var query = window.location.search.substring(1)
		if(query.length) {
			if(window.history != undefined && window.history.pushState != undefined) {
				window.history.pushState({}, document.title, window.location.pathname);
			}
		}
		</script>
		<script language="JavaScript">
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
		
		<?php
		if (isset($_GET['ch'])) {
			$id= "ch" . $_GET['ch'];
		} else {
			$id="rightnow";
		}
		if (isset($_GET['tv'])) {
			$tv = $_GET['tv'];
			if ($tv != "null") {
				$plexClientName = $_GET['tv'];
				$urlstring = "tv=" . $_GET['tv'] . "&";
				$urlstring = $urlstring;
				if ($_GET['tv'] != $configClientName) {
					$pseudochannel = $pseudochannelTrim . "_" . $_GET['tv'] . "/";
					$pseudochannel = trim($pseudochannel);
				}
			} else {
				$tv = plexClientName;
				$urlstring = "";
			}
		} else {
			$tv = $plexClientName;
		}
		if (isset($_GET['time'])) {
		        $time = $_GET['time'];
	        } else {
		        $time = "0";
		}

		$dircontents=array();
		//GET ALL PSEUDO CHANNEL DAILY SCHEDULE XML FILE LOCATIONS
		$lsgrep = exec("find ". $pseudochannelMaster . "pseudo-channel_*/schedules | grep xml | tr '\n' ','"); //list the paths of all daily schedule xml files in as comma sparated
		$dircontents = explode(",", $lsgrep); //write file locations into an array
		$scheduleTable = "<table width='100%' max-width='100%' class='schedule-table'><thead><tr width='100%'><th width='4%' max-width='4%'>&nbsp;Ch.&nbsp;</th><th colspan='1' width='8%' max-width='8%' id=nowtime>Now</th><th colspan='1' width='8%' max-width='8%' id=timePlus15>+15</th><th colspan='1' width='8%' max-width='8%' id=timePlus30>+30</th><th colspan='1' width='8%' max-width='8%' id=timePlus45>+45</th><th colspan='1' max-width='8%' width='8%' id=timePlus60>+60</th><th colspan='1' width='8%' max-width='8%' id=timePlus75>+75</th><th colspan='1' max-width='8%' width='8%' id=timePlus90>+90</th><th colspan='1' width='8%' max-width='8%' id=timePlus105>+105</th><th colspan='1' max-width='8%' width='8%' id=timePlus120>+120</th><th colspan='1' width='8%' max-width='8%' id=timePlus135>+135</th><th colspan='1' max-width='8%' width='8%' id=timePlus150>+150</th><th colspan='1' width='8%' max-width='8%' id=timePlus165>+165</th></tr></thead><tbody>";
		$nowPlayingDisplay = "<p style='color:yellow'>$plexClientName <span style='color:white' id='nowplaying' class='container'>Please Stand By</span></p>";

		foreach ($dircontents as $xmlfile) { //do the following for each xml schedule file
		$ch_file = str_replace($pseudochannelMaster . "pseudo-channel_", "ch", $xmlfile); //get channel number
		$ch_file = str_replace("/schedules/pseudo_schedule.xml", "", $ch_file);
		$ch_number = str_replace("ch", "", $ch_file);
		$ch_row = "row" . $ch_number;
		$ch_cell = "chan" . $ch_number;
		if($xmlfile){
			$xmldata = simplexml_load_file($xmlfile); //load the xml schedule file
		}
		if($xmldata){
			$scheduleTable .= "<tr class='schedule-table' max-width='100%' min-width='100%' width='100%' id='$ch_row'></tr>";
		}
		}
		$scheduleTable .= "</tbody></table>";
		?>
		<div class="container main-container">
			<?php echo $nowPlayingDisplay;
			echo $scheduleTable; ?>
			</br><a href="schedule.php?action=updateweb&<?php echo $urlstring; ?>" style="color:white" href="">Update Channel Schedule Data &#8594;</a>
		</div><!-- /container -->
		<div id="topbar" name="topbar"></div>
	</body>
</html>
