<?php
include 'config.php';
$results = Array();

#Display Custom Image
if ($customImageEnabled == "Yes") {
  $title = "<br /><p style='font-size: 55px; -webkit-text-stroke: 2px yellow;'> &nbsp; </p>";
  $display = "<img src='$customImage' style='width: 100%'>";
  $info = "<p style='font-size: 25px;'> &nbsp; </p>";
} else {

#Plex Module
  $url     = 'http://'.$plexServer.':32400/status/sessions?X-Plex-Token='.$plexToken.'';
  $getxml  = file_get_contents($url);
  $xml 	 = simplexml_load_string($getxml) or die("feed not loading");
  $title   = NULL;
  $display = NULL;
  $info    = NULL;
  $date = date('H:i');
  $day = date('D F d, Y');
#  $ch1 = simplexml_load_file("/var/www/html/ch1/schedules/pseudo_channel.xml");
$pgrep = shell_exec('find /home/pi/channels/ -name "running.pid" -type f -exec cat {} +');
$pdir = shell_exec('find /home/pi/channels/ -name "running.pid" -type f');

  if ($xml['size'] != '0') {
      foreach ($xml->Video as $clients) {
          if(strstr($clients->Player['address'], $plexClient)) {
#Movie Playing
            if($clients['librarySectionID'] == $plexServerMovieSection) {
            	$art = $clients['art'];

                $poster = explode("/", $art);
                $poster = trim($poster[count($poster) - 1], '/');
                $filename = '/cache/' . $poster;

                if (file_exists($filename)) {
                    #file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                } else {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                }
		$bg = "<p><img position: absolute; top: 0; src='http://$plexServer:32400$art' width='480' style='opacity:0.5;'></p>";
                $title =  "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px black, 0 0 25px black, 0 0 5px black; color: yellow; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>" . $date . "</p><p style='font-family: Digital\-7; font-size: 15vw; font-size: 13vh; color: yellow; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; line-height: 92%; text-align: center; z-index: 100; position: absolute; top: 200px; width: 480px; left: 50%; margin-left: -240px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis ,   ; max-width: 480px;'>" . $clients['title'] . "</p><img position: absolute; top: 20px; src='http://$plexServer:32400$art' width='480' style='opacity:1;'>";
                $display = "<p style='font-family: Digital\-7; font-size: 35px; text-align: center; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; z-index: 100; top: 230px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>" . $clients['year'] . "</p>";
                if (strpos($pdir,'pseudo-channel_01')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 1</p>";
                }
                if (strpos($pdir,'pseudo-channel_02')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 2</p>";
                }
                if (strpos($pdir,'pseudo-channel_03')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 3</p>";
                }
                if (strpos($pdir,'pseudo-channel_04')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 4</p>";
                }
                if (strpos($pdir,'pseudo-channel_05')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 5</p>";
                }
                if (strpos($pdir,'pseudo-channel_06')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 6</p>";
                }
                if (strpos($pdir,'pseudo-channel_07')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 7</p>";
                }
                if (strpos($pdir,'pseudo-channel_08')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 8</p>";
                }
                if (strpos($pdir,'pseudo-channel_09')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 9</p>";
                }
                if (strpos($pdir,'pseudo-channel_10')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 10</p>";
                }
                $info = "<p style='font-family: Digital\-7; font-size: 35px; text-align: center; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; z-index: 100; color: yellow; line-height: 90%; top: 275px; position: absolute; width: 480px; left: 50%; margin-left: -240px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 480px;'>" . $clients['tagline'] . "</p>";
	    }

#TV Show Playing
            if($clients["librarySectionID"] == $plexServerTVSection) {
                $art = $clients['grandparentArt'];

                $poster = explode("/", $art);
                $poster = trim($poster[count($poster) - 1], '/');
                $filename = '/cache/' . $poster;

                if (file_exists($filename)) {
                    #file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                } else {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                }
		$bg = "<div style='background-image: url(http://$plexServer:32400$art); background-repeat: no-repeat;width: 480px;'>";
                $title =  "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px black, 0 0 25px black, 0 0 5px black; color: yellow; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>" . $date . "</p><img position: absolute; top: 20px; src='http://$plexServer:32400$art' width='480' style='opacity:0.9;'>";
                $display = "<p style='font-family: Digital\-7; font-size: 35px; color: yellow; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; line-height: 92%; text-align: center; z-index: 100; position: absolute; top: 200px; width: 480px; left: 50%; margin-left: -240px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 480px;'>" . $clients['grandparentTitle'] . "</p><p style='font-family: Digital\-7; font-size: 35px; text-align: center; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; z-index: 100; top: 230px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>" . $clients['parentTitle'] . ", Episode " . $clients['index'] . "</p>";
                if (strpos($pdir,'pseudo-channel_01')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 1</p>";
                }
                if (strpos($pdir,'pseudo-channel_02')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 2</p>";
                }
                if (strpos($pdir,'pseudo-channel_03')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 3</p>";
                }
                if (strpos($pdir,'pseudo-channel_04')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 4</p>";
                }
                if (strpos($pdir,'pseudo-channel_05')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 5</p>";
                }
                if (strpos($pdir,'pseudo-channel_06')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 6</p>";
                }
                if (strpos($pdir,'pseudo-channel_07')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 7</p>";
                }
                if (strpos($pdir,'pseudo-channel_08')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 8</p>";
                }
                if (strpos($pdir,'pseudo-channel_09')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 9</p>";
                }
                if (strpos($pdir,'pseudo-channel_10')!==False) {
                        $channel = "<p style='font-family: Digital\-7; font-size: 30px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: white; line-height: 92%; text-align: center; z-index: 100; top: 25px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 10</p>";
                }
                $info = "<p style='font-family: Digital\-7; font-size: 35px; text-align: center; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; z-index: 100; color: yellow; line-height: 90%; top: 275px; position: absolute; width: 480px; left: 50%; margin-left: -240px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 480px;'>" . $clients['title'] . "</p>";
           }

#70s Commercial Playing
		if($clients["librarySectionID"] == $plexServer70sCommercialSection) {
                $art = $clients['art'];
                $poster = explode("/", $art);
                $poster = trim($poster[count($poster) - 1], '/');
                $filename = '/cache/' . $poster;

                if (file_exists($filename)) {
                    #file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                } else {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                }
                $bg = "<p><img position: absolute; top: 0; src='http://$plexServer:32400$art' width='480' style='opacity:0.5;'></p>";
		$title = "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px cyan, 0 0 25px cyan, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>" . $date . "</p><img position: absolute; top: 0; src='/assets/vcr-play.jpg' width='480' style='opacity:1;'>";
		$info = NULL;
		$display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>1970s ADVERTISEMENT</p>";
	  }

#80s Commercial Playing
		if($clients['librarySectionID'] == $plexServer80sCommercialSection) {
                $art = $clients['art'];
                $poster = explode("/", $art);
                $poster = trim($poster[count($poster) - 1], '/');
                $filename = '/cache/' . $poster;

                if (file_exists($filename)) {
                    #file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                } else {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                }
                $bg = "<p><img position: absolute; top: 0; src='http://$plexServer:32400$art' width='480' style='opacity:0.5;'></p>";
		$title = "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px cyan, 0 0 25px cyan, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>" . $date . "</p><img position: absolute; top: 0; src='/assets/vcr-play.jpg' width='480' style='opacity:1;'>";
		$info = NULL;
		$display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>1980s ADVERTISEMENT</p>";
	  }

#90s Commercial Playing
		if($clients['librarySectionID'] == $plexServer90sCommercialSection) {
                $art = $clients['art'];
                $poster = explode("/", $art);
                $poster = trim($poster[count($poster) - 1], '/');
                $filename = '/cache/' . $poster;

                if (file_exists($filename)) {
                    #file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                } else {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                }
                $bg = "<p><img position: absolute; top: 0; src='http://$plexServer:32400$art' width='480' style='opacity:0.5;'></p>";
		$title = "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px cyan, 0 0 25px cyan, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>" . $date . "</p><img position: absolute; top: 0; src='/assets/vcr-play.jpg' width='480' style='opacity:1;'>";
		$info = NULL;
		$display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>1990s ADVERTISEMENT</p>";
	  }

#00s Commercial Playing
		if($clients['librarySectionID'] == $plexServer00sCommercialSection) {
                $art = $clients['art'];
                $poster = explode("/", $art);
                $poster = trim($poster[count($poster) - 1], '/');
                $filename = '/cache/' . $poster;

                if (file_exists($filename)) {
                    #file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                } else {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                }
                $bg = "<p><img position: absolute; top: 0; src='http://$plexServer:32400$art' width='480' style='opacity:0.5;'></p>";
		$title = "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px cyan, 0 0 25px cyan, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>" . $date . "</p><img position: absolute; top: 0; src='/assets/vcr-play.jpg' width='480' style='opacity:1;'>";
		$info = NULL;
		$display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>2000s ADVERTISEMENT</p>";
	  }

#10s Commercial Playing
		if($clients['librarySectionID'] == $plexServer10sCommercialSection) {
                $art = $clients['art'];
                $poster = explode("/", $art);
                $poster = trim($poster[count($poster) - 1], '/');
                $filename = '/cache/' . $poster;

                if (file_exists($filename)) {
                    #file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                } else {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                }
                $bg = "<p><img position: absolute; top: 0; src='http://$plexServer:32400$art' width='480' style='opacity:0.5;'></p>";
		$title = "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px cyan, 0 0 25px cyan, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>" . $date . "</p><img position: absolute; top: 0; src='/assets/vcr-play.jpg' width='480' style='opacity:1;'>";
		$info = NULL;
		$display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>2010s ADVERTISEMENT</p>";
	  }

#Fake Commercial Playing
		if($clients['librarySectionID'] == $plexServerFakeCommercialSection) {
                $art = $clients['art'];
                $poster = explode("/", $art);
                $poster = trim($poster[count($poster) - 1], '/');
                $filename = '/cache/' . $poster;

                if (file_exists($filename)) {
                    #file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                } else {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                }
                $bg = "<p><img position: absolute; top: 0; src='http://$plexServer:32400$art' width='480' style='opacity:0.5;'></p>";
		$title = "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px cyan, 0 0 25px cyan, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>" . $date . "</p><img position: absolute; top: 0; src='/assets/vcr-play.jpg' width='480' style='opacity:1;'>";
		$info = NULL;
		$display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>FAKE ADVERTISEMENT</p>";
	  }

#Music Videos Playing
		if($clients['librarySectionID'] == $plexServerMusicVideosSection) {
                $art = $clients['art'];
                $poster = explode("/", $art);
                $poster = trim($poster[count($poster) - 1], '/');
                $filename = '/cache/' . $poster;

                if (file_exists($filename)) {
                    #file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                } else {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                }
                $bg = "<p><img position: absolute; top: 0; src='http://$plexServer:32400$art' width='480' style='opacity:0.5;'></p>";
		$title = "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px cyan, 0 0 25px cyan, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>" . $date . "</p><img position: absolute; top: 0; src='/assets/vcr-play.jpg' width='480' style='opacity:1;'>";
		$info = NULL;
		$display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>MUSIC VIDEO</p>";
	  }

#Station ID Playing
		if($clients['librarySectionID'] == $plexServerStationIDSection) {
                $art = $clients['art'];
                $poster = explode("/", $art);
                $poster = trim($poster[count($poster) - 1], '/');
                $filename = '/cache/' . $poster;

                if (file_exists($filename)) {
                    #file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                } else {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                }
                $bg = "<p><img position: absolute; top: 0; src='http://$plexServer:32400$art' width='480' style='opacity:0.5;'></p>";
		$title = "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px cyan, 0 0 25px cyan, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>" . $date . "</p><img position: absolute; top: 0; src='/assets/vcr-play.jpg' width='480' style='opacity:1;'>";
		$info = NULL;
		$display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>FAKETV HDMI CHANNEL 1</p>";
	  }

#Movie Trailers Playing
		if($clients['librarySectionID'] == $plexServerTrailersSection) {
                $art = $clients['art'];
                $poster = explode("/", $art);
                $poster = trim($poster[count($poster) - 1], '/');
                $filename = '/cache/' . $poster;

                if (file_exists($filename)) {
                    #file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                } else {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                }
                $bg = "<p><img position: absolute; top: 0; src='http://$plexServer:32400$art' width='480' style='opacity:0.5;'></p>";
		$title = "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px cyan, 0 0 25px cyan, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>" . $date . "</p><img position: absolute; top: 0; src='/assets/vcr-play.jpg' width='480' style='opacity:1;'>";
		$info = NULL;
		$display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>MOVIE TRAILER</p>";
	  }
        }
     }
  }

#If Nothing is Playing (off)
  if ($display == NULL) {
	 if ($pgrep >= 1) {
		$title = "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px cyan, 0 0 25px cyan, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>" . $date . "</p><img position: absolute; top: 0; src='/assets/vcr-play.jpg' width='480' style='opacity:1;'>";
				if (strpos($pdir,'pseudo-channel_01')!==False) {
					$display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 1</p>";
				}
                if (strpos($pdir,'pseudo-channel_02')!==False) {
                        $display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 2</p>";
                }
                if (strpos($pdir,'pseudo-channel_03')!==False) {
                        $display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 3</p>";
                }
                if (strpos($pdir,'pseudo-channel_04')!==False) {
                        $display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 4</p>";
                }
                if (strpos($pdir,'pseudo-channel_05')!==False) {
                        $display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 5</p>";
                }
                if (strpos($pdir,'pseudo-channel_06')!==False) {
                        $display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 6</p>";
                }
                if (strpos($pdir,'pseudo-channel_07')!==False) {
                        $display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 7</p>";
                }
                if (strpos($pdir,'pseudo-channel_08')!==False) {
                        $display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 8</p>";
                }
                if (strpos($pdir,'pseudo-channel_09')!==False) {
                        $display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 9</p>";
                }
                if (strpos($pdir,'pseudo-channel_10')!==False) {
                        $display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Channel 10</p>";
                }
                if (strpos($pdir,'pseudo-channel_11')!==False) {
                        $display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>Test Channel for Testing</p>";
                }
	} else {
		$title = "<p style='font-family: Digital\-7; font-size: 195px; font-style: monospace; text-shadow: 1px 1px 3px cyan, 0 0 25px cyan, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 30px; position: absolute; width: 480px; left: 50%; margin-left: -230px;'>" . $date . "</p><img position: absolute; top: 0; src='/assets/vcr.jpg' width='480' style='opacity:1;'>";
		$display = "<p style='font-family: Digital\-7; font-size: 45px; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: cyan; line-height: 92%; text-align: center; z-index: 100; top: 198px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>" . $day . "</p>";
	}
    $info = "<p></p>";
  }

}

$results['top'] = "$channel $title";
$results['middle'] = "$display $info";
$results['bottom'] = "$channel";
echo json_encode($results);
?>
