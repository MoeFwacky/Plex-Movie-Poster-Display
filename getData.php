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
	$time_style = "<p style='font-family: Digital\-7; font-size: 95px; font-style: monospace; text-shadow: 1px 1px 3px cyan, 0 0 25px cyan, 0 0 5px black; color: cyan; line-height: 92%; text-align: left; z-index: 100; top: 0px; position: absolute; width: 350px; left: 140px; margin-left: 0px;'>";
	$top_line = "<p style='font-family: Digital\-7; font-size: 35px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: left; z-index: 100; top: 80px; position: absolute; white-space: nowrap; overflow: hidden; width: 480px; left: 140px; margin-left: 0px;'>";
	$middle_line = "<p style='font-family: Digital\-7; font-size: 35px; text-align: left; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; z-index: 100; top: 100px; color: cyan; left: 140px; position: absolute; width: 350px; margin-left: 0px;'>";
	$bottom_line = "<p style='font-family: Digital\-7; font-size: 35px; text-align: left; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; z-index: 100; color: cyan; line-height: 90%; top: 140px; left: 140px; position: absolute; width: 350px; margin-left: 0px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 480px;'>";
	$side_channel = "<p style='font-family: Digital\-7; font-size: 20px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: right; z-index: 100; top: 10px; position: absolute; width: 480px; right: 27px; margin-left: 0px;'>Channel $channel_num</p>";

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
	$time_style = "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px black, 0 0 25px black, 0 0 5px black; color:" . $text_color . "; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>";
	$top_line = "<p style='font-family: Digital\-7; font-size: 45px; color: $text_color; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; line-height: 92%; text-align: center; z-index: 100; position: absolute; top: 200px; width: 480px; left: 50%; margin-left: -240px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis ,   ; max-width: 480px;'>";
	$middle_line = "<p style='font-family: Digital\-7; font-size: 35px; text-align: center; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; z-index: 100; top: 230px; color: $text_color_alt; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>";
	$bottom_line = "<p style='font-family: Digital\-7; font-size: 35px; text-align: center; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; z-index: 100; color: $text_color; line-height: 90%; top: 275px; position: absolute; width: 480px; left: 50%; margin-left: -240px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 480px;'>";
	$side_channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: $text_color_alt; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel $channel_num</p>";

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
} else { #PSEUDO CHANNEL OFF
	$top_section = $time_style . $date . "</p>" . $position;
	$middle_section = $top_line . $day . "</p>";
	$bottom_section = "<p></p>";
}

  if ($xml['size'] != '0') { # IF PLAYING CONTENT
      foreach ($xml->Video as $clients) {
          if(strstr($clients->Player['address'], $plexClient)) {
			    #IF PLAYING COMMERCIAL
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

					$top_section = $background_art . $time_style . $date . $side_channel . "</p>" . $position;
					$middle_section = $top_line . $clients['title'] . $middle_line . $clients['year'] . "</p>";
					$bottom_section = $bottom_line . $clients['tagline'] . "</p>";
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
					}
				}
		  }
	  }

$results['top'] = "$top_section";
$results['middle'] = "$middle_section $bottom_section";
$results['bottom'] = "<p></p>";
echo json_encode($results);
?>
