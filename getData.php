<?php
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
	$time_style = "<p class='vcr-time-full-idle' style='color: $text_color'>";
	$top_line = "<p class='vcr-info-full-1-idle' style=color: $text_color'>";
	$middle_line = "<p class='vcr-info-full-2-idle' style=color: $text_color_alt'>";
	$bottom_line = "<p class='vcr-info-full-3-idle' style=color: $text_color'>";
	$side_channel = "<p class='vcr-side-full' style='color: $text_color_alt'>Channel $channel_num</p>";

	$position_play_full = "<img position: absolute; top: 20px; width='480' style='opacity:1;'>";
	$position_idle_full = "<img position: absolute; top: 0; src='/assets/vcr-play.jpg' width='480' style='opacity:1;'>";
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
					$nowplaying = "<a href='schedule.php?$urlstring' style='color:white'>Now Playing: " . $title_clean . " on Channel ". $channel_num . "</a>";
				}
				//IF PLAYING MOVIE
				if($clients['type'] == "movie" && $clients['duration'] >= 1800000) {
					$text_color='yellow';
					$text_color_alt='white';
			        if ($DisplayType == 'half') {
						$art = $clients['thumb'];
						$background_art	= "<img position: fixed; margin-top: 10; top: 10px; src='http:\/\/$plexServer:$plexport$art' width='130';'>";
						$position=$position_half;
					}
					if ($DisplayType == 'full') {
						$art = $clients['art'];
						$background_art	= "<img position: fixed; align: left; left: -100; top: 10px; margin-top: 10; src='http:\/\/$plexServer:$plexport$art'; width='480';>";
						$position=$position_play_full;
					}

					$top_section = $background_art . $time_style . $date . $side_channel . "</p>" . $position;
					$middle_section = $top_line . $clients['title'] . $middle_line . $clients['year'] . "</p>";
					$bottom_section = $bottom_line . $clients['tagline'] . "</p>";
					$nowplaying = "<a href='schedule.php?$urlstring' style='color:white'>Now Playing: " . $clients['title'] . " (" . $clients['year'] . ")" . " on Channel ". $channel_num . "</a>";
				}
				//IF PLAYING TV SHOW
				if($clients['type'] == "show" || $clients['parentTitle'] != "") {
					if ($DisplayType == 'half') {
						$art = $clients['parentThumb'];
						$background_art	= "<img position: fixed; align: left; left: -100; top: 10px; margin-top: 10; src='http:\/\/$plexServer:$plexport$art'; width='130';>";
						$position=$position_half;
					}
					if ($DisplayType == 'full') {
						$art = $clients['grandparentArt'];
						$background_art	= "<img position: fixed; align: left; left: -100; top: 10px; margin-top: 10; src='http:\/\/$plexServer:$plexport$art'; width='480';>";
						$position=$position_play_full;
						$text_color='yellow';
						$text_color_alt='white';
					}
					$top_section =  $background_art . $time_style . $date . "</p>" . $position;
					$middle_section = $top_line . $clients['grandparentTitle'] . "</p>" . $middle_line . $clients['parentTitle'] . ", Episode " . $clients['index'] . "</p>";
					$bottom_section = $bottom_line . $clients['title'] . "</p>" . $side_channel . "</p>";
					$nowplaying = "<a href='schedule.php?$urlstring' style='color:white'>Now Playing: " . $clients['grandparentTitle'] . " • " . $clients['parentTitle'] . ", Episode " . $clients['index'] . " • " . $clients['title'] . " on Channel ". $channel_num . "</a>";
					}
				}
		  }
	  }

//BUILD DAILY SCHEDULE PAGES
$doheader = "0";
foreach ($dircontents as $xmlfile) { //do the following for each xml schedule file
	$xmldata = simplexml_load_file($xmlfile); //load the xml schedule file
	foreach($xmldata->time as $attributes) { //for each entry in the schedule, do the following
		$start_time_unix = strtotime($attributes['time-start']); //get the entry start time
	    	$start_time_human = date("H:i", $start_time_unix); //convert start time to readable format
		$duration_seconds = $attributes['duration']/1000; //get entry duration and convert to seconds
		$duration_seconds = $duration_seconds-1;
		$end_time_unix = $start_time_unix + $duration_seconds; //using start time and duration, calculate the end time
		$end_time_human = date("H:i", $end_time_unix); //end time in readable format
		$ch_file = str_replace($pseudochannelMaster . "pseudo-channel_", "ch", $xmlfile); //get channel number
		$ch_file = str_replace("/schedules/pseudo_schedule.xml", "", $ch_file);
		$ch_number = str_replace("ch", "", $ch_file);
		if ($doheader != "1") {
			$tableheader = "<table class='schedule-table'><tr><th>&nbsp;Channel&nbsp;</th><th>Time</th><th>Title</th></tr>";
			$chantableheader = "<table class='schedule-table'><tr><th colspan='2'>";
			$nowtable = $tableheader;
			$doheader = "1";
		}
		if ($chnum == $ch_number) {
			$channelplaying = "font-weight:bold;font-size:1.1em";
		} else {
			$channelplaying = "";
		}
		if ($rightnow >= $start_time_unix && $rightnow <= $end_time_unix) {
			$nowtable .= "<tr><td><a style='$channelplaying;display:block; width:100%' href='schedule.php?" . $urlstring . "ch=$ch_number'>" . $ch_number . "</a></td>";
			$nowtable .= "<td style='$channelplaying'>" . $start_time_human . " - " . $end_time_human . " </td>";
			$nowtable .= "<td style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?" . $urlstring . "action=channel&num=$ch_number'>&nbsp";
			if ($attributes['type'] == "TV Shows") {
				$nowtable .= $attributes['show-title'];
				$nowtable .= "</br>&nbsp;S" . $attributes['show-season'] . "E" . $attributes['show-episode'] . " - " . $attributes['title'] . "</td>";
			} elseif ($attributes['type'] == "Commercials") {
				$nowtable .= $attributes['type'] . "</td>";
			} else {
				$nowtable .= $attributes['title'] . "</a></td>";
			}
		}
		if ($results[$ch_file] == "") {
			$results[$ch_file] = $chantableheader . "<a href='schedule.php?" . $urlstring . "action=channel&num=$ch_number'>Channel " . $ch_number . "</a></th></tr><th>Time</th><th>Title</th></tr></tr>";
		}
		if ($rightnow >= $start_time_unix && $rightnow < $end_time_unix) {
			$isnowplaying = "font-weight:bold;font-size:1.2em";
		} else {
			$isnowplaying = "";
		}
		if ($attributes['type'] != "Commercials") {
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
$nowtable .= "</table>";
$results[$ch_file] .= "</table>";
$results['rightnow'] = $nowtable;
$results['top'] = "$top_section";
$results['middle'] = "$middle_section $bottom_section";
$results['bottom'] = "<p></p>";
$results['nowplaying'] = "$nowplaying";
echo json_encode($results);
?>
