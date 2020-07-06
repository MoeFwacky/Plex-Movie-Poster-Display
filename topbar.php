<?php
$ch_file="";
include('./control.php');
include('./config.php');
$tvlocations = glob($pseudochannelTrim . "*", GLOB_ONLYDIR);
$boxes = '';
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
		<div class="container main-container">
			<ul id="gn-menu" class="gn-menu-main">
				<li class="gn-trigger">
					<a class="gn-icon gn-icon-menu"><span>Menu</span></a>
					<nav class="gn-menu-wrapper">
						<div class="gn-scroller">
							<ul class="gn-menu">
								<li><a href="index.php" class="gn-icon gn-icon-earth">Home</a></li>
								<li><a href="adminConfig.php?<?php echo $urlstring;?>" class="gn-icon gn-icon-cog">Settings</a></li>
								<?php echo $boxes; ?>
							</ul>
						</div><!-- /gn-scroller -->
					</nav>
				</li>
				<li><a class="codrops-icon" href="schedule.php?action=up&<?php echo $urlstring; ?>">Up</a></li>
				<li><a class="codrops-icon" href="schedule.php?action=down&<?php echo $urlstring; ?>">Down</a></li>
				<li><a class="codrops-icon" href="schedule.php?action=stop&<?php echo $urlstring; ?>">Stop</a></li>
				<li></li>
			</ul>
		</div><!-- /container -->
		<script>
			new gnMenu( document.getElementById( 'gn-menu' ) );
		</script>
