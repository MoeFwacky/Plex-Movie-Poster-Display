<?php include_once('config.php'); ?>
<?php include_once('assets/header.php'); ?>

<?php if ($customImageEnabled == "Yes") {
  echo "<div class='alert alert-info'>Custom Image is Enable.</div>";
} ?>


<h4>Plex Status Display</h4>
The Plex status display scrapes Plex sessions to display the current movie or TV show or Pseudo Channel status.<br />

<h4>License</h4>
This is free software under the GPL v3 open source license. Feel free to do with it what you wish, but any modification must be open sourced. A copy of the license is included.

<?php include_once('assets/footer.php'); ?>
