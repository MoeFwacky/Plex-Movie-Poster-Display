<!DOCTYPE html>
<?php
$ch_file="";
include('./control.php');
include('./config.php');
$tvlocations = glob($pseudochannelTrim . "*", GLOB_ONLYDIR);
$boxes = '';
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
                        	getdata = "getSchedule.php?tv="+tv;
				getclock = "getClock.php?tv="+tv;
			} else {
				topbar = "topbar.php";
				getdata = "getSchedule.php";
				getclock = "getClock.php";
			}
                        $(document).ready( function() {
                                $("#topbar").load(topbar);
				$.getJSON(topbar,function(data) {
					$.each(data, function(key,val) {
						$('#'+key).html(val);
					});
				});
                        });
                </script>
		<style type="text/css">a {text-decoration: none}</style>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>FakeTV Schedule Editor for Pseudo Channel</title>
		<meta name="description" content="A page to edit Pseudo Channel database schedules" />
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
<!--		<script>
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
		</script> -->
	</head>
	<body>
		
		<?php
		$dircontents=array();
		$scheduleTable = "<table width='100%' max-width='100%' class='schedule-table'><thead><tr width='100%'><th width='4%' max-width='4%'>&nbsp;ID&nbsp;</th><th colspan='1' width='8%' max-width='8%'>Day</th><th colspan='1' width='8%' max-width='8%'>Start Time</th><th colspan='1' width='8%' max-width='8%'>Section</th><th colspan='1' max-width='8%' width='8%'>Title</th><th colspan='1' width='8%' max-width='8%'>Time Mode</th><th colspan='1' max-width='8%' width='8%'>Duration</th><th colspan='1' width='8%' max-width='8%'>Time Shift</th><th colspan='1' max-width='8%' width='8%'>Max Preempt</th><th colspan='1' width='8%' max-width='8%'>Xtra</th><th>Submit</th></thead><tbody>";

		//GET ALL PSEUDO CHANNEL DATABASE FILE LOCATIONS
		$DBarray = array();
		$globString = $pseudochannelMaster . "pseudo-channel_*/pseudo-channel.db";
		$DBarray = glob($globString);
		if(isset($_GET['channel'])) {
			$channelNumber = $_GET['channel'];
		} else {
			$channelNumber = "01";
		}
		if(isset($_GET['dayOfWeek'])) {
			$selectDayOfWeek = $_GET['dayOfWeek'];
		} else {
			$selectDayOfWeek = "all";
		}
		$channelDropdown = "<table><span style='color:white'>CHANNEL: </span><form method='get'><select name='channel' style='width:4em'>";
		foreach($DBarray as $channelFromList) {
			$channelFile = str_replace($pseudochannelMaster . "pseudo-channel_", "", $channelFromList);
			$channelFileNumber = str_replace("/pseudo-channel.db", "", $channelFile);
			if ($channelNumber == $channelFileNumber) {
				$channelDropdown .= "<option value='" . $channelFileNumber . "' selected>" . $channelFileNumber . "</option>";
			} else {
				$channelDropdown .= "<option value='" . $channelFileNumber . "'>" . $channelFileNumber . "</option>";
			}
		}
		$channelDropdown .= "</select>";

		$daysDropDown = "<span style='color:white'> DAY: </span><select name='dayOfWeek' style='width:10em'>";
		$allDays = array('all','sundays','mondays','tuesdays','wednesdays','thursdays','fridays','saturdays','weekends','weekdays','everyday');
		foreach ($allDays as $theDay) {
			if ($selectDayOfWeek == $theDay) {
				$daysDropDown .= "<option value='" . $theDay . "' selected>" . ucfirst($theDay) . "</option>";
			} else {
				$daysDropDown .= "<option value='" . $theDay . "'>" . ucfirst($theDay) . "</option>";
			}
		}
		$daysDropDown .="</select><input type='submit'></form></table></br>";

		foreach ($DBarray as $databasefile) { //do the following for each database file
			if($databasefile) {
				$psDB = new SQLite3($databasefile);
				$ch_file = str_replace($pseudochannelMaster . "pseudo-channel_", "ch", $databasefile); //get channel number
				$ch_file = str_replace("/pseudo-channel.db", "", $ch_file);
				$ch_number = str_replace("ch", "", $ch_file);
				$ch_row = "row" . $ch_number;
				$sqlData = array();
				if($channelNumber == $ch_number) {
				if(isset($_GET['addRow'])) {
					if ($_GET['addRow'] == "addAbove") {
						$getStartTimeUnix = strval(strtotime($_GET['startTime']) - (30 * 60));
						$getStartTime = strftime("%H:%M:%S", $getStartTimeUnix);
						$rightNow = intval(time());
						$getID = intval($_GET['id']);
						$getDay = $_GET['day'];
						$insertStatement = $psDB->prepare("INSERT INTO schedule VALUES (null, :unix, '999', 'random', '19,30', :startTime, '0', :dayOfWeek, :startTimeUnix, 'TV Shows', 'secondary', '5', '30', '')");
						$insertStatement->bindParam(':unix', $rightNow);
						$insertStatement->bindParam(':startTime', $getStartTime);
						$insertStatement->bindParam(':dayOfWeek', $getDay);
						$insertStatement->bindParam(':startTimeUnix', $getStartTimeUnix);
						$insertResult = $insertStatement->execute();
					} elseif ($_GET['addRow'] == "addBelow") {
						$getStartTimeUnix = strval(strtotime($_GET['startTime']) + (30 * 60));
						$getStartTime = strftime("%H:%M:%S", $getStartTimeUnix);
						$rightNow = intval(time());
						$getID = intval($_GET['id']) + 1;
						$getDay = $_GET['day'];
						$insertStatement = $psDB->prepare("INSERT INTO schedule VALUES (null, :unix, '999', 'random', '19,30', :startTime, '0', :dayOfWeek, :startTimeUnix, 'TV Shows', 'secondary', '5', '30', '')");
						$insertStatement->bindParam(':unix', $rightNow);
						$insertStatement->bindParam(':startTime', $getStartTime);
						$insertStatement->bindParam(':dayOfWeek', $getDay);
						$insertStatement->bindParam(':startTimeUnix', $getStartTimeUnix);
						$insertResult = $insertStatement->execute();
					} elseif ($_GET['addRow'] == "delete") {
						$getID = intval($_GET['id']);
						$deleteStatement = $psDB->prepare("DELETE FROM schedule WHERE ID = :getID");
						$deleteStatement->bindParam(':getID', $getID);
						$deleteResult = $deleteStatement->execute();
					}
				}
					if($selectDayOfWeek != "all") {
//						$result = $psDB->query("SELECT * FROM schedule ORDER BY CASE WHEN dayOfWeek='all' THEN 0 WHEN dayOfWeek='sundays' THEN 1 WHEN dayOfWeek='mondays' THEN 2 WHEN dayOfWeek='tuesdays' THEN 3 WHEN dayOfWeek='wednesdays' THEN 4 WHEN dayOfWeek='thursdays' THEN 5 WHEN dayOfWeek='fridays' THEN 6 WHEN dayOfWeek='saturdays' THEN 7 WHEN dayOfWeek='weekends' THEN 8 WHEN dayOfWeek='weekdays' THEN 9 WHEN dayOfWeek='everyday' THEN 10 END, startTime");
						$statement = $psDB->prepare("SELECT * FROM schedule WHERE dayOfWeek = :day ORDER BY startTime");
						$statement->bindParam(':day', $selectDayOfWeek);
						$result = $statement->execute();
					} else {
//						$result = $psDB->query("SELECT * FROM schedule ORDER BY dayOfWeek, startTime");
						$result = $psDB->query("SELECT * FROM schedule ORDER BY CASE WHEN dayOfWeek='all' THEN 0 WHEN dayOfWeek='sundays' THEN 1 WHEN dayOfWeek='mondays' THEN 2 WHEN dayOfWeek='tuesdays' THEN 3 WHEN dayOfWeek='wednesdays' THEN 4 WHEN dayOfWeek='thursdays' THEN 5 WHEN dayOfWeek='fridays' THEN 6 WHEN dayOfWeek='saturdays' THEN 7 WHEN dayOfWeek='weekends' THEN 8 WHEN dayOfWeek='weekdays' THEN 9 WHEN dayOfWeek='everyday' THEN 10 END, startTime");
					}
					while ($sqlData = $result->fetchArray()) {
						if ($sqlData['mediaID'] != 9999) {
							$sectionValue = $sqlData['section'];
							$sectionText = $sqlData['section'];
						} else {
							$sectionValue = "random";
							$sectionText = "Random Episode";
						}
				                if ($sqlData['strictTime'] == "true") {
				                        $timeMode = "Strict Start Time";
				                } elseif ($sqlData['strictTime'] == "false") {
				                        $timeMode = "Variable Start Time";
				                } elseif ($sqlData['strictTime'] == "secondary") {
				                        $timeMode = "Allow Preempting";
				                } else {
				                        $timeMode = "INVALID ENTRY";
				                }
						$explodeDuration = explode(",", $sqlData['duration']);
						if ($sqlData['mediaID'] == 9999) {
							if($explodeDuration[0] == "0" && $explodeDuration[1] == "43200000") {
	               	        	                        $durationStatement = $psDB->prepare("SELECT * FROM shows WHERE title == :title");
        	        	                                $durationStatement->bindParam(':title', $sqlData['title']);
                		                                $durationResult = $durationStatement->execute();
								$durationArray = $durationResult->fetchArray();
								$scheduleDuration = round($durationArray['duration'] / 60000) . " Mins";
							} else {
								$scheduleDuration = $explodeDuration[0] . " - " . $explodeDuration[1] . " Mins";
							}
						} elseif ($sqlData['title'] != "random") {
	               	                                $durationStatement = $psDB->prepare("SELECT * FROM shows WHERE title == :title");
        	                                        $durationStatement->bindParam(':title', $sqlData['title']);
                	                                $durationResult = $durationStatement->execute();
							$durationArray = $durationResult->fetchArray();
							$scheduleDuration = round($durationArray['duration'] / 60000) . " Mins";
						} else {
							if($explodeDuration[0] == "0" && $explodeDuration[1] == "43200000") {
								$scheduleDuration = "Any";
							} else {
							$scheduleDuration = $explodeDuration[0] . " - " . $explodeDuration[1] . " Mins";
							}
						}
				                if ($sqlData['xtra'] != "") {
				                        $xtraData = str_replace(";","</br>",$sqlData['xtra']);
				                        $xtraData = str_replace(":",": ",$xtraData);
				                        $xtraData = ucwords($xtraData,">");
				                        $xtraData = ucwords($xtraData," ");
				                        $xtraData = ucwords($xtraData,"-");
				                } else {
				                        $xtraData = "";
				                }
						$scheduleTable .= "<tr><td>" . $sqlData['id'] . "</td>";
						$scheduleTable .= "<td><span name='" . $ch_row . "Day' style='width: 6em'>" . ucfirst($sqlData['dayOfWeek']) . "</span></td>";
						$scheduleTable .= "<td><span name='" . $ch_row . "Start' style='width: 7em'>" . $sqlData['startTime'] . "</span></td>";
						$scheduleTable .= "<td><span name='" . $ch_row . "Section' style='width: 9em'>" . ucfirst($sectionText) . "</span></td>";
						$scheduleTable .= "<td><span name='" . $ch_row . "Title' style='width: 20em'>" . ucfirst($sqlData['title']) . "</td>";
						$scheduleTable .= "<td><span name='" . $ch_row . "strictTime'>" . $timeMode . "</td>";
						$scheduleTable .= "<td><span name='" . $ch_row . "DurationMin' style='width: 7em'>" . $scheduleDuration . "</span></td>";
						$scheduleTable .= "<td><span name='" . $ch_row . "TimeShift' style='width: 4em'>" . $sqlData['timeShift'] . " Mins</td>";
						$scheduleTable .= "<td><span name='" . $ch_row . "OverlapMax' style='width: 4em'>" . $sqlData['overlapMax'] . " Mins</td>";
						$scheduleTable .= "<td><span name='" . $ch_row . "Xtra'>" . $xtraData . "</td>";
						$scheduleTable .= "<td><form action='db-schedule.php' method='get'>";
						$scheduleTable .= "<input type='hidden' name='channel' value=" . $channelNumber . ">";
						$scheduleTable .= "<input type='hidden' name='id' value=" . $sqlData['id'] . ">";
						if(isset($_GET['dayOfWeek'])) {
							$scheduleTable .= "<input type='hidden' name='dayOfWeek' value=" . $_GET['dayOfWeek'] . ">";
						}
						$scheduleTable .= "<input type='hidden' name='day' value=" . $sqlData['dayOfWeek'] . ">";
						$scheduleTable .= "<input type='hidden' name='startTime' value=" . $sqlData['startTime'] . ">";
						$scheduleTable .= "<button type='submit' name='addRow' value='addAbove'>Add Above</button></form>";
						$scheduleTable .= "<form action='db-edit.php' method='get' style='display:inline-block'><input type='hidden' name='channelNumber' value=" . $channelNumber . "><button type='submit' name='id' value=" . $sqlData['id'] . ">Edit Row</button></form>";
						$scheduleTable .= "<form action='db-schedule.php' method='get' style='display:inline-block'>";
						$scheduleTable .= "<input type='hidden' name='channel' value=" . $channelNumber . ">";
						$scheduleTable .= "<input type='hidden' name='id' value=" . $sqlData['id'] . ">";
						if(isset($_GET['dayOfWeek'])) {
							$scheduleTable .= "<input type='hidden' name='dayOfWeek' value=" . $_GET['dayOfWeek'] . ">";
						}
						$scheduleTable .= "<input type='hidden' name='day' value=" . $sqlData['dayOfWeek'] . ">";
						$scheduleTable .= "<button type='submit' name='addRow' value='delete'>Delete Row</button></form>";
						$scheduleTable .= "<form action='db-schedule.php' method='get'>";
						$scheduleTable .= "<input type='hidden' name='channel' value=" . $channelNumber . ">";
						$scheduleTable .= "<input type='hidden' name='id' value=" . $sqlData['id'] . ">";
						if(isset($_GET['dayOfWeek'])) {
							$scheduleTable .= "<input type='hidden' name='dayOfWeek' value=" . $_GET['dayOfWeek'] . ">";
						}
						$scheduleTable .= "<input type='hidden' name='day' value=" . $sqlData['dayOfWeek'] . ">";
						$scheduleTable .= "<input type='hidden' name='startTime' value=" . $sqlData['startTime'] . ">";
						$scheduleTable .= "<button type='submit' name='addRow' value='addBelow'>Add Below</button></td></tr></form>";
					}
				}
			}
		}
		$scheduleTable .= "</tbody></table>";
		?>
		<div id="topbar" name="topbar"></div>
		<div class="container main-container">
			<?php echo $channelDropdown; ?>
			<?php echo $daysDropDown; ?>
			<?php echo $scheduleTable; ?>
		</div><!-- /container -->
	</body>
</html>
