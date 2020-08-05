(function() {

  function showProfilePic(profilePic) {
    var video = document.getElementById('video');
    video.setAttribute('style', 'display: none');

    if(!profilePic) {
      profilePic = 'images/404.jpg';
    }

    var img = document.getElementById('profile-pic');
    img.setAttribute('src', profilePic);
    img.setAttribute('style', 'display: block;');
  }


  var onlineParams = localStorage.onlineParams;
  if(onlineParams != '') {
    onlineParams = JSON.parse(onlineParams);
  }
  
  var screenName = null;
  var roomMode = null;
  var hlsStream = null;
  var profilePic = null;

  screenName = typeof onlineParams.screenName !== 'undefined' ? onlineParams.screenName : null;
  screenName = !screenName ? 'Model is offline, please visit later' : screenName;

  roomMode = typeof onlineParams.roomMode !== 'undefined' ? onlineParams.roomMode : null;
  roomMode = roomMode == 'priv' ? 'private' : roomMode;

  if(screenName) {
    document.getElementById('model-info').innerHTML  = screenName + (roomMode ? ' (Room Type: ' + roomMode + ')' : '');
  }

  if(typeof onlineParams.modeSpecific !== 'undefined') {
    if(typeof onlineParams.modeSpecific.main !== 'undefined') {
      if(typeof onlineParams.modeSpecific.main.hls !== 'undefined') {
        if(typeof onlineParams.modeSpecific.main.hls.address !== 'undefined') {
          hlsStream = onlineParams.modeSpecific.main.hls.address;
        }
      }
    }
  }
  
  if(typeof onlineParams.publicData !== 'undefined') {
    if(typeof onlineParams.publicData.profilePic !== 'undefined') {
      profilePic = onlineParams.publicData.profilePic;
    }
  }

  // if hls exists
  if(hlsStream) {
    var player = videojs('video');
    player.src({type: 'application/x-mpegURL', src: hlsStream});
    player.ready(function() {
      player.play();
    });

    // show profilepic on error
    player.on('error', function(err) {
      showProfilePic(profilePic);
    });

  // show profilepic when no hls
  } else {
    showProfilePic(profilePic);
  }


})();