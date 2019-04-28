<?php
include 'config.php'; #Get variables from config
$results = Array();

# GET PLEX DATA
$url = "http://" . $plexServer . ":" . $plexport . "/status/sessions?X-Plex-Token=" . $plexToken; #set plex server url
$getxml = file_get_contents($url);
$xml = simplexml_load_string($getxml) or die("feed not loading");

$time_style=NULL;
$top_line=NULL;
$middle_line=NULL;
$bottom_line=NULL;

# SET TIME AND DATE
$date = date('H:i'); #get current time
$day = date('D F d'); #get current date
$text_color='cyan';
$text_color_alt='cyan';

#CHECK IF PSEUDO CHANNEL IS RUNNING AND ON WHAT CHANNEL
$is_ps_running = "find " . $pseudochannel . " -name running.pid -type f -exec cat {} +";
$ps_channel_id = "find " . $pseudochannel . " -name running.pid -type f";
$pgrep = shell_exec($is_ps_running); #check if pseudo channel is running
$pdir = shell_exec($ps_channel_id); #identify which channel is running
$channel_num = str_replace($pseudochannel . "pseudo-channel_", "", $pdir);
$channel_num = ltrim($channel_num, '0');
$channel_num = str_replace("/running.pid", "", $channel_num);

# LINE STYLE VARIABLES
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
          if(strstr($playdata->Player['address'], $plexClient)) {
			$video_duration = (int)$playdata['duration'];
			if($playdata['type'] == "movie") {
				if ($video_duration < "1800000") { #COMMERCIAL
				$text_color='cyan';
				$text_color_alt='cyan';
				} else { #MOVIE
				$text_color='yellow';
				$text_color_alt='white';
				}
			} elseif($playdata['type'] == "show" || $playdata['parentTitle'] != "") { #SHOW
			$text_color='yellow';
			$text_color_alt='white';
			} else {
			$text_color='cyan';
			$text_color_alt='cyan';
			}
			}
		  }

# SET FULL OPTIONS
	$time_style = "<p class='vcr-time-full-idle' style=color:$text_color>";
	$top_line = "<p class='vcr-info-full-1' style=color:$text_color>";
	$middle_line = "<p class='vcr-info-full-2' style=color:$text_color_alt>";
	$bottom_line = "<p class='vcr-info-full-3'>";
	$side_channel = "<p class='vcr-side-full'>Channel $channel_num</p>";

	$position_play_full = "<img position: absolute; top: 20px; width='480' style='opacity:1;'>";
	$position_idle_full = "<img position: absolute; top: 0; src='/assets/vcr-play.jpg' width='480' style='opacity:1;'>";
}

if(strcmp($channel_num," ")<=0){
	$channel_num=0;
}

#If Nothing is Playing
$text_color='cyan';
$text_color_alt='cyan';
if ($DisplayType == 'full') {
	$position=$position_idle_full;
}
if ($DisplayType == 'half') {
	$position=$position_half;
}
if ($pgrep >= 1) { #PSEUDO CHANNEL ON
	$top_section = $time_style . $date . "</p>" . $position;
	$middle_section = $top_line . "Channel $channel_num</p>";
	$bottom_section = $middle_line . "</p>";
	$schedule_header = "Channel $channel_num Standing by...</a>";
} else { #PSEUDO CHANNEL OFF
	$top_section = $time_style . $date . "</p>" . $position;
	$middle_section = $top_line . $day . "</p>";
	$bottom_section = "<p></p>";
	$schedule_header = "<a>Please Stand By...</a>";
}

  if ($xml['size'] != '0') { # IF PLAYING CONTENT
      foreach ($xml->Video as $clients) {
          if(strstr($clients->Player['address'], $plexClient)) {
			    #IF PLAYING COMMERCIAL
				if($clients['type'] == "movie" && $clients['duration'] < 1800000) {
					if ($DisplayType == 'full') {
					$position=$position_idle_full;
					}
					if ($DisplayType == 'half') {
					$position=$position_half;
					}
					$title_clean = str_replace("_", " ", $clients['title']);
					$top_section = $time_style . $date . "</p>" . $position;
					$middle_section = $top_line . $clients['librarySectionTitle'] . "</p>";
					$bottom_section = "<p></p>";
					$schedule_header = "<h2 class='schedule-header'><a href='schedule.php'>Now Playing: " . $title_clean . " (" . $clients['year'] . ")" . " on Channel ". $channel_num . "</a></h2>";
				}
				#IF PLAYING MOVIE
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
					$title_clean = str_replace("_", " ", $clients['title']);
					$top_section = $background_art . $time_style . $date . $side_channel . "</p>" . $position;
					$middle_section = $top_line . $clients['title'] . $middle_line . $clients['year'] . "</p>";
					$bottom_section = $bottom_line . $clients['tagline'] . "</p>";
					$schedule_header = "<h2 class='schedule-header'><a href='schedule.php'>Now Playing: " . $title_clean . " (" . $clients['year'] . ")" . " on Channel ". $channel_num . "</a></h2>";
					
				}
				#IF PLAYING TV SHOW
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
					$schedule_header = "<h2 class='schedule-header'><a href='schedule.php'>Now Playing: " . $clients['grandparentTitle'] . " • " . $clients['parentTitle'] . ", Episode " . $clients['index'] . " • " . $clients['title'] . " on Channel ". $channel_num . "</a></h2>";
					}
				}
		  }
	  }

