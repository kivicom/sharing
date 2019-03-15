function get_history(){
$.ajax({
  url: "history.php",
  dataType: 'html',
  success: function(data){
    //console.log(data);
    $('.history-block tbody').html(data);
  }
});
}
$( document ).ready(function(){
  $('.link-change-block input').keypress(function(){
    if($(this).parent().hasClass('has-error'))
      $(this).parent().removeClass('has-error');
  });
  $('.link-block button').click(function(){
    $('.link-change-block').css( "display", "table");
    $('.save-block').show();
    $('.upload-button-block').hide();
    $('.link-block').hide();  
    $('#basic-url').focus();
  });
  $('#change-form').submit(function(e){
    e.preventDefault();
    var url = $('#basic-url').val();
    var id  = $('#basic-id').val();
    $.ajax({
      url: "change.php",
      data: {id: id, url: url},
      dataType: 'json',
      success: function(data){
        if(data.error){
          $('.link-change-block').addClass('has-error');
          console.log(data.error);
        }else{
          $('.link-change-block').hide();
          $('.save-block').hide();
          $('.link-block a').attr('href',data.url);
          $('.link-block a').text(data.url);
          $('.upload-button-block').show();
          $('.link-block').show();
          $('#basic-url').val(data.url_id);
          get_history();
        }
        
      }
    });
  })
  
})
var speedTime = Date.now();
var speedInterval = 1500;
var uploader = new plupload.Uploader({
  runtimes : 'html5,flash,silverlight,html4',
  browse_button : 'pickfiles', // you can pass an id...
  container: document.getElementById('container'), // ... or DOM Element itself
  url : 'upload.php',
  flash_swf_url : '../js/Moxie.swf',
  silverlight_xap_url : '../js/Moxie.xap',
  chunk_size: '2mb',
  multi_selection: false,
  autostart: true,  
  filters : {
    max_file_count: 1,    
  },

  init: {
    PostInit: function() {
      //document.getElementById('filelist').innerHTML = '';

    },

    FilesAdded: function(up, files) {
      plupload.each(files, function(file) {
        $('.file-name').text(file.name);
        $('.file-size').text(plupload.formatSize(file.size));
        //document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <span id="speed"></span> <b></b></div>';
        $('#pickfiles').hide();
        $('.progress-block').show();
        $('.status-line').show();
        uploader.start();
      });
    },

    UploadProgress: function(up, file) {
      $('progress').val(file.percent);
      if(speedTime+speedInterval<Date.now()){
        $('.upload-speed').text((parseInt(up.total.bytesPerSec)/1024/1024*8).toFixed(2)+' Mbit/s');
        speedTime = Date.now();
      }
      
    },

    Error: function(up, err) {
      document.getElementById('console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
    },

    FileUploaded: function(up, file, res){
      var json = JSON.parse(res.response)
      var url = json.result;
      var id = json.id;
      $('.link-block a').attr('href',url).text(url);
      $('.progress-block').hide();
      $('.status-line').hide();
      $('#basic-url').val(id);
      $('.link-change-block input[name=id]').val(id);
      $('#pickfiles').text('Upload one more');
      $('#pickfiles').show();
      $('.link-block').show();
      get_history();
    }
  }
});

uploader.init();
/*
function onSignIn(googleUser) {
  var profile = googleUser.getBasicProfile();
  console.log('ID: ' + profile.getId()); // Do not send to your backend! Use an ID token instead.
  console.log('Name: ' + profile.getName());
  console.log('Image URL: ' + profile.getImageUrl());
  console.log('Email: ' + profile.getEmail());
  var id_token = googleUser.getAuthResponse().id_token;
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'http://seyarabata.com/tokensignin.php');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
    console.log('Signed in as: ' + xhr.responseText);
  };
  xhr.send('idtoken=' + id_token + '&email=' + profile.getEmail());
}
 function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
      console.log('User signed out.');
    });
  }
  */