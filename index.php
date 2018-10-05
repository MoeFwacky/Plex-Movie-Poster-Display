<html>
    <head>
    <title></title>

    <script type="text/javascript" src="assets/js/jquery-3.0.0.min.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/PieBox.css">

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
    </head>

    <body>
        <div id="container" style="max-width:480">
            <div id="alert" align="left" class="center" scrolling="no"></div>
            <div id="top" align="left" class="center" scrolling="no"></div>
            <div id="middle" class="left" scrolling="no"></div>
            <div id="bottom" align="left" class="center" scrolling="no"></div>
        </div>
    </body>
</html>
