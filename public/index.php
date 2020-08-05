<?php

  require_once('../app/includes.php');

  // get params
  $affiliateId = null;
  $campaignId = null;

  if(isset($_GET['affid']))
    $affiliateId = (int)$_GET['affid'];

  if(isset($_GET['campaign']))
    $campaignId = (int)$_GET['campaign'];

  if(!$affiliateId || $campaignId) {
    // fall back to 1-1
    $affiliateId = 1;
    $campaignId = 1;
  }

  $fb = new Firebase;
  $campaigns = $fb->getCampaigns();

  // add sample campaigns if not exist
  if(!$campaigns) {
    try {
      $fb->addCampaigns();
      $campaigns = $fb->getCampaigns();
    }
    catch(Exception $e) {
      // handle below in fallback
    }
  }

  // select campaign
  $selectedCampaign = null;
  foreach($campaigns as $campaign) {
    if((int)$campaign->affiliate_id === $affiliateId && (int)$campaign->campaign_id === $campaignId) {
      $selectedCampaign = $campaign;
    }
  }

  if(!$selectedCampaign) {
    // fall back to 1-1
    $selectedCampaign = new stdClass();
    $selectedCampaign->primary_cat = 1;
    $selectedCampaign->tags = [];
    $selectedCampaign->language = '';
  }

  $rc = new RoomController;
  $room = $rc->getSingleRoom($selectedCampaign->primary_cat, $selectedCampaign->tags, $selectedCampaign->language);

?>

<!doctype html>
<html lang="en">

  <meta charset="utf-8">

  <title>stream player</title>
  <meta name="description" content="playing streams">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <head>
    <link href="//vjs.zencdn.net/7.8.2/video-js.min.css" rel="stylesheet">
    <link href="css/app.css" rel="stylesheet">
  </head>

  <body>

    <div class="wrapper">
      <div id="stream-container">
        <!-- stream -->
        <video id="video" class="adjust-video video-js vjs-default-skin" controls muted></video>

        <!-- profile pic -->
        <div class="profile-pic-holder">
          <img id="profile-pic" class="adjust-pic" src style="display: none;">
        </div>

        <!-- screen name + room type-->
        <div class="model-details">
          <h2 id="model-info"></h2>
        </div>
      </div>
    </div>




    <!-- JS scripts -->
    <script src="//vjs.zencdn.net/7.8.2/video.min.js"></script>
    <script src="https://unpkg.com/@videojs/http-streaming@1.13.3/dist/videojs-http-streaming.min.js"></script>
    <script>
      (function() {
        localStorage.onlineParams = <?php echo(json_encode($room->onlineparams)); ?>;
      })();
    </script>
    <script src="js/app.js"></script>
  </body>

</html>