<?php
$ch_file="";
include('./control.php');
include('./config.php');
$tvlocations = glob($pseudochannelTrim . "*", GLOB_ONLYDIR);
$boxes = '';
if (isset($_GET['tv'])) {
        $plexClientName = $_GET['tv'];
        $urlstring = "tv=" . $_GET['tv'];
        if ($_GET['tv'] != $configClientName) {
                $pseudochannel = $pseudochannelTrim . "_" . $_GET['tv'] . "/";
                $pseudochannel = trim($pseudochannel);
	}
} else {
	$urlstring = "";
}
foreach ($tvlocations as $tvbox) {
        if ($tvbox . "/"  == $pseudochannelMaster) {
                $boxname = $configClientName;
                $boxes .= "<li><a href='schedule.php?tv=$boxname' class='gn-icon gn-icon-videos'>TV: $boxname</a></li>";
        } else {
                $boxname = explode("_", $tvbox);
                $boxes .= "<li><a href='schedule.php?tv=$boxname[1]' class='gn-icon gn-icon-videos'>TV: $boxname[1]</a></li>";
        }
}
?>
			<ul id="gn-menu" class="gn-menu-main">
				<li class="gn-trigger">
					<a class="gn-icon gn-icon-menu"><span>Menu</span></a>
					<nav class="gn-menu-wrapper">
						<div class="gn-scroller">
							<ul class="gn-menu">
								<?php echo $boxes; ?>
							</ul>
						</div><!-- /gn-scroller -->
					</nav>
				</li>
				<li><a href="index.php" class="gn-icon gn-icon-earth">Home</a></li>
				<li><a href="db-schedule.php" class="gn-icon gn-icon-download">Edit Schedule</a></li>
				<li><a href="adminConfig.php" class="gn-icon gn-icon-cog">Settings</a></li>
				<li></li>
			</ul>
		<script>
			new gnMenu( document.getElementById( 'gn-menu' ) );
		</script>
