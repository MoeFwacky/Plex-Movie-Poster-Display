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

#Movie Playing
  if ($xml['size'] != '0') {
      foreach ($xml->Video as $clients) {
          if(strstr($clients->Player['address'], $plexClient)) {

            if(strstr($clients['librarySectionID'], $plexServerMovieSection)) {
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
                $title =  "<p style='overflow: hidden; font-size: 60px; font-size: 10vw; font-size: 14vh; font-weight: bold; color: yellow; line-height: 92%; text-align: center; z-index: 100; top: 40px; position: absolute; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; width: 440px; left: 50%; margin-left: -220px;'>" . $clients['title'] . "</p><img position: absolute; top: 20px; src='http://$plexServer:32400$art' width='480' style='opacity:1;'>";
                $display = "<p style='overflow: hidden; font-size: 35px; text-align: center; font-weight: bold; z-index: 100; top: 170px; position: absolute; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; width: 480px; left: 50%; margin-left: -240px; width: 480px'>" . $clients['year'] . "</p>";
                $info = "<p style='overflow: hidden; font-size: 30px; text-align: center; z-index: 100; top: 225px; position: absolute; width: 480px; left: 50%; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; margin-left: -240px; line-height: 92%;'>" . $clients['summary'] . "</p>";
	    }

#TV Show Playing
            if(strstr($clients['librarySectionID'], $plexServerTVSection)) {
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
                $title =  "<p style='font-size: 60px; font-size: 12vw; font-size: 15vh; font-weight: bold; color: yellow; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; line-height: 92%; text-align: center; z-index: 100; position: absolute; top: 40px; width: 480px; left: 50%; margin-left: -240px;'>" . $clients['grandparentTitle'] . "</p><img position: absolute; top: 20px; src='http://$plexServer:32400$art' width='480' style='opacity:1;'>";
                $display = "<p style='font-size: 35px; text-align: center; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; font-weight: bold; z-index: 100; top: 210px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>" . $clients['parentTitle'] . ", Episode " . $clients['index'] . "</p>";
                $info = "<p style='font-size: 35px; text-align: center; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; z-index: 100; color: yellow; line-height: 90%; top: 260px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>" . $clients['title'] . "</p>";
           }
        }
     }
  }

#If Nothing is Playing
  if ($display == NULL) {
    $title = "<img position: absolute; top: 0; src='/assets/standby.jpg' width='480' style='opacity:1;'>";
    $info = NULL;
    $display = "<p style='font-size: 45px; font-weight: bold; text-shadow: 5px 5px 10px black, 0 0 25px black, 0 0 5px black; color: yellow; line-height: 92%; text-align: center; z-index: 100; top: 190px; position: absolute; width: 480px; left: 50%; margin-left: -240px;'>" . $date . "</p>";
  }
}

$results['top'] = "$title";
$results['middle'] = "$display $info";
$results['bottom'] = NULL;
echo json_encode($results);
?>
