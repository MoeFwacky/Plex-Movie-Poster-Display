<?php
$width = "<script type='text/javascript'>document.write(window.innerWidth);</script>";
$height = "<script type='text/javascript'>document.write(window.innerHeight);</script>";
include 'config.php'; //Get variables from config
include 'control.php';
$results = Array();
if (isset($_GET['tv'])) {
	$tv = $_GET['tv'];
	if ($tv != "null" && $tv != NULL) {
	        $plexClientName = $_GET['tv'];
		$urlstring = "tv=" . $_GET['tv'] . "&";
		if ($_GET['tv'] != $configClientName && $_GET['tv'] != "null" && $_GET['tv'] != NULL) {
			$pseudochannel = $pseudochannelTrim . "_" . $_GET['tv'] . "/";
			$pseudochannel = trim($pseudochannel);
		}
	} else {
		$tv = plexClientName;
                $urlstring = "";
	}
} else {
	$urlstring = "";
}

//GET PLEX DATA
$url = "http://" . $plexServer . ":" . $plexport . "/status/sessions?X-Plex-Token=" . $plexToken; #set plex server url
$getxml = file_get_contents($url);
$xml = simplexml_load_string($getxml) or die("feed not loading");

$time_style=NULL;
$top_line=NULL;
$middle_line=NULL;
$bottom_line=NULL;
$dircontents=array();
$nowplaying=array();
$xmldata=array();
$chantable=array();
$ps_boxes=array();

//SET TIME AND DATE
$rightnow = time(); //time
$date = date('H:i'); //also time
//$date = date('H:i',strtotime("23:30"));
$dateunix = strtotime($date);
$day = date('D F d'); //date
$text_color='cyan';
$text_color_alt='cyan';

//CHECK IF PSEUDO CHANNEL IS RUNNING AND ON WHAT CHANNEL
$is_ps_running = "find " . $pseudochannel . " -name running.pid -type f -exec cat {} +";
$ps_channel_id = "find " . $pseudochannel . " -name running.pid -type f";
$pgrep = shell_exec($is_ps_running); //check if pseudo channel is running
$pdir = shell_exec($ps_channel_id); //identify directory has the running.pid file
$channel_number = str_replace($pseudochannel . "pseudo-channel_", "", $pdir); //strip the prefix of the directory name to get the channel number
$channel_num = ltrim($channel_number, '0'); //strip the leading zero from single digit channel numbers
$channel_num = str_replace("/running.pid", "", $channel_num); //strip running.pid filename from the variable
$chnum = str_replace("/running.pid", "",$channel_number); //strip running.pid from the variable that keeps the leading zero
$chnum = trim($chnum);

//GET ALL PSEUDO CHANNEL DAILY SCHEDULE XML FILE LOCATIONS
$lsgrep = exec("find ". $pseudochannelMaster . "pseudo-channel_*/schedules | grep xml | tr '\n' ','"); //list the paths of all daily schedule xml files in a comma separated list
$dircontents = explode(",", $lsgrep); //write file locations into an array

//GET ALL PSEUDO CHANNEL DATABASE FILE LOCATIONS
$DBarray = array();
$findDB = exec("find ". $pseudochannelMaster . "pseudo-channel_* -name 'pseudo-channel.db' | tr '\n' ','");
$DBarray = explode(",", $findDB);

// LINE STYLE VARIABLES
if ($DisplayType == 'half') {
	$time_style = "<p class='vcr-time-half'>";
	$top_line = "<p class='vcr-info-half-1'>";
	$middle_line = "<p class='vcr-info-half-2'>";
	$bottom_line = "<p class='vcr-info-half-3'>";
	$side_channel = "<p class='vcr-side-half'>Channel $channel_num</p>";
	$position_half = "<img position: absolute; align: top; width='480' style='opacity:1;'>";
//	$position_half = "<img width='100%' style='opacity:1;position: absolute; align: top; '>";
}

