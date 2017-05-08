<?php
#session_set_cookie_params(60,"/");
#session_start()
include 'config.php';
$results = Array();
$movies = Array();

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

  if ($xml['size'] != '0') {
      foreach ($xml->Video as $clients) {
          if(strstr($clients->Player['address'], $plexClient)) {

            if(strstr($clients['librarySectionID'], "2")) {
            	$art = $clients['thumb'];

                $poster = explode("/", $art);
                $poster = trim($poster[count($poster) - 1], '/');
                $filename = '/cache/' . $poster;

                if (file_exists($filename)) {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                } else {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                }

                $title =  "<p style='font-size: 40px; -webkit-text-stroke: 2px yellow; color: yellow; line-height: 92%;'>" . $clients['title'] . "</p>";
                $display = "<p style='font-size: 35px;'>" . $clients['year'] . "</p>";
                $info = "<p style='font-size: 25px;'>" . $clients['summary'] . "</p>";
	    }

            if(strstr($clients['librarySectionID'], "1")) {
                $art = $clients['grandparentThumb'];

                $poster = explode("/", $art);
                $poster = trim($poster[count($poster) - 1], '/');
                $filename = '/cache/' . $poster;

                if (file_exists($filename)) {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                } else {
                    file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
                }

                $title =  "<p style='font-size: 40px; -webkit-text-stroke: 2px yellow; color: yellow; line-height: 92%;'>" . $clients['grandparentTitle'] . "</p>";
                $display = "<p style='font-size: 25px; line-height: 50%;'>" . $clients['parentTitle'] . ", Episode " . $clients['index'] . "</p>";
                $info = "<p style='font-size: 30px; line-height 50%;'>" . $clients['title'] . "</p><br /><p style='font-size: 30px; color: yellow; bottom: 0px;'> $date </p>" ;
           }
        }
     }
  }

  #If Nothing is Playing
  if ($display == NULL) {
    $title = "<p style='font-size: 45px; -webkit-text-stroke: 2px yellow; color: yellow;'> $date </p>";
    $UnWatchedMoviesURL = 'http://'.$plexServer.':32400/library/sections/'.$plexServerMovieSection.'/unwatched?X-Plex-Token='.$plexToken.'';
    $getMovies  = file_get_contents($UnWatchedMoviesURL);
    $xmlMovies = simplexml_load_string($getMovies) or die("feed not loading");
    $countMovies = count($xmlMovies);
    $f_contents = file("rss.txt");
    $line = $f_contents[rand(0, count($f_contents) - 1)];

    $rss = new DOMDocument();
	$rss->load($line);
	$feed = array();
	foreach ($rss->getElementsByTagName('item') as $node) {
		$item = array (
			'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
			'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
			);
		array_push($feed, $item);
	}
	$limit = 5;
	for($x=0;$x<$limit;$x++) {
		$headline = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
		$description = $feed[$x]['description'];
		$description = substr($description, 0, 100);
	}

    if ($countMovies > '0') {
      foreach ($xmlMovies->Video as $movie) {
        $movies[] = strip_tags($movie['title']);
      }

      $random_keys = array_rand($movies,1);
      $showMovie = $movies[$random_keys];

      foreach ($xmlMovies->Video as $movie) {
         if(strstr($movie['title'], $showMovie)) {
           $art = $movie['thumb'];

           $poster = explode("/", $art);
           $poster = trim($poster[count($poster) - 1], '/');
           $filename = 'cache/' . $poster;

           if (file_exists($filename)) {
              #Future Code Coming
           } else {
              file_put_contents("cache/$poster", fopen("http://$plexServer:32400$art?X-Plex-Token=$plexToken", 'r'));
           }
         }
      }
    }
    $info = "<p style='font-size: 20px; word-wrap: break-word;'>" . $description . "</p>";
    $display = "<p style='font-size: 28px; word-wrap: break-word; line-height: 95%;'>" . $headline . "</p>";
  }
}

$results['top'] = "$title";
$results['middle'] = "$display";
$results['bottom'] = "$info";

echo json_encode($results);
?>
