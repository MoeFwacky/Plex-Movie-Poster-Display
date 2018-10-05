<?php if (!empty($_POST)) {

  $myfile = fopen("config.php", "w") or die("Unable to open file!");

  //Hack to fix '... Need to fix this later.
  $_POST[comingSoonTopText] = str_replace("'", "\'", $_POST[comingSoonTopText]);
  $_POST[comingSoonBottomText] = str_replace("'", "\'", $_POST[comingSoonBottomText]);
  $_POST[nowShowingTopText] = str_replace("'", "\'", $_POST[nowShowingTopText]);

  $txt = "

<?php
//Server Configuration
\$plexServer = '$_POST[plexServer]';
\$plexport = '$_POST[plexport]';
\$plexToken = '$_POST[plexToken]';

\n//Cleint Configuration
\$plexClient = '$_POST[plexClient]';

\n//Pseudo Channel
\$pseudochannel = '$_POST[pseudochannel]';

\n//Display Type
\$DisplayType = '$_POST[DisplayType]';
?>
";

  echo  $txt;
  fwrite($myfile, $txt);
  fclose($myfile);
  $update = "1";

} ?>

<?php include_once('config.php'); ?>
<?php include_once('assets/header.php'); ?>

        <h4><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Configuration</h4>

        <?php if($update == "1") {
          echo "<div class='alert alert-info'>Configuration File Updated.</div>";
        } ?>

        <form class="form-horizontal" method="post">
          <div class="form-group">
            <label class="control-label col-sm-2">Server IP: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexServer" value="<?php echo "$plexServer"; ?>">
            </div>
          </div>
        <form class="form-horizontal" method="post">
          <div class="form-group">
            <label class="control-label col-sm-2">Plex Server Port: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexport" value="<?php echo "$plexport"; ?>">
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2"><a href="https://support.plex.tv/hc/en-us/articles/204059436-Finding-your-account-token-X-Plex-Token" target=_blank>
              <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> X-Plex-Token: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexToken" value="<?php echo "$plexToken"; ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2">Client IP: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexClient" value="<?php echo "$plexClient"; ?>">
            </div>
          </div>

                  <form class="form-horizontal" method="post">
          <div class="form-group">
            <label class="control-label col-sm-2">Pseudo Channel Location: </label>
                        <div class="col-sm-10">
              <input type="text" class="form-control" name="pseudochannel" value="<?php echo "$pseudochannel"; ?>">
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2">Display Type: </label>
            <div class="col-sm-10">
              <label><input type="radio" class="form-control" name="DisplayType" value="full">Full Landscape</label>
              <label><input type="radio" class="form-control" name="DisplayType" value="half">Half Landscape</label>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <input class="btn btn-primary" type="submit" value="Save" name='submit' />
            </div>
          </div>

        </form>

<?php include_once('assets/footer.php'); ?>