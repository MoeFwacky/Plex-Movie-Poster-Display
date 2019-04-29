<html>
    <head>
    <title></title>

    <script type="text/javascript" src="assets/js/jquery-3.0.0.min.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/PieBox.css">
    <link rel="stylesheet" href="css/tv.css">

    <script>
        $(document).ready(
            function() {
                setInterval(function() {
                    $.getJSON('getData.php',function(data) {
                        $.each(data, function(key, val) {
                            $('#'+key).html(val);
                        });
                    });
                }, 3000);
            });
    </script>
<?php
if (isset($_GET['size'])) {
	$size=$_GET['size'];
	} else {
	$size="half";
}
if ($size == "half") {
	$vcr_time = "vcr-time-half";
	$vcr_info_1 = "vcr-info-half-1";
	$vcr_info_2 = "vcr-info-half-2";
	$vcr_info_3 = "vcr-info-half-3";
	$vcr_side = "vcr-side-half";
	} elseif ($size == "full") {
	$vcr_time = "vcr-time-full";
	$vcr_info_1 = "vcr-info-half-1-idle";
	$vcr_info_2 = "vcr-info-half-2-idle";
	$vcr_info_3 = "vcr-info-half-3-idle";
	$vcr_side = "vcr-side-full";
}
?>

    </head>

    <body class='vcr-body'>
        <div id="container" style="max-width:480">
            <div id="alert" align="left" scrolling="no"></div>
            <div id="top" align="left" scrolling="no"></div>
            <div id="middle" scrolling="no"></div>
            <div id="bottom" scrolling="no"></div>
        </div>
    </body>
</html>