if ($DisplayType == 'full') {
      foreach ($xml->Video as $playdata) {
          if($playdata->Player['title'] == $plexClientName) {
			$video_duration = (int)$playdata['duration'];
			if($playdata['type'] == "movie") {
				if ($video_duration < "1800000") { //COMMERCIAL
				$text_color='cyan';
				$text_color_alt='cyan';
				} else { //MOVIE
				$text_color='yellow';
				$text_color_alt='white';
				}
			} elseif($playdata['type'] == "show" || $playdata['parentTitle'] != "") { //TV SHOW
			$text_color='yellow';
			$text_color_alt='white';
			} else {
			$text_color='cyan';
			$text_color_alt='cyan';
			}
			}
		  }

# SET FULL OPTIONS
	$time_style = "<p class='vcr-time-full-idle' style='color: $text_color;top:60px;'>";
	$top_line = "<p class='vcr-info-full-1-idle' style='color: $text_color; font-size:85px;top:240px;'>";
	$middle_line = "<p class='vcr-info-full-2-idle' style='color:cyan; font-size:55px;top:310px;'>";
	$bottom_line = "<p class='vcr-info-full-3-idle' style='color: cyan; font-size:65px;top:390px;'>";
	$side_channel = "<p class='vcr-side-full' style='color: cyan;font-size:75px;top:5px;'>Channel $channel_num</p>";

	$position_play_full = "<img position: absolute; top: 0; width='100%' style='opacity:1;'>";
	$position_idle_full = "<img position: absolute; top: 0; src='/assets/vcr-play.jpg' width='100%' style='opacity:1;'>";
}

if(strcmp($channel_num," ")<=0){
	$channel_num=0;
}

//If Nothing is Playing
$text_color='cyan';
$text_color_alt='cyan';
if ($DisplayType == 'full') {
	$position=$position_idle_full;
}
if ($DisplayType == 'half') {
	$position=$position_half;
}
if ($pgrep >= 1) { //PSEUDO CHANNEL ON
	$top_section = $time_style . $date . "</p>" . $position;
	$middle_section = $top_line . "Channel $channel_num</p>";
	$bottom_section = $middle_line . "</p>";
	$nowplaying = "Channel $channel_num Standing By...";
} else { //PSEUDO CHANNEL OFF
	$top_section = $time_style . $date . "</p>" . $position;
	$middle_section = $top_line . $day . "</p>";
	$bottom_section = "<p></p>";
	$nowplaying = "Please Stand By...";
}

  if ($xml['size'] != '0') { //IF PLAYING CONTENT
      foreach ($xml->Video as $clients) {
          if($clients->Player['title'] == $plexClientName) { //If the active client on plex matches the client in the config
			    //IF PLAYING COMMERCIAL
				if($clients['type'] == "movie" && $clients['duration'] < 1800000) {
					if ($DisplayType == 'full') {
					$position=$position_idle_full;
					}
					if ($DisplayType == 'half') {
					$position=$position_half;
					}
					$top_section = $time_style . $date . "</p>" . $position;
					$middle_section = $top_line . $clients['librarySectionTitle'] . "</p>";
					$bottom_section = "<p></p>";
					$title_clean = str_replace("_", " ", $clients['title']);
					$nowplaying = "<a href='schedule.php?$urlstring' style='color:white'>Now Playing: <span style='color:red;'>" . $title_clean . "</span> on Channel ". $channel_num . "</a>";
				}
				//IF PLAYING MOVIE
				if($clients['type'] == "movie" && $clients['duration'] >= 1800000) {
					$text_color='yellow';
					$text_color_alt='white';
			        if ($DisplayType == 'half') {
						$art = $clients['thumb'];
						$background_art	= "<img style='position: absolute; align: left; left: 0;' src='http:\/\/$plexServer:$plexport$art?X-Plex-Token=$plexToken' width='130';'>";
						$position=$position_half;
					}
					if ($DisplayType == 'full') {
						$art = $clients['art'];
						$background_art	= "<div style='opacity:.5;width:100%;height:100%;position: absolute; align: left; left: 0;background:url(http:\/\/$plexServer:$plexport$art?X-Plex-Token=$plexToken);background-repeat:no-repeat;background-position: center center;background-size:cover;' src='' width='100%' ></div>";
						$position=$position_play_full;
					}

					$top_section = $background_art . $time_style . $date . $side_channel . "</p>" . $position;
					$middle_section = $top_line . $clients['title'] . $middle_line . $clients['year'] . "</p>";
					$bottom_section = $bottom_line . $clients['tagline'] . "</p>";
					$nowplaying = "<a href='schedule.php?$urlstring' style='color:white'>Now Playing: <span style='color:red;'>" . $clients['title'] . " (" . $clients['year'] . ")" . "</span> on Channel ". $channel_num . "</a>";
				}
				//IF PLAYING TV SHOW
				if($clients['type'] == "show" || $clients['parentTitle'] != "") {
					if ($DisplayType == 'half') {
						$art = $clients['parentThumb'];
						$background_art	= "<img style='position: absolute; align: left; left: 0;' src='http:\/\/$plexServer:$plexport$art?X-Plex-Token=$plexToken' width='130'>";
						$position=$position_half;
					}
					if ($DisplayType == 'full') {
						$art = $clients['grandparentArt'];
						$background_art	= "<div style='opacity:.5;width:100%;height:100%;position: absolute; align: left; left: 0;background:url(http:\/\/$plexServer:$plexport$art?X-Plex-Token=$plexToken);background-repeat:no-repeat;background-position: center center;background-size:cover;' src='' width='100%' ></div>";
						$position=$position_play_full;
						$text_color='yellow';
						$text_color_alt='white';
					}
					$top_section =  $background_art . $time_style . $date . "</p>" . $position;
					$middle_section = $top_line . $clients['grandparentTitle'] . "</p>" . $middle_line . $clients['parentTitle'] . ", Episode " . $clients['index'] . "</p>";
					$bottom_section = $bottom_line . $clients['title'] . "</p>" . $side_channel . "</p>";
					$nowplaying = "<a href='schedule.php?$urlstring' style='color:white'>Now Playing:  <span style='color:red;'>" . $clients['grandparentTitle'] . " • " . $clients['parentTitle'] . ", Episode " . $clients['index'] . " • " . $clients['title'] . "</span> on Channel ". $channel_num . "</a>";
					}
				}
		  }
	  }

