<html>
    <head>
    <title></title>

    <script
    src="https://code.jquery.com/jquery-2.2.4.min.js"
    integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
    crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/PieBox.css">
    <link rel="stylesheet" href="css/tv.css">
    <script>
	const queryString = window.location.search;
	const urlParams = new URLSearchParams(queryString);
	const tv = urlParams.get('tv');
	const getdata = "getClock.php?tv="+tv;
        $(document).ready(
            function() {
                setInterval(function() {
                    $.getJSON(getdata,function(data) {
                        $.each(data, function(key, val) {
                            $('#'+key).html(val);
                        });
                    });
                }, 3000);
            });
    </script>
<?php
include('./config.php');
if (isset($_GET['size'])) {
	$size = $_GET['size'];
	$DisplayType=$_GET['size'];
	} else {
	$size = $DisplayType;
}

if ($DisplayType == "half") {
	$vcr_time = "vcr-time-half";
	$vcr_info_1 = "vcr-info-half-1";
	$vcr_info_2 = "vcr-info-half-2";
	$vcr_info_3 = "vcr-info-half-3";
	$vcr_side = "vcr-side-half";
	} elseif ($DisplayType == "full") {
	$vcr_time = "vcr-time-full";
	$vcr_info_1 = "vcr-info-half-1-idle";
	$vcr_info_2 = "vcr-info-half-2-idle";
	$vcr_info_3 = "vcr-info-half-3-idle";
	$vcr_side = "vcr-side-full";
}
if (isset($_GET['tv'])) {
	$tv = $_GET['tv'];
	$urlstring = "tv=" . $_GET['tv'] . "&";
} else {
	$tv = $plexClientName;
}
$down = "";
if (isset($_GET['down'])) {
	$times = $_GET['down'];
	for ($i = 0; $i < $times; $i++){ $down .= '</br>'; }
}
?>
    </head>


    <body class='vcr-body'><?php echo "$down"?>
        <div id="container" style="max-width:480">
            <div id="alert" align="left" scrolling="no"></div>
            <div id="top" align="left" scrolling="no"></div>
            <div id="middle" scrolling="no"></div>
            <div id="bottom" scrolling="no"></div>
        </div>
    </body>
</html>