$doheader = "0";
foreach ($dircontents as $xmlfile) {
	$xmldata = simplexml_load_file($xmlfile);
    	foreach($xmldata->time as $attributes) {
    	$start_time_unix = strtotime($attributes['time-start']);
    	$start_time_human = date("H:i", $start_time_unix);
	$duration_seconds = $attributes['duration']/1000;
	$duration_seconds = $duration_seconds-1;
    	$end_time_unix = $start_time_unix + $duration_seconds;
    	$end_time_human = date("H:i", $end_time_unix);
    	$ch_file = str_replace(".xml", "", $xmlfile);
	$ch_number = str_replace("ch", "", $ch_file);
    	if ($doheader != "1")
    	{
		$tableheader = "<table class='schedule-table'><tr><th>&nbsp;Channel&nbsp;</th><th>Time</th><th>Title</th></tr>";
		$chantableheader = "<table class='schedule-table'><tr><th colspan='2'>";
		$nowtable = $tableheader;
	    	$doheader = "1";
	}
	if ($chnum == $ch_number)
	{
		$channelplaying = "font-weight:bold;font-size:1.1em";
	} else {
		$channelplaying = "";
	}
    	if ($rightnow >= $start_time_unix && $rightnow <= $end_time_unix)
	{
		$nowtable .= "<tr><td><a style='$channelplaying;display:block; width:100%' href='schedule.php?ch=$ch_number'>" . $ch_number . "</a></td>";
		$nowtable .= "<td style='$channelplaying'>" . $start_time_human . " - " . $end_time_human . " </td>";
		$nowtable .= "<td style='$channelplaying;text-align:left'><a style='display:block;width:100%' href='?action=channel&num=$ch_number'>&nbsp";
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
	$results[$ch_file] = $chantableheader . "<a href='schedule.php?action=channel&num=$ch_number'>Channel " . $ch_number . "</a></th></tr><th>Time</th><th>Title</th></tr></tr>";
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

function psControl() {
	if (isset($_GET['action'])) {
		if ($_GET['action'] == "channel") {
		        $controlURL = curl_init("../control.php?action=channel&num=$ch_number");
		} elseif ($_GET['action'] == "stop") {
			$controlURL = curl_init("../control.php?action=stop");
		} elseif ($_GET['action'] == "up") {
			$controlURL = curl_init("../control.php?action=up");
		} elseif ($_GET['action'] == "down") {
			$controlURL = curl_init("../control.php?action=down");
		} else {
			$controlURL = curl_init("schedule.php");
		}
}
}
$nowtable .= "</table>";
$results[$ch_file] .= "</table>";
$results['rightnow'] = $nowtable;
$results['scheduleheader'] = $schedule_header;
$results['top'] = "$top_section";
$results['middle'] = "$middle_section $bottom_section";
$results['bottom'] = "<p></p>";
$results['channel'] = "$channel_number";
$results['chan_page'] = "$channel_page";
echo json_encode($results);
?>
