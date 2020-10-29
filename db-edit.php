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
			topbar = "topbar.php";
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
	</head>
	<body>
		<?php
		$dircontents=array();
		$ch_number = $_GET['channelNumber'];
		$databasefile = $pseudochannelMaster . "pseudo-channel_" . $ch_number . "/pseudo-channel.db";
		$entryID = $_GET['id'];
		$psDB = new SQLite3($databasefile);
		$ch_file = "ch" . $ch_number;
		$ch_row = "row" . $ch_number;
		$sqlData = array();
	if ($_GET['saveChanges'] == "save") {
		if ($_GET['librarytype'] == "Movies") {
			$setMediaID = "1";
			$libraryType = "Movies";
		} elseif ($_GET['librarytype'] == "TV Shows" && $_GET['title'] != "random") {
			$setMediaID = "2";
			$libraryType = "TV Shows";
		} elseif ($_GET['librarytype'] == "TV Shows" && $_GET['title'] == "random") {
			$setMediaID = "999";
			$libraryType = "TV Shows";
		} elseif ($_GET['librarytype'] == "random") {
			$setMediaID = "9999";
			$libraryType = "TV Shows";
		}
		if ($_GET['durationMax'] == "999") {
			$setDurationMax = "43200000";
		} else {
			$setDurationMax = $_GET['durationMax'];
		}
		$setDuration = $_GET['durationMin'] . "," . $setDurationMax;
		$setStartTimeUnix = strtotime($_GET['setTime']);
		$setStartTime = strftime("%H:%M:%S",$setStartTimeUnix);
		if ($_GET['timeMode'] == "Strict Start Time") {
			$setStrictTime = "true";
		} elseif ($_GET['timeMode'] == "Variable Start Time") {
			$setStrictTime = "false";
		} elseif ($_GET['timeMode'] == "Allow Preempting") {
			$setStrictTime = "secondary";
		} else {
			$setStrictTime = "secondary";
		}
		$saveData = "UPDATE schedule SET unix=:unixTime,mediaID=:mediaID,title=:titleEntry,duration=:setDuration,startTime=:setStartTime,endTime=0,dayOfWeek=:dayofweek,startTimeUnix=:setStartTimeUnix,section=:section,strictTime=:setStrictTime,timeShift=:timeShift,overlapMax=:maxOverlap,xtra=:xtra WHERE id LIKE :entryID";
		$statement = $psDB->prepare($saveData);
		$statement->bindParam(":unixTime", $_GET['unixTime']);
		$statement->bindParam(":mediaID", $setMediaID);
		$statement->bindParam(":titleEntry", $_GET['titleEntry']);
		$statement->bindParam(":setDuration", $setDuration);
		$statement->bindParam(":setStartTime", $setStartTime);
		$statement->bindParam(":dayofweek", $_GET['dayofweek']);
		$statement->bindParam(":setStartTimeUnix", $setStartTimeUnix);
		$statement->bindParam(":section", $libraryType);
		$statement->bindParam(":setStrictTime", $setStrictTime);
		$statement->bindParam(":timeShift", $_GET['timeShift']);
		$statement->bindParam(":maxOverlap", $_GET['maxOverlap']);
		$statement->bindParam(":xtra", $_GET['xtraArgs']);
		$statement->bindParam(":entryID", $entryID);
		$results = $statement->execute();
		if($results==FALSE && $_GET['saveChanges'] == "save") {
			$echoSave = "<span style='color:yellow'>ERROR: $statement->lastErrorMsg()</span>";
		} elseif ($_GET['saveChanges'] == "save") {
			$echoSave = "<span style='color:yellow'>Changes Saved</span>";
		}
	}

		$result = $psDB->query("SELECT * FROM schedule WHERE id LIKE " . $entryID); //get data on selected entry
		$sqlData = $result->fetchArray();

	        $moviesData = array();
		$allMovies = array();
        	$moviesResult = $psDB->query("SELECT title FROM movies"); //get all movie titles
		while ($moviesData = $moviesResult->fetchArray(SQLITE3_ASSOC)) {
			array_push($allMovies, $moviesData['title']);
		}
		$movieDropDown = "";
		foreach ($allMovies as $oneMovie) {
			if ($sqlData['title'] == $oneMovie) {
				$movieDropDown .= "<option value='" . $oneMovie . "' selected>" . $oneMovie . "</option>";
			} else {
				$movieDropDown .= "<option value='" . $oneMovie . "'>" . $oneMovie . "</option>";
			}
		}

		if ($sqlData['title'] == "random") {
			$randomTitleSelected = "selected";
		} else {
			$randomTitleSelected = "";
		}

                $showData = array();
		$allShows = array();
                $showsResult = $psDB->query("SELECT title FROM shows ORDER BY id"); //get all show titles
		while ($showData = $showsResult->fetchArray(SQLITE3_ASSOC)) {
			array_push($allShows, $showData['title']);
		}

		$showDropDown = "";
		foreach ($allShows as $oneShow) {
			if ($sqlData['title'] == $oneShow) {
				$showDropDown .= "<option value='" . $oneShow . "' selected>" . $oneShow . "</option>";
			} else {
				$showDropDown .= "<option value='" . $oneShow . "'>" . $oneShow . "</option>";
			}
		}

		$startTime = strtotime($sqlData['startTime']);
		if ($sqlData['section'] == "TV Shows" && $sqlData['title'] == "random") {
			$dataTitle = "Random TV Show";
		} elseif ($sqlData['section'] == "Movies" && $sqlData['title'] == "random") {
			$dataTitle = "Random Movie";
		} else {
			$dataTitle = $sqlData['title'];
		}
		if ($sqlData['duration'] == "0,43200000") {
			$dataDuration = "N/A";
			$dataDurationMin = "0";
			$dataDurationMax = "999";
		} else {
			$dataDurationExplode = explode(",", $sqlData['duration']);
			$dataDuration = $dataDurationExplode[0] . " - " . $dataDurationExplode[1] . " minutes";
			$dataDurationMin = $dataDurationExplode[0];
			$dataDurationMax = $dataDurationExplode[1];
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
		if ($sqlData['xtra'] != "") {
			$xtraData = str_replace(";","</br>",$sqlData['xtra']);
			$xtraData = str_replace(":",": ",$xtraData);
			$xtraData = ucwords($xtraData,">");
			$xtraData = ucwords($xtraData," ");
			$xtraData = ucwords($xtraData,"-");
		} else {
			$xtraData = "";
		}
		if ($sqlData['section'] == "TV Shows") {
			$selectShows = " selected";
			$selectMovies = "";
			$selectRandom = "";
		} elseif ($sqlData['section'] == "Movies") {
                        $selectShows = "";
                        $selectMovies = " selected";
                        $selectRandom = "";
                } elseif ($sqlData['section'] == "random") {
                        $selectShows = "";
                        $selectMovies = "";
                        $selectRandom = " selected";
                } else {
                        $selectShows = "";
                        $selectMovies = "";
                        $selectRandom = "";
		}
                $daysDropDown = "";
		$allDays = array(everyday,weekdays,weekends,mondays,tuesdays,wednesdays,thursdays,fridays,saturdays,sundays);
                foreach ($allDays as $theDay) {
                        if ($sqlData['dayOfWeek'] == $theDay) {
                                $daysDropDown .= "<option value='" . $theDay . "' selected>" . ucfirst($theDay) . "</option>";
                        } else {
                                $daysDropDown .= "<option value='" . $theDay . "'>" . ucfirst($theDay) . "</option>";
                        }
		}
		$timeModeDropDown = "";
		$allTimeModes = array("Strict Start Time","Variable Start Time","Allow Preempting");
		foreach ($allTimeModes as $theTimeMode) {
			if ($timeMode == $theTimeMode) {
				$timeModeDropDown .= "<option value='" . $theTimeMode . "' selected>" . $theTimeMode . "</option>";
			} else {
				$timeModeDropDown .= "<option value='" . $theTimeMode . "' >" . $theTimeMode . "</option>";
			}
		}
		?>
     		<div id="topbar" name="topbar"></div>
		<!-- show data from entry -->
		<div class="container" style="margin-top:80px;color:white;text-align:left;margin-left:.5em" name="entryinfo">
			<h2 style='font-size:1.8em;line-height:.5em'>Entry Details</h2>
			<span style='font-size:0.7em'>Last Modified: <?php echo strftime('%Y-%m-%d %H:%M:%S %Z',$sqlData['unix']); ?></span>
			<p style='text-align:left;font-size:.8em'>
			<span>Channel: <b><?php echo $ch_number; ?></b></br>ID: <b><?php echo $sqlData['id']; ?></b></span>
			<span></br>Library Type: <b><?php echo $sqlData['section']; ?></b></span>
			<span></br>Title: <b><?php echo $dataTitle; ?></b></span>
			<span style='line-height:50%'></br><b><?php echo ucfirst($sqlData['dayOfWeek']); ?></b> at <b><?php echo strftime("%H:%M", $startTime); ?></b></span>
			<span style='color:white'></br>Duration: <b><?php echo $dataDuration; ?></b></span>
			<span style='color:white'></br>Time Mode: <b><?php echo $timeMode; ?></b></span>
			<span style='color:white'></br>Time Shift: <b><?php echo $sqlData['timeShift']; ?> Minute Increments</b></span>
			<span style='color:white'></br>Maximum Preempting: <b><?php echo $sqlData['overlapMax']; ?> Minutes</b></span>
			<span style='color:white'></br>Extra Arguments:</br><b><?php echo $xtraData; ?></b></span>
		<div class="container" style="color:white;text-align:left;font-size:1em" name="editform"><form>  <!--  edit form to change data in entry -->
			<input type='hidden' value='<?php echo $_GET['channelNumber'] ?>' name='channelNumber' id='channelNumber'></input>
			<input type='hidden' value='<?php echo $_GET['id'] ?>' name='id' id='id'></input>
			<h2 style='font-size:1.8em;line-height:.5em'>Edit Entry</h2>
			<label for='libraryType'>Library Type: </label><select name='librarytype' id='librarytype'>
			<option value='Movies'<?php echo $selectMovies; ?> >Movie</option>
			<option value='TV Shows'<?php echo $selectShows; ?> >TV Show</option>
			<option value='random'<?php echo $selectRandom; ?> >Random Show Episode</option></select></br>
			<label for='titleEntry'>Title: </label><select name='titleEntry' id='titleEntry'>
			<option value='random'<?php echo $randomTitleSelected; ?> >Random Title</option>
			<?php echo $showDropDown; ?></select></br>
			<label for='dayofweek'>Day(s): </label><select name='dayofweek' id='dayofweek'>
			<?php echo $daysDropDown; ?></select></br>
			<label for='setTime'>Time: </label><input type='time' value='<?php echo $sqlData['startTime'] ?>' name='setTime' id='setTime' required></br>
			<label for='durationMin'>Min Duration (minutes): </label><input type='number' value='<?php echo $dataDurationMin ?>' name='durationMin' id='durationMin' style='width:55px'></input></br>
			<label for='durationMin'>Max Duration (minutes): </label><input type='number' value='<?php echo $dataDurationMax ?>' name='durationMax' id='durationMax' style='width:52px'></input></br>
			<label for='timeMode'>Time Mode: </label><select name='timeMode' id='timeMode'>
			<?php echo $timeModeDropDown; ?></select></br>
			<label for='timeShift'>Time Shift (minutes): </label><input type='number' value='<?php echo $sqlData['timeShift'] ?>' name='timeShift' id='timeShift' style='width:45px'></input></br>
			<label for='maxOverlap'>Max Preempting (minutes): </label><input type='number' value='<?php echo $sqlData['overlapMax'] ?>' name='maxOverlap' id='maxOverlap' style='width:45px'></input></br>
			<label for='xtraArgs'>Extra Arguments:</br><span style='font-size:0.8em'>(separate each category and value with a colon ':' and multiple entries with a semicolon ';')</span></br></label>
			<textarea name='xtraArgs' id='xtraArgs' style='width:500px' value='<?php echo $sqlData['xtra']; ?>'><?php echo $sqlData['xtra']; ?></textarea></br>
			<span style='font-size:.9em'>Example: genre:action;decade:1980;collection:holiday</span></br>
			<input type='hidden' value='<?php echo time(); ?>' name='unixTime' id='unixTime'></input>
			<input type='hidden' value='save' name='saveChanges' id='saveChanges'></input>
			<input type='submit' value='Save Changes'></input></form></p>
			<?php echo $echoSave; ?></br>
			<a style='color:white;text-align:left;font-size:0.7em' href="db-schedule.php?channel=<?php echo $_GET['channelNumber'];?>&dayOfWeek=all">&#8592; Return to Channel Schedule Page</a></br>
		</div>
	</body>
</html>