//BUILD DAILY SCHEDULE PAGES
$doheader = "0";
$ch_file = "";
$nowtable = "";
$timeData = "";
$results['test'] = "";

foreach ($DBarray as $databasefile) { //do the following for each database file
	if($databasefile) {
		$psDB = new SQLite3($databasefile);
		$ch_file = str_replace($pseudochannelMaster . "pseudo-channel_", "ch", $databasefile); //get channel number
		$ch_file = str_replace("/pseudo-channel.db", "", $ch_file);
		$ch_number = str_replace("ch", "", $ch_file);
		$ch_row = "row" . $ch_number;
		$favicon_local_path = glob('./logos/channel-logo_'.$ch_number.".{jpg,png,gif,ico,svg,jpeg}", GLOB_BRACE);
		$favicon_pseudo_path = glob($pseudochannelMaster . "pseudo-channel_".$ch_number.'/favicon*'.".{jpg,png,gif,ico,svg,jpeg}", GLOB_BRACE);
		$favicon_img_tag = "";

		if (!file_exists('./logos')) {
			mkdir('./logos', 0777, true);
		}

		if(count($favicon_local_path) > 0){
			if(!file_exists($favicon_local_path[0])){
				if(file_exists($favicon_pseudo_path[0])){
					copy($favicon_pseudo_path[0], $favicon_local_path[0]);
				}
			}
		} else {
			if (count($favicon_pseudo_path) > 0){ 
				if(file_exists($favicon_pseudo_path[0])){
					copy($favicon_pseudo_path[0], './logos/channel-logo_'.$ch_number.'.'.pathinfo($favicon_pseudo_path[0])['extension']);
				}
			}
		}
		if (count($favicon_local_path) > 0){
			if(file_exists($favicon_local_path[0])){
				$favicon_img_tag = "<img class='schedule-channel-favicon' src='$favicon_local_path[0]'>";
			}else{
				$favicon_img_tag = "";
			}
		}

		if ($doheader != "1") {
			$currentTime = $dateunix;
			$nowTimeUnix = floor($dateunix / 900) * 900;
			$results['nowtime'] = date("H:i", $nowTimeUnix);
			$timePlus15Unix = floor(($currentTime + 900) / 900) * 900;
			$results['timePlus15'] = date("H:i", $timePlus15Unix);
			$timePlus30Unix = floor(($currentTime + 1800) / 900) * 900;
			$results['timePlus30'] = date("H:i", $timePlus30Unix);
			$timePlus45Unix = floor(($currentTime + 2700) / 900) * 900;
			$results['timePlus45'] = date("H:i", $timePlus45Unix);
			$timePlus60Unix = floor(($currentTime + 3600) / 900) * 900;
			$results['timePlus60'] = date("H:i", $timePlus60Unix);
			$timePlus75Unix = floor(($currentTime + 4500) / 900) * 900;
			$results['timePlus75'] = date("H:i", $timePlus75Unix);
			$timePlus90Unix = floor(($currentTime + 5400) / 900) * 900;
			$results['timePlus90'] = date("H:i", $timePlus90Unix);
			$timePlus105Unix = floor(($currentTime + 6300) / 900) * 900;
			$results['timePlus105'] = date("H:i", $timePlus105Unix);
			$timePlus120Unix = floor(($currentTime + 7200) / 900) * 900;
			$results['timePlus120'] = date("H:i", $timePlus120Unix);
			$timePlus135Unix = floor(($currentTime + 8100) / 900) * 900;
			$results['timePlus135'] = date("H:i", $timePlus135Unix);
			$timePlus150Unix = floor(($currentTime + 9000) / 900) * 900;
			$results['timePlus150'] = date("H:i", $timePlus150Unix);
			$timePlus165Unix = floor(($currentTime + 9900) / 900) * 900;
			$results['timePlus165'] = date("H:i", $timePlus165Unix);
			$timePlus180Unix = floor(($currentTime + 10800) / 900) * 900;
			$results['timePlus180'] = date("H:i", $timePlus180Unix);
			$tableheader = "<table><tr class='schedule-table' width='100%'><th width=4%>&nbsp;Ch.&nbsp;</th><th colspan='2' width=16%>Now</th><th colspan='2' width=16%>timePlus30</th><th colspan='2' with=16%>timePlus60</th><th colspan='2' width=16%>timePlus90</th><th colspan='2' width=16%>timePlus120</th><th colspan='2' width=16%>timePlus150</th></tr><tr>";
			$chantableheader = "<table><tr class='schedule-table'><th colspan='2'>";
			$nowtimecell = "";
			$plus15Data = "";
			$plus30Data = "";
			$plus45Data = "";
			$plus60Data = "";
			$plus75Data = "";
			$plus90Data = "";
			$plus105Data = "";
			$plus120Data = "";
			$plus135Data = "";
			$plus150Data = "";
			$plus165Data = "";
			$plus180Data = "";
			$disappear = "";
			$timeData = "";
			$channelData = "";
			$nowtable = $tableheader;
			$doheader = "1";
		}
		if ($chnum == $ch_number) {
			$channelplaying = "color:#f4ff96";
			$channelPlayingRowClass = "now-playing-highlight-me";
			$channelplayingTitleStyle = "color:#f4ff96";
		} else {
			$channelplaying = "";
			$channelPlayingRowClass = "";
			$channelplayingTitleStyle = "";
		}
		$ch_number_for_html = ($favicon_img_tag == "") ? $ch_number : "";
			$channelData = "<td class='$channelPlayingRowClass'><span class='favicon-container'><a style='$channelplaying' href='schedule.php?" . $urlstring . "action=channel&num=$ch_number'>" . $favicon_img_tag . "<span class='ch_number'>" . $ch_number_for_html . "</a></span></td>";
		$rowContents = $channelData;
			$lastentry = "";
			$spanMax = 12;
			$column = 1;
		if ($DebugMode == "on") {
			$offsetNow = "+0&nbsp;";
			$offset15 = "+15&nbsp;";
			$offset30 = "+30&nbsp;";
			$offset45 = "+45&nbsp;";
			$offset60 = "+60&nbsp;";
			$offset75 = "+75&nbsp;";
			$offset90 = "+90&nbsp;";
			$offset105 = "+105&nbsp;";
			$offset120 = "+120&nbsp;";
			$offset135 = "+135&nbsp;";
			$offset150 = "+150&nbsp;";
			$offset165 = "+165&nbsp;";
		} else {
						$offsetNow = "";
						$offset15 = "";
						$offset30 = "";
						$offset45 = "";
						$offset60 = "";
						$offset75 = "";
						$offset90 = "";
						$offset105 = "";
						$offset120 = "";
						$offset135 = "";
						$offset150 = "";
						$offset165 = "";
		}
		$lastentry = array();
		$sqlData = array();
		$result = $psDB->query("SELECT * FROM daily_schedule WHERE (time(endTime) > time('" . $nowTimeUnix . "','unixepoch','localtime') AND time(startTime) <= time('" . $nowTimeUnix . "','unixepoch','localtime') AND sectionType != 'Commercials') ORDER BY time(startTime) ASC LIMIT 1");
		$sqlNow = $result->fetchArray();
		if($sqlNow) {	
			$sqlData = $sqlNow;
			$colspan = 1;
			$end_time_modified = str_replace("1900-01-01 ", "", $sqlData['endTime']);
			$end_time_modified = explode('.',$end_time_modified);
			$end_time_modified = $end_time_modified[0];
            $start_time_human = strtotime($sqlData['startTime']);
            $end_time_human = strtotime($end_time_modified);
            $spanDuration = $end_time_human - $start_time_human - (time() - $start_time_human);
            $colspan = ceil($spanDuration / 900);
			$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>" . $offsetNow;
			if ($sqlData['sectionType'] == "TV Shows") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em'>";
				$timeData .= $sqlData['showTitle'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>" . $sqlData['title'] . "&nbsp;(S" . $sqlData['seasonNumber'] . "E" . $sqlData['episodeNumber'] . ")";
				$timeData .= "</br>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			} elseif ($sqlData['sectionType'] == "Movies") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>" . $sqlData['title'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			}
			$spanMax = $spanMax - 1;
            $column = $column + 1;
		} else {
			$timeData .= "<td colspan=1></td>";
		}
		$result = $psDB->query("SELECT * FROM daily_schedule WHERE (time(endTime) > time('" . $nowTimeUnix . "','unixepoch','+15 minutes','localtime') AND time(startTime) <= time('" . $nowTimeUnix . "','unixepoch','+15 minutes','localtime') AND sectionType != 'Commercials') ORDER BY time(startTime) ASC LIMIT 1"); 
		$sql15 = $result->fetchArray();
		if($sql15) {
			$sqlData = $sql15;
			if($sqlData != $lastentry) {
			$colspan = 1;
			$end_time_modified = str_replace("1900-01-01 ", "", $sqlData['endTime']);
			$end_time_modified = explode('.',$end_time_modified);
			$end_time_modified = $end_time_modified[0];
			$start_time_human = strtotime($sqlData['startTime']);
			$end_time_human = strtotime($end_time_modified);
			$spanDuration = $end_time_human - $start_time_human;
			$colspan = ceil($spanDuration / 900);
			$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>" . $offset15;
			if ($sqlData['sectionType'] == "TV Shows") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>"; 
				$timeData .= $sqlData['showTitle'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>" . $sqlData['title'] . "&nbsp;(S" . $sqlData['seasonNumber'] . "E" . $sqlData['episodeNumber'] . ")";
				$timeData .= "</br>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			} elseif ($sqlData['sectionType'] == "Movies") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>" . $sqlData['title'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			}
			$spanMax = $spanMax - 1;
            $column = $column + 1;
			}
		} else {
			$timeData .= "<td colspan=1></td>";
		}
		$result = $psDB->query("SELECT * FROM daily_schedule WHERE (time(endTime) > time('" . $nowTimeUnix . "','unixepoch','+30 minutes','localtime') AND time(startTime) <= time('" . $nowTimeUnix . "','unixepoch','+30 minutes','localtime') AND sectionType != 'Commercials') ORDER BY time(startTime) ASC LIMIT 1");
		$sql30 = $result->fetchArray();
		if($sql30) {	
			$sqlData = $sql30;
			if($sqlData != $lastentry) {
			$colspan = 1;
			$end_time_modified = str_replace("1900-01-01 ", "", $sqlData['endTime']);
			$end_time_modified = explode('.',$end_time_modified);
			$end_time_modified = $end_time_modified[0];
			$start_time_human = strtotime($sqlData['startTime']);
			$end_time_human = strtotime($end_time_modified);
			$spanDuration = $end_time_human - $start_time_human;
			$colspan = ceil($spanDuration / 900);
			$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>" . $offset30;
			if ($sqlData['sectionType'] == "TV Shows") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>"; 
				$timeData .= $sqlData['showTitle'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>" . $sqlData['title'] . "&nbsp;(S" . $sqlData['seasonNumber'] . "E" . $sqlData['episodeNumber'] . ")";
				$timeData .= "</br>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			} elseif ($sqlData['sectionType'] == "Movies") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>" . $sqlData['title'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			}
			$spanMax = $spanMax - 1;
            $column = $column + 1;
			}
		} else {
			$timeData .= "<td colspan=1></td>";		
		}
		$result = $psDB->query("SELECT * FROM daily_schedule WHERE (time(endTime) > time('" . $nowTimeUnix . "','unixepoch','+45 minutes','localtime') AND time(startTime) <= time('" . $nowTimeUnix . "','unixepoch','+45 minutes','localtime') AND sectionType != 'Commercials') ORDER BY time(startTime) ASC LIMIT 1");
		$sql45 = $result->fetchArray();
		if($sql45) {
			$sqlData = $sql45;
			if($sqlData != $lastentry) {
			$colspan = 1;
			$end_time_modified = str_replace("1900-01-01 ", "", $sqlData['endTime']);
			$end_time_modified = explode('.',$end_time_modified);
			$end_time_modified = $end_time_modified[0];
			$start_time_human = strtotime($sqlData['startTime']);
			$end_time_human = strtotime($end_time_modified);
			$spanDuration = $end_time_human - $start_time_human;
			$colspan = ceil($spanDuration / 900);
			$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>" . $offset45;
			if ($sqlData['sectionType'] == "TV Shows") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>"; 
				$timeData .= $sqlData['showTitle'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>" . $sqlData['title'] . "&nbsp;(S" . $sqlData['seasonNumber'] . "E" . $sqlData['episodeNumber'] . ")";
				$timeData .= "</br>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			} elseif ($sqlData['sectionType'] == "Movies") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>" . $sqlData['title'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			}
			$spanMax = $spanMax - 1;
            $column = $column + 1;
			}
		} else {
			$timeData .= "<td colspan=1></td>";		
		}
		$result = $psDB->query("SELECT * FROM daily_schedule WHERE (time(endTime) > time('" . $nowTimeUnix . "','unixepoch','+60 minutes','localtime') AND time(startTime) <= time('" . $nowTimeUnix . "','unixepoch','+60 minutes','localtime') AND sectionType != 'Commercials') ORDER BY time(startTime) ASC LIMIT 1");
		$sql60 = $result->fetchArray();
		if($sql60) {
			$sqlData = $sql60;
			if($sqlData != $lastentry) {
			$colspan = 1;
			$end_time_modified = str_replace("1900-01-01 ", "", $sqlData['endTime']);
			$end_time_modified = explode('.',$end_time_modified);
			$end_time_modified = $end_time_modified[0];
			$start_time_human = strtotime($sqlData['startTime']);
			$end_time_human = strtotime($end_time_modified);
			$spanDuration = $end_time_human - $start_time_human;
			$colspan = ceil($spanDuration / 900);
			$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>" . $offset60;
			if ($sqlData['sectionType'] == "TV Shows") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>"; 
				$timeData .= $sqlData['showTitle'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>" . $sqlData['title'] . "&nbsp;(S" . $sqlData['seasonNumber'] . "E" . $sqlData['episodeNumber'] . ")";
				$timeData .= "</br>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			} elseif ($sqlData['sectionType'] == "Movies") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>" . $sqlData['title'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			}
			$spanMax = $spanMax - 1;
            $column = $column + 1;
			}
		} else {
			$timeData .= "<td colspan=1></td>";		
		}
		$result = $psDB->query("SELECT * FROM daily_schedule WHERE (time(endTime) > time('" . $nowTimeUnix . "','unixepoch','+75 minutes','localtime') AND time(startTime) <= time('" . $nowTimeUnix . "','unixepoch','+75 minutes','localtime') AND sectionType != 'Commercials') ORDER BY time(startTime) ASC LIMIT 1");
		$sql75 = $result->fetchArray();
		if($sql75) {
			$sqlData = $sql75;
			if($sqlData != $lastentry) {
			$colspan = 1;
			$end_time_modified = str_replace("1900-01-01 ", "", $sqlData['endTime']);
			$end_time_modified = explode('.',$end_time_modified);
			$end_time_modified = $end_time_modified[0];
			$start_time_human = strtotime($sqlData['startTime']);
			$end_time_human = strtotime($end_time_modified);
			$spanDuration = $end_time_human - $start_time_human;
			$colspan = ceil($spanDuration / 900);
			$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>" . $offset75 . "";
			if ($sqlData['sectionType'] == "TV Shows") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>"; 
				$timeData .= $sqlData['showTitle'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>" . $sqlData['title'] . "&nbsp;(S" . $sqlData['seasonNumber'] . "E" . $sqlData['episodeNumber'] . ")";
				$timeData .= "</br>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			} elseif ($sqlData['sectionType'] == "Movies") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>" . $sqlData['title'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			}
			$spanMax = $spanMax - 1;
            $column = $column + 1;
			}
		} else {
			$timeData .= "<td colspan=1></td>";	
		}
		$result = $psDB->query("SELECT * FROM daily_schedule WHERE (time(endTime) > time('" . $nowTimeUnix . "','unixepoch','+90 minutes','localtime') AND time(startTime) <= time('" . $nowTimeUnix . "','unixepoch','+90 minutes','localtime') AND sectionType != 'Commercials') ORDER BY time(startTime) ASC LIMIT 1");
		$sql90 = $result->fetchArray();
		if($sql90) {
			$sqlData = $sql90;
			if($sqlData != $lastentry) {
			$colspan = 1;
			$end_time_modified = str_replace("1900-01-01 ", "", $sqlData['endTime']);
			$end_time_modified = explode('.',$end_time_modified);
			$end_time_modified = $end_time_modified[0];
			$start_time_human = strtotime($sqlData['startTime']);
			$end_time_human = strtotime($end_time_modified);
			$spanDuration = $end_time_human - $start_time_human;
			$colspan = ceil($spanDuration / 900);
			$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>" . $offset90;
			if ($sqlData['sectionType'] == "TV Shows") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>"; 
				$timeData .= $sqlData['showTitle'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>" . $sqlData['title'] . "&nbsp;(S" . $sqlData['seasonNumber'] . "E" . $sqlData['episodeNumber'] . ")";
				$timeData .= "</br>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			} elseif ($sqlData['sectionType'] == "Movies") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>" . $sqlData['title'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			}
			$spanMax = $spanMax - 1;
            $column = $column + 1;
			}
		} else {
			$timeData .= "<td colspan=1></td>";		
		}
		$result = $psDB->query("SELECT * FROM daily_schedule WHERE (time(endTime) > time('" . $nowTimeUnix . "','unixepoch','+105 minutes','localtime') AND time(startTime) <= time('" . $nowTimeUnix . "','unixepoch','+105 minutes','localtime') AND sectionType != 'Commercials') ORDER BY time(startTime) ASC LIMIT 1");
		$sql105 = $result->fetchArray();
		if($sql105) {
			$sqlData = $sql105;
			if($sqlData != $lastentry) {
			$colspan = 1;
			$end_time_modified = str_replace("1900-01-01 ", "", $sqlData['endTime']);
			$end_time_modified = explode('.',$end_time_modified);
			$end_time_modified = $end_time_modified[0];
			$start_time_human = strtotime($sqlData['startTime']);
			$end_time_human = strtotime($end_time_modified);
			$spanDuration = $end_time_human - $start_time_human;
			$colspan = ceil($spanDuration / 900);
			$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>" . $offset105;
			if ($sqlData['sectionType'] == "TV Shows") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>"; 
				$timeData .= $sqlData['showTitle'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>" . $sqlData['title'] . "&nbsp;(S" . $sqlData['seasonNumber'] . "E" . $sqlData['episodeNumber'] . ")";
				$timeData .= "</br>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			} elseif ($sqlData['sectionType'] == "Movies") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>" . $sqlData['title'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			}
			$spanMax = $spanMax - 1;
            $column = $column + 1;
			}
		} else {
			$timeData .= "<td colspan=1></td>";		
		}
		$result = $psDB->query("SELECT * FROM daily_schedule WHERE (time(endTime) > time('" . $nowTimeUnix . "','unixepoch','+120 minutes','localtime') AND time(startTime) <= time('" . $nowTimeUnix . "','unixepoch','+120 minutes','localtime') AND sectionType != 'Commercials') ORDER BY time(startTime) ASC LIMIT 1");
		$sql120 = $result->fetchArray();
		if($sql120) {
			$sqlData = $sql120;
			if($sqlData != $lastentry) {
			$colspan = 1;
			$end_time_modified = str_replace("1900-01-01 ", "", $sqlData['endTime']);
			$end_time_modified = explode('.',$end_time_modified);
			$end_time_modified = $end_time_modified[0];
			$start_time_human = strtotime($sqlData['startTime']);
			$end_time_human = strtotime($end_time_modified);
			$spanDuration = $end_time_human - $start_time_human;
			$colspan = ceil($spanDuration / 900);
			$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>" . $offset120;
			if ($sqlData['sectionType'] == "TV Shows") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>"; 
				$timeData .= $sqlData['showTitle'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>" . $sqlData['title'] . "&nbsp;(S" . $sqlData['seasonNumber'] . "E" . $sqlData['episodeNumber'] . ")";
				$timeData .= "</br>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			} elseif ($sqlData['sectionType'] == "Movies") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>" . $sqlData['title'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			}
			$spanMax = $spanMax - 1;
            $column = $column + 1;
			}
		} else {
			$timeData .= "<td colspan=1></td>";		
		}
		$result = $psDB->query("SELECT * FROM daily_schedule WHERE (time(endTime) > time('" . $nowTimeUnix . "','unixepoch','+135 minutes','localtime') AND time(startTime) <= time('" . $nowTimeUnix . "','unixepoch','+135 minutes','localtime') AND sectionType != 'Commercials') ORDER BY time(startTime) ASC LIMIT 1");
		$sql135 = $result->fetchArray();
		if($sql135) {
			$sqlData = $sql135;
			if($sqlData != $lastentry) {
			$colspan = 1;
			$end_time_modified = str_replace("1900-01-01 ", "", $sqlData['endTime']);
			$end_time_modified = explode('.',$end_time_modified);
			$end_time_modified = $end_time_modified[0];
			$start_time_human = strtotime($sqlData['startTime']);
			$end_time_human = strtotime($end_time_modified);
			$spanDuration = $end_time_human - $start_time_human;
			$colspan = ceil($spanDuration / 900);
			$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>" . $offset135;
			if ($sqlData['sectionType'] == "TV Shows") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>"; 
				$timeData .= $sqlData['showTitle'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>" . $sqlData['title'] . "&nbsp;(S" . $sqlData['seasonNumber'] . "E" . $sqlData['episodeNumber'] . ")";
				$timeData .= "</br>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			} elseif ($sqlData['sectionType'] == "Movies") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>" . $sqlData['title'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			}
			$spanMax = $spanMax - 1;
            $column = $column + 1;
			}
		} else {
			$timeData .= "<td colspan=1></td>";	
		}
		$result = $psDB->query("SELECT * FROM daily_schedule WHERE (time(endTime) > time('" . $nowTimeUnix . "','unixepoch','+150 minutes','localtime') AND time(startTime) <= time('" . $nowTimeUnix . "','unixepoch','+150 minutes','localtime') AND sectionType != 'Commercials') ORDER BY time(startTime) ASC LIMIT 1");
		$sql150 = $result->fetchArray();
		if($sql150) {
			$sqlData = $sql150;
			if($sqlData != $lastentry) {
			$colspan = 1;
			$end_time_modified = str_replace("1900-01-01 ", "", $sqlData['endTime']);
			$end_time_modified = explode('.',$end_time_modified);
			$end_time_modified = $end_time_modified[0];
			$start_time_human = strtotime($sqlData['startTime']);
			$end_time_human = strtotime($end_time_modified);
			$spanDuration = $end_time_human - $start_time_human;
			$colspan = ceil($spanDuration / 900);
			$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>" . $offset150;
			if ($sqlData['sectionType'] == "TV Shows") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>"; 
				$timeData .= $sqlData['showTitle'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>" . $sqlData['title'] . "&nbsp;(S" . $sqlData['seasonNumber'] . "E" . $sqlData['episodeNumber'] . ")";
				$timeData .= "</br>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			} elseif ($sqlData['sectionType'] == "Movies") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>" . $sqlData['title'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			}
			$spanMax = $spanMax - 1;
            $column = $column + 1;
			}
		} else {
			$timeData .= "<td colspan=1></td>";	
		}
		$result = $psDB->query("SELECT * FROM daily_schedule WHERE (time(endTime) > time('" . $nowTimeUnix . "','unixepoch','+165 minutes','localtime') AND time(startTime) <= time('" . $nowTimeUnix . "','unixepoch','+165 minutes','localtime') AND sectionType != 'Commercials') ORDER BY time(startTime) ASC LIMIT 1");
		$sql165 = $result->fetchArray();
		if($sql165) {
			$sqlData = $sql165;
			if($sqlData != $lastentry) {
			$colspan = 1;
			$end_time_modified = str_replace("1900-01-01 ", "", $sqlData['endTime']);
			$end_time_modified = explode('.',$end_time_modified);
			$end_time_modified = $end_time_modified[0];
			$start_time_human = strtotime($sqlData['startTime']);
			$end_time_human = strtotime($end_time_modified);
			$spanDuration = $end_time_human - $start_time_human;
			$colspan = ceil($spanDuration / 900);
			$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>" . $offset165;
			if ($sqlData['sectionType'] == "TV Shows") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>"; 
				$timeData .= $sqlData['showTitle'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>" . $sqlData['title'] . "&nbsp;(S" . $sqlData['seasonNumber'] . "E" . $sqlData['episodeNumber'] . ")";
				$timeData .= "</br>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			} elseif ($sqlData['sectionType'] == "Movies") {
				$timeData .= "<span class='schedule-title' style='$channelplayingTitleStyle;font-size:1.2em';>" . $sqlData['title'] . "</span>";
				$timeData .= "</br><span class='schedule-subtitle' style='font-size:1em';>(" . date('H:i',$start_time_human) . " - " . date('H:i',$end_time_human) . ")</span></td>";
				$lastentry = $sqlData;
			}
			$spanMax = $spanMax - 1;
            $column = $column + 1;
			}
		} else {
			$timeData .= "<td colspan=1></td>";	
		}
	}
	$rowContents = $channelData . $timeData;
	$results["$ch_row"] = "$rowContents";
	$timeData = "";
}

$nowtable .= "</table>"; 
if (isset($results[$ch_file])) {
	$results[$ch_file] .= "</table>";
}
//$results['nowplaying'] = "$nowplaying";
$results['row'] = "";
echo json_encode($results);
?>
