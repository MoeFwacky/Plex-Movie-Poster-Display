<?php
$width = "<script type='text/javascript'>document.write(window.innerWidth);</script>";
$height = "<script type='text/javascript'>document.write(window.innerHeight);</script>";
include 'config.php'; //Get variables from config
include 'control.php';
$results = Array();
if (isset($_GET['tv'])) {
        $plexClientName = $_GET['tv'];
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
	          			#$text_color='cyan';
					#$text_color_alt='cyan';
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
foreach ($dircontents as $xmlfile) { //do the following for each xml schedule file
	if($xmlfile){
		$xmldata = simplexml_load_file($xmlfile); //load the xml schedule file
	        $timeData = "";
	}
	if($xmldata){
		foreach($xmldata->time as $attributes) { //for each entry in the schedule, do the following
			$start_time_unix = strtotime($attributes['time-start']); //get the entry start time
		    	$start_time_human = date("H:i", $start_time_unix); //convert start time to readable format
			//$duration_seconds = $attributes['duration']/1000; //get entry duration and convert to seconds
			//$duration_seconds = $duration_seconds-1;
                        $end_time_unix = strtotime($attributes['time-end']); //get entry end time
			//$end_time_unix = $start_time_unix + $duration_seconds; //using start time and duration, calculate the end time
			$end_time_human = date("H:i", $end_time_unix); //end time in readable format
                        $duration_seconds = $end_time_unix - $start_time_unix;
			$ch_file = str_replace($pseudochannelMaster . "pseudo-channel_", "ch", $xmlfile); //get channel number
			$ch_file = str_replace("/schedules/pseudo_schedule.xml", "", $ch_file);
			$ch_number = str_replace("ch", "", $ch_file);
		        $ch_row = "row" . $ch_number;
			$favicon_local_path = glob('./logos/channel-logo_'.$ch_number.".{jpg,png,gif,ico,svg,jpeg}", GLOB_BRACE);
			$favicon_pseudo_path = glob($pseudochannelMaster . "pseudo-channel_".$ch_number.'/favicon*'.".{jpg,png,gif,ico,svg,jpeg}", GLOB_BRACE);
			$favicon_img_tag = "";
			//error_log("favicon_local_path", 0);
			//error_log(print_r($favicon_local_path, TRUE)); 
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
				if($ScheduleType == "portrait"){
					$tableheader = "<table class='schedule-table'><tr><th>&nbsp;Channel&nbsp;</th><th>Time</th><th>Title</th></tr>";
					$chantableheader = "<table class='schedule-table'><tr><th colspan='2'>";
				} else {
					$currentTime = time();
					$nowTimeUnix = floor($currentTime / 900) * 900;
					$nowTime = date("H:i", $nowTimeUnix);
					$timePlus15Unix = floor(($currentTime + 900) / 900) * 900;
					$timePlus15 = date("H:i", $timePlus15Unix);
					$timePlus30Unix = floor(($currentTime + 1800) / 900) * 900;
					$timePlus30 = date("H:i", $timePlus30Unix);
					$timePlus45Unix = floor(($currentTime + 2700) / 900) * 900;
					$timePlus45 = date("H:i", $timePlus45Unix);
					$timePlus60Unix = floor(($currentTime + 3600) / 900) * 900;
					$timePlus60 = date("H:i", $timePlus60Unix);
					$timePlus75Unix = floor(($currentTime + 4500) / 900) * 900;
					$timePlus75 = date("H:i", $timePlus75Unix);
					$timePlus90Unix = floor(($currentTime + 5400) / 900) * 900;
					$timePlus90 = date("H:i", $timePlus90Unix);
					$timePlus105Unix = floor(($currentTime + 6300) / 900) * 900;
					$timePlus105 = date("H:i", $timePlus105Unix);
					$timePlus120Unix = floor(($currentTime + 7200) / 900) * 900;
					$timePlus120 = date("H:i", $timePlus120Unix);
					$timePlus135Unix = floor(($currentTime + 8100) / 900) * 900;
					$timePlus135 = date("H:i", $timePlus135Unix);
					$timePlus150Unix = floor(($currentTime + 9000) / 900) * 900;
					$timePlus150 = date("H:i", $timePlus150Unix);
					$timePlus165Unix = floor(($currentTime + 9900) / 900) * 900;
					$timePlus165 = date("H:i", $timePlus165Unix);
					$timePlus180Unix = floor(($currentTime + 10800) / 900) * 900;
					$timePlus180 = date("H:i", $timePlus180Unix);
					$tableheader = "<table class='schedule-table'><tr width='100%'><th width=4%>&nbsp;Ch.&nbsp;</th><th colspan='2' width=16%>$nowTime</th><th colspan='2' width=16%>$timePlus30</th><th colspan='2' with=16%>$timePlus60</th><th colspan='2' width=16%>$timePlus90</th><th colspan='2' width=16%>$timePlus120</th><th colspan='2' width=16%>$timePlus150</th></tr><tr>";
					$chantableheader = "<table class='schedule-table'><tr><th colspan='2'>";
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
				}
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
			}
			if ($nowTimeUnix >= $start_time_unix && $nowTimeUnix <= $end_time_unix || $nowTimeUnix > $start_time_unix && $nowTimeUnix <= $end_time_unix && $start_time_unix < $timePlus15Unix) {
					$theTimeUnix = $nowTimeUnix;
					$timeBetween = $start_time_unix - $theTimeUnix;
					$emptySpan = round($timeBetween / 450);
					if ($emptySpan >= 1) {
					$timeData .= "<td colspan=$emptySpan></td>";
					}
					if ($duration_seconds >= 1000 && $duration_seconds < 1800) {
					$colspan = 2;
					} elseif ($duration_seconds < 1000) {
					$colspan = 1;
					} else {
					$spanLimit = round($duration_seconds / 900);
					$spanArray = array($spanLimit,$spanMax);
					$colspan = min($spanArray);
					}
					if ($start_time_unix < $theTimeUnix) {
					$startbefore = $theTimeUnix - $start_time_unix;
					$startbefore = round($startbefore / 900);
					$colspan = $colspan - $startbefore;
					}
			    $timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>" . $offsetNow . "";
					if ($attributes['type'] == "TV Shows") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['show-title'];
					$timeData .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")";
					$lastentry = $attributes['title'];
					} elseif ($attributes['type'] == "Commercials") {
					$lastentry = "Commercial";
					} elseif ($attributes['type'] == "Movies") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")</a>";
					$lastentry = $attributes['title'];
					}
					$timeData .= "</span></td>";
					$lastEndTimeUnix = $end_time_unix;
					$spanMax = $spanMax - 1;
					$column = $column + 1;
			}
			 
		    //$rowContents .= $timeData;
		    if ($timePlus15Unix >= $start_time_unix && $timePlus15Unix <= $end_time_unix && $start_time_unix < $timePlus30Unix || $timePlus15Unix > $start_time_unix + 899 && $timePlus15Unix <= $end_time_unix && $start_time_unix < $timePlus30Unix) {
			if ($lastentry != $attributes['title']) {
			$theTimeUnix = $timePlus30Unix;
			$timeBetween = $start_time_unix - $theTimeUnix;
			$emptySpan = ceil($timeBetween / 450);
			if ($emptySpan >= 1) {
			    $timeData .= "<td colspan='$emptySpan'></td>";
			}
 			if ($duration_seconds >= 1000 && $duration_seconds < 1800) {
			    $colspan = 2;
			} elseif ($duration_seconds < 1000) {
			    $colspan = 1;
			} else {
			    $spanLimit = ceil($duration_seconds / 900);
			    $spanArray = array($spanLimit,$spanMax);
			    $colspan = min($spanArray);
			}
			if ($start_time_unix < $timePlus15Unix) {
			    $startbefore = $timePlus15Unix - $start_time_unix;
			    $startbefore = round($startbefore / 900);
			    $colspan = $colspan - $startbefore;
			}
			
			$timeData .= "<td colspan='$colspan' class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>$offset15";
			if ($attributes['type'] == "TV Shows") {
			    $timeData .= "<span style='$channelplayingTitleStyle;'>";
			    $timeData .= $attributes['show-title'];
			    $timeData .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")";
			    $lastentry = $attributes['title'];
			} elseif ($attributes['type'] == "Commercials") {
			    $lastentry = "Commercial";
			} elseif ($attributes['type'] == "Movies") {
			    $timeData .= "<span style='$channelplayingTitleStyle;'>";
			    $timeData .= $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")</a>";
			    $lastentry = $attributes['title'];
			}
			$timeData .= "</span></td>";
			$lastEndTimeUnix = $end_time_unix;
			$spanMax = $spanMax - 1;
			$column = $column + 1;
			} else {
			    //$timeData .= "<td></td>";
			}
		    }
		    if ($timePlus30Unix >= $start_time_unix && $timePlus30Unix <= $end_time_unix && $start_time_unix < $timePlus45Unix || $timePlus30Unix > $start_time_unix + 899 && $timePlus30Unix <= $end_time_unix && $start_time_unix < $timePlus45Unix) {
				if ($lastentry != $attributes['title']) {	
			                //$timeData = "";
			                $theTimeUnix = $timePlus30Unix;
					$timeBetween = $start_time_unix - $theTimeUnix;
					$emptySpan = round($timeBetween / 450);
					if ($emptySpan >= 1) {
					$timeData .= "<td colspan=$emptySpan></td>";
					}
					if ($duration_seconds >= 1000 && $duration_seconds < 1800) {
					$colspan = 2;
					} elseif ($duration_seconds < 1000) {
					$colspan = 1;
					} else {
					$spanLimit = ceil($duration_seconds / 900);
					$spanArray = array($spanLimit,$spanMax);
					$colspan = min($spanArray);
					}
					if ($start_time_unix < $timePlus30Unix) {
					$startbefore = $timePlus30Unix - $start_time_unix;
					$startbefore = round($startbefore / 900);
					$colspan = $colspan - $startbefore;
					}
					
					$timeData .= "<td colspan='$colspan' class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>$offset30";
					if ($attributes['type'] == "TV Shows") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['show-title'];
					$timeData .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")";
					$lastentry = $attributes['title'];
					} elseif ($attributes['type'] == "Commercials") {
					$lastentry = "Commercial";
					} elseif ($attributes['type'] == "Movies") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")</a>";
					$lastentry = $attributes['title'];
					}
					$timeData .= "</span></td>";
					$lastEndTimeUnix = $end_time_unix;
					$spanMax = $spanMax - 1;
					$column = $column + 1;
				} else {
                            //$timeData .= "<td></td>";
				}
			}
			if ($timePlus45Unix >= $start_time_unix && $timePlus45Unix <= $end_time_unix && $start_time_unix < $timePlus60Unix || $timePlus45Unix > $start_time_unix + 899 && $timePlus45Unix <= $end_time_unix && $start_time_unix < $timePlus60Unix) {
				if ($lastentry != $attributes['title']) {
			                $theTimeUnix = $timePlus45Unix;
					$timeBetween = $start_time_unix - $theTimeUnix;
					$emptySpan = round($timeBetween / 450);
					if ($emptySpan >= 1) {
					$timeData .= "<td colspan=$emptySpan></td>";
					}
					if ($duration_seconds >= 1000 && $duration_seconds < 1800) {
					$colspan = 2;
					} elseif ($duration_seconds < 1000) {
					$colspan = 1;
					} else {
					$spanLimit = ceil($duration_seconds / 900);
					$spanArray = array($spanLimit,$spanMax);
					$colspan = min($spanArray);
					}
					if ($start_time_unix < $timePlus45Unix) {
					$startbefore = $theTimeUnix - $start_time_unix;
					$startbefore = round($startbefore / 900);
					$colspan = $colspan - $startbefore;
					}
					
					$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>$offset45";
					if ($attributes['type'] == "TV Shows") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['show-title'];
					$timeData .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")";
					$lastentry = $attributes['title'];
					} elseif ($attributes['type'] == "Commercials") {
					$lastentry = "Commercial";
					} elseif ($attributes['type'] == "Movies") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")</a>";
					$lastentry = $attributes['title'];
					}
					$timeData .= "</span></td>";
					$lastEndTimeUnix = $end_time_unix;
					$spanMax = $spanMax - 1;
					$column = $column + 1;
				} else {                                                                                                                                                             
                            //$timeData .= "<td></td>";                                                                                                                                                
                                } 
			} 
			if ($timePlus60Unix >= $start_time_unix && $timePlus60Unix <= $end_time_unix && $start_time_unix < $timePlus75Unix || $timePlus60Unix > $start_time_unix + 899 && $timePlus60Unix <= $end_time_unix && $start_time_unix < $timePlus75Unix) {
				if ($lastentry != $attributes['title']) {	
			                $theTimeUnix = $timePlus60Unix;
					$timeBetween = $start_time_unix - $theTimeUnix;
					$emptySpan = round($timeBetween / 450);
					if ($emptySpan >= 1) {
					$timeData .= "<td colspan=$emptySpan></td>";
					}
					if ($duration_seconds >= 1000 && $duration_seconds < 1800) {
					$colspan = 2;
					} elseif ($duration_seconds < 1000) {
					$colspan = 1;
					} else {
					$spanLimit = ceil($duration_seconds / 900);
					$spanArray = array($spanLimit,$spanMax);
					$colspan = min($spanArray);
					}
					if ($start_time_unix < $timePlus60Unix) {
					$startbefore = $timePlus60Unix - $start_time_unix;
					$startbefore = round($startbefore / 900);
					$colspan = $colspan - $startbefore;
					}
					
					$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>$offset60";
					if ($attributes['type'] == "TV Shows") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['show-title'];
					$timeData .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")";
					$lastentry = $attributes['title'];
					} elseif ($attributes['type'] == "Commercials") {
					$lastentry = "Commercial";
					} elseif ($attributes['type'] == "Movies") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")</a>";
					$lastentry = $attributes['title'];
					}
					$timeData .= "</span></td>";
					$lastEndTimeUnix = $end_time_unix;
					$spanMax = $spanMax - 1;
					$column = $column + 1;
				}
			}
			if ($timePlus75Unix >= $start_time_unix && $timePlus75Unix <= $end_time_unix && $start_time_unix < $timePlus90Unix || $timePlus75Unix > $start_time_unix + 899 && $timePlus75Unix <= $end_time_unix && $start_time_unix < $timePlus90Unix) {
				if ($lastentry != $attributes['title']) {
			                $theTimeUnix = $timePlus75Unix;
					$timeBetween = $start_time_unix - $theTimeUnix;
					$emptySpan = round($timeBetween / 450);
					if ($emptySpan >= 1) {
					$timeData .= "<td colspan=$emptySpan></td>";
					}
					if ($duration_seconds >= 1000 && $duration_seconds < 1800) {
					$colspan = 2;
					} elseif ($duration_seconds < 1000) {
					$colspan = 1;
					} else {
					$spanLimit = ceil($duration_seconds / 900);
					$spanArray = array($spanLimit,$spanMax);
					$colspan = min($spanArray);
					}
					if ($start_time_unix < $timePlus75Unix) {
					$startbefore = $timePlus75Unix - $start_time_unix;
					$startbefore = round($startbefore / 900);
					$colspan = $colspan - $startbefore;
					}
					
					$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>$offset75";
					if ($attributes['type'] == "TV Shows") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['show-title'];
					$timeData .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")";
					$lastentry = $attributes['title'];
					} elseif ($attributes['type'] == "Commercials") {
					$lastentry = "Commercial";
					} elseif ($attributes['type'] == "Movies") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")</a>";
					$lastentry = $attributes['title'];
					}
					$timeData .= "</span></td>";
					$lastEndTimeUnix = $end_time_unix;
					$spanMax = $spanMax - 1;
					$column = $column + 1;
				}
			}
			if ($timePlus90Unix >= $start_time_unix && $timePlus90Unix <= $end_time_unix && $start_time_unix < $timePlus105Unix || $timePlus90Unix > $start_time_unix + 899 && $timePlus90Unix <= $end_time_unix && $start_time_unix < $timePlus90Unix) {
				if ($lastentry != $attributes['title']) {
			                $theTimeUnix = $timePlus90Unix;
					$timeBetween = $start_time_unix - $theTimeUnix;
					$emptySpan = round($timeBetween / 450);
					if ($emptySpan >= 1) {
					$timeData .= "<td colspan=$emptySpan></td>";
					}
					if ($duration_seconds >= 1000 && $duration_seconds < 1800) {
					$colspan = 2;
					} elseif ($duration_seconds < 1000) {
					$colspan = 1;
					} else {
					$spanLimit = ceil($duration_seconds / 900);
					$spanArray = array($spanLimit,$spanMax);
					$colspan = min($spanArray);
					}
					if ($start_time_unix < $timePlus90Unix) {
					$startbefore = $timePlus90Unix - $start_time_unix;
					$startbefore = round($startbefore / 900);
					$colspan = $colspan - $startbefore;
					}
					
					$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>$offset90";
					if ($attributes['type'] == "TV Shows") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['show-title'];
					$timeData .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")";
					$lastentry = $attributes['title'];
					} elseif ($attributes['type'] == "Commercials") {
					$lastentry = "Commercial";
					} elseif ($attributes['type'] == "Movies") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")</a>";
					$lastentry = $attributes['title'];
					}
					$timeData .= "</span></td>";
					$lastEndTimeUnix = $end_time_unix;
					$spanMax = $spanMax - 1;
					$column = $column + 1;
				}
			}
			if ($timePlus105Unix >= $start_time_unix && $timePlus105Unix <= $end_time_unix && $start_time_unix < $timePlus120Unix || $timePlus105Unix > $start_time_unix + 899 && $timePlus105Unix <= $end_time_unix && $start_time_unix < $timePlus105Unix) {
				if ($lastentry != $attributes['title']) {
			                $theTimeUnix = $timePlus105Unix;
					$timeBetween = $start_time_unix - $timePlus105Unix;
					$emptySpan = round($timeBetween / 450);
					if ($emptySpan >= 1) {
					$timeData .= "<td colspan='$emptyspan'></td>";
					}
					if ($duration_seconds >= 1000 && $duration_seconds < 1800) {
					$colspan = 2;
					} elseif ($duration_seconds < 1000) {
					$colspan = 1;
					} else {
					$spanLimit = ceil($duration_seconds / 900);
					$spanArray = array($spanLimit,$spanMax);
					$colspan = min($spanArray);
					}
					if ($start_time_unix < $timePlus105Unix) {
					$startbefore = $timePlus105Unix - $start_time_unix;
					$startbefore = round($startbefore / 900);
					$colspan = $colspan - $startbefore;
					}
					
					$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>$offset105";
					if ($attributes['type'] == "TV Shows") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['show-title'];
					$timeData .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")";
					$lastentry = $attributes['title'];
					} elseif ($attributes['type'] == "Commercials") {
					$lastentry = "Commercial";
					    $timeData = "<td></td>";
					} elseif ($attributes['type'] == "Movies") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")</a>";
					$lastentry = $attributes['title'];
					}
					$timeData .= "</span></td>";
					$lastEndTimeUnix = $end_time_unix;
					$spanMax = $spanMax - 1;
					$column = $column + 1;
				}
			}
			if ($timePlus120Unix >= $start_time_unix && $timePlus120Unix <= $end_time_unix && $start_time_unix < $timePlus135Unix || $timePlus120Unix > $start_time_unix + 899 && $timePlus120Unix <= $end_time_unix && $start_time_unix < $timePlus120Unix) {
				if ($lastentry != $attributes['title']) {
			                $theTimeUnix = $timePlus120Unix;
					$timeBetween = $start_time_unix - $theTimeUnix;
					$emptySpan = round($timeBetween / 450);
					if ($emptySpan >= 1) {
					$timeData .= "<td colspan=$emptySpan></td>";
					}
					if ($duration_seconds >= 1000 && $duration_seconds < 1800) {
					$colspan = 2;
					} elseif ($duration_seconds < 1000) {
					$colspan = 1;
					} else {
					$spanLimit = ceil($duration_seconds / 900);
					$spanArray = array($spanLimit,$spanMax);
					$colspan = min($spanArray);
					}
					if ($start_time_unix < $timePlus120Unix) {
					$startbefore = $timePlus120Unix - $start_time_unix;
					$startbefore = round($startbefore / 900);
					$colspan = $colspan - $startbefore;
					}
					
					$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>$offset120";
					if ($attributes['type'] == "TV Shows") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['show-title'];
					$timeData .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")";
					$lastentry = $attributes['title'];
					} elseif ($attributes['type'] == "Commercials") {
					$lastentry = "Commercial";
					} elseif ($attributes['type'] == "Movies") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")</a>";
					$lastentry = $attributes['title'];
					}
					$timeData .= "</span></td>";
					$lastEndTimeUnix = $end_time_unix;
					$spanMax = $spanMax - 1;
					$column = $column + 1;
				}
			}
			if ($timePlus135Unix >= $start_time_unix && $timePlus135Unix <= $end_time_unix && $start_time_unix < $timePlus150Unix || $timePlus135Unix > $start_time_unix + 899 && $timePlus135Unix <= $end_time_unix && $start_time_unix < $timePlus135Unix) {
				if ($lastentry != $attributes['title']) {	
			                $theTimeUnix = $timePlus135Unix;
					$timeBetween = $start_time_unix - $theTimeUnix;
					$emptySpan = round($timeBetween / 450);
					if ($emptySpan >= 1) {
					$timeData .= "<td colspan=$emptySpan></td>";
					}
					if ($duration_seconds >= 1000 && $duration_seconds < 1800) {
					$colspan = 2;
					} elseif ($duration_seconds < 1000) {
					$colspan = 1;
					} else {
					$spanLimit = ceil($duration_seconds / 900);
					$spanArray = array($spanLimit,$spanMax);
					$colspan = min($spanArray);
					}
					if ($start_time_unix < $timePlus135Unix) {
					$startbefore = $timePlus135Unix - $start_time_unix;
					$startbefore = round($startbefore / 900);
					$colspan = $colspan - $startbefore;
					}
					
					$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>$offset135";
					if ($attributes['type'] == "TV Shows") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['show-title'];
					$timeData .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")";
					$lastentry = $attributes['title'];
					} elseif ($attributes['type'] == "Commercials") {
					$lastentry = "Commercial";
					} elseif ($attributes['type'] == "Movies") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")</a>";
					$lastentry = $attributes['title'];
					}
					$timeData .= "</span></td>";
					$lastEndTimeUnix = $end_time_unix;
					$spanMax = $spanMax - 1;
					$column = $column + 1;
				}
			}
			if ($timePlus150Unix >= $start_time_unix && $timePlus150Unix <= $end_time_unix && $start_time_unix < $timePlus165Unix || $timePlus150Unix > $start_time_unix + 899 && $timePlus150Unix <= $end_time_unix && $start_time_unix < $timePlus150Unix) {
				if ($lastentry != $attributes['title']) {
			                $theTimeUnix = $timePlus150Unix;
					$timeBetween = $start_time_unix - $theTimeUnix;
					$emptySpan = round($timeBetween / 450);
					if ($emptySpan >= 1) {
					$timeData .= "<td colspan=$emptySpan></td>";
					}
					if ($duration_seconds >= 1000 && $duration_seconds < 1800) {
					$colspan = 2;
					} elseif ($duration_seconds < 1000) {
					$colspan = 1;
					} else {
					$spanLimit = ceil($duration_seconds / 900);
					$spanArray = array($spanLimit,$spanMax);
					$colspan = min($spanArray);
					}
					if ($start_time_unix < $timePlus150Unix) {
					$startbefore = $timePlus150Unix - $start_time_unix;
					$startbefore = round($startbefore / 900);
					$colspan = $colspan - $startbefore;
					}
					
					$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>$offset150";
					if ($attributes['type'] == "TV Shows") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['show-title'];
					$timeData .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")";
					$lastentry = $attributes['title'];
					} elseif ($attributes['type'] == "Commercials") {
					$lastentry = "Commercial";
					} elseif ($attributes['type'] == "Movies") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")</a>";
					$lastentry = $attributes['title'];
					}
					$timeData .= "</span></td>";
					$lastEndTimeUnix = $end_time_unix;
					$spanMax = $spanMax - 1;
					$column = $column + 1;
				}
			}
			if ($timePlus165Unix >= $start_time_unix && $timePlus165Unix <= $end_time_unix && $start_time_unix < $timePlus180Unix || $timePlus165Unix > $start_time_unix + 899 && $timePlus165Unix <= $end_time_unix && $start_time_unix < $timePlus165Unix) {
				if ($lastentry != $attributes['title']) {
			                $theTimeUnix = $timePlus165Unix;
					$timeBetween = $start_time_unix - $theTimeUnix;
					$emptySpan = round($timeBetween / 450);
					if ($emptySpan >= 1) {
					$timeData .= "<td colspan=$emptySpan></td>";
					}
					if ($duration_seconds >= 1000 && $duration_seconds < 1800) {
					$colspan = 2;
					} elseif ($duration_seconds < 1000) {
					$colspan = 1;
					} else {
					$spanLimit = ceil($duration_seconds / 900);
					$spanArray = array($spanLimit,$spanMax);
					$colspan = min($spanArray);
					}
					if ($start_time_unix < $timePlus165Unix) {
					$startbefore = $timePlus165Unix - $start_time_unix;
					$startbefore = round($startbefore / 900);
					$colspan = $colspan - $startbefore;
					}
					
					$timeData .= "<td colspan=$colspan class='$channelPlayingRowClass' style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>$offset165";
					if ($attributes['type'] == "TV Shows") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['show-title'];
					$timeData .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")";
					$lastentry = $attributes['title'];
					} elseif ($attributes['type'] == "Commercials") {
					$lastentry = "Commercial";
					} elseif ($attributes['type'] == "Movies") {
					$timeData .= "<span style='$channelplayingTitleStyle;'>";
					$timeData .= $attributes['title'] . "&nbsp(" . $start_time_human . " - " . $end_time_human . ")</a>";
					$lastentry = $attributes['title'];
					}
					$timeData .= "</span></td>";
					$lastEndTimeUnix = $end_time_unix;
					$spanMax = $spanMax - 1;
					$column = $column + 1;
				}
			}			
			$rowContents = $channelData . $timeData;
			$results["$ch_row"] = "$rowContents";
			try {
				if (!isset($results[$ch_file]) || $results[$ch_file] == "") {
					$results[$ch_file] = $chantableheader . "<a href='schedule.php?" . $urlstring . "action=channel&num=$ch_number'>Channel " . $ch_number . "</a></th></tr><th>Time</th><th>Title</th></tr></tr>";
				}
		        
		    } catch (Exception $e) {
		        error_log(print_r($e, TRUE)); 
		    }

			if ($rightnow >= $start_time_unix && $rightnow < $end_time_unix) {
				$isnowplaying = "color:#f4ff96";
			} else {
				$isnowplaying = "";
			}
			if ($attributes['type'] != "Commercials") {
				if (isset($results[$ch_file])) {
					$results[$ch_file] .= "<tr>";
					$results[$ch_file] .= "<td style='$isnowplaying'>" . $start_time_human . " - " . $end_time_human . " </td>";
					$results[$ch_file] .= "<td style='$isnowplaying;text-align:left'>&nbsp;";
					if ($attributes['type'] == "TV Shows") {
						$results[$ch_file] .= $attributes['show-title'];
						$results[$ch_file] .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "</td>";
					} elseif ($attributes['type'] == "Commercials") {
						$results[$ch_file] .= $attributes['type'] . "</td>";
					} else {
						$results[$ch_file] .= $attributes['title'] . "</td>";
					}
				}
			}
		}
		//makes sure empty schedule channels still show
					$channelData = "<td class='$channelPlayingRowClass'><span class='favicon-container'><a style='$channelplaying' href='schedule.php?" . $urlstring . "action=channel&num=$ch_number'>" . $favicon_img_tag . "<span class='ch_number'>" . $ch_number_for_html . "</a></span></td>";
	}
}
$nowtable .= "</table>";
if (isset($results[$ch_file])) {
	$results[$ch_file] .= "</table>";
}
$results['rightnow'] = $nowtable;
$results['top'] = "$top_section";
$results['middle'] = "$middle_section $bottom_section";
$results['bottom'] = "<p></p>";
$results['nowplaying'] = "$nowplaying";
$results['nowTime'] = $nowTime;
$results['timePlus15'] = $timePlus15;
$results['timePlus30'] = $timePlus30;
$results['timePlus45'] = $timePlus45;
$results['timePlus60'] = $timePlus60;
$results['timePlus75'] = $timePlus75;
$results['timePlus90'] = $timePlus90;
$results['timePlus105'] = $timePlus105;
$results['timePlus120'] = $timePlus120;
$results['timePlus135'] = $timePlus135;
$results['timePlus150'] = $timePlus150;
$results['timePlus165'] = $timePlus165;
$results['row'] = "";
echo json_encode($results);
?>
