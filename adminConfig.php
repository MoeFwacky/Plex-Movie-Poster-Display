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
\$plexToken = '$_POST[plexToken]';
\$plexServerMovieSection = '$_POST[plexServerMovieSection]';
\$plexServerTVSection = '$_POST[plexServerTVSection]';
\$plexServer70sCommercialSection = '$_POST[plexServer70sCommercialSection]';
\$plexServer80sCommercialSection = '$_POST[plexServer80sCommercialSection]';
\$plexServer90sCommercialSection = '$_POST[plexServer90sCommercialSection]';
\$plexServer00sCommercialSection = '$_POST[plexServer00sCommercialSection]';
\$plexServer10sCommercialSection = '$_POST[plexServer10sCommercialSection]';
\$plexServerFakeCommercialSection = '$_POST[plexServerFakeCommercialSection]';
\$plexServerMusicVideosSection = '$_POST[plexServerMusicVideosSection]';
\$plexServerStationIDSection = '$_POST[plexServerStationIDSection]';
\$plexServerTrailersSection = '$_POST[plexServerTrailersSection]';

\n//Cleint Configuration
\$plexClient = '$_POST[plexClient]';
\n//Custom Image
\$customImageEnabled = '$_POST[customImageEnabled]';
\$customImage = '$_POST[customImage]';
\n//Misc
\$comingSoonTopText = '$_POST[comingSoonTopText]';
\$comingSoonBottomText = '$_POST[comingSoonBottomText]';
\$nowShowingTopText = '$_POST[nowShowingTopText]';
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

          <div class="form-group">
            <label class="control-label col-sm-2"><a href="https://support.plex.tv/hc/en-us/articles/204059436-Finding-your-account-token-X-Plex-Token" target=_blank>
              <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> X-Plex-Token: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexToken" value="<?php echo "$plexToken"; ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> Movie Section: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexServerMovieSection" value="<?php echo "$plexServerMovieSection"; ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> TV Section: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexServerTVSection" value="<?php echo "$plexServerTVSection"; ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> 70s Commercial Section: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexServer70sCommercialSection" value="<?php echo "$plexServer70sCommercialSection"; ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> 80s Commercial Section: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexServer80sCommercialSection" value="<?php echo "$plexServer80sCommercialSection"; ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> 90s Commercial Section: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexServer90sCommercialSection" value="<?php echo "$plexServer90sCommercialSection"; ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> 2000s Commercial Section: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexServer00sCommercialSection" value="<?php echo "$plexServer00sCommercialSection"; ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> 2010s Commercial Section: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexServer10sCommercialSection" value="<?php echo "$plexServer10sCommercialSection"; ?>">
            </div>
          </div>
 
          <div class="form-group">
            <label class="control-label col-sm-2"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> Fake Commercial Section: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexServerFakeCommercialSection" value="<?php echo "$plexServerFakeCommercialSection"; ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> Music Videos Section: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexServerMusicVideosSection" value="<?php echo "$plexServerMusicVideosSection"; ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> Station ID Section: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexServerStationIDSection" value="<?php echo "$plexServerStationIDSection"; ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> Movie Trailers Section: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexServerTrailersSection" value="<?php echo "$plexServerTrailersSection"; ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-sm-2">Client IP: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="plexClient" value="<?php echo "$plexClient"; ?>">
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-10">
              <input type="hidden" class="form-control" name="customImageEnabled" value="<?php echo "$customImageEnabled"; ?>">
              <input type="hidden" class="form-control" name="customImage" value="<?php echo "$customImage"; ?>">
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <input class="btn btn-primary" type="submit" value="Save" name='submit' />
            </div>
          </div>

        </form>

<?php include_once('assets/footer.php'); ?>
