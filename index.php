<?php

session_start();

$cookie_expire = time()+60*60*24*365*10; // Время жизни - 10 лет

if(isset($_COOKIE['user_id']))
  $user_id = $_COOKIE['user_id'];
else
  $user_id = uniqid();
setcookie("user_id", $user_id, $cookie_expire, "/");

require_once "./class/Simpla.php";
require_once "tokensignin.php";

$simpla = new Simpla();

$files = $simpla->files->get_files(array('order'=>'id','direction'=>'desc','user_id'=>$user_id));

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="google-signin-client_id" content="1098569029953-s90u3tebb0tq6dpu603huljc6nbi2pvp.apps.googleusercontent.com">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/img/favicon.png">

    <title>Seyarabata – filesharing made easy</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/main.css?v9" rel="stylesheet">

  </head>

  <body>

    <div class="container">
      <div class="row">
        <div class="col-md-2 col-lg-2">
        </div>

        <div class="col-md-8 col-lg-8">
          <div class="text-xs-center no-overflow logo"><img class="logo-rotate" src="img/logo-1.png?v1"></div>
          <div class="text-xs-center head"><img src="img/head-1.png?v1"></div>
          <div id="container"></div>
          <form id="change-form">
          <div class="input-group link-change-block">
            <span class="input-group-addon" id="basic-addon3">http://seyarabata.com/</span>
            <input type="hidden" name="id" id="basic-id">
            <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" pattern="[A-Za-z0-9._-]{1,200}" maxlength="200" minlength="1">
          </div>
          </form>
          <div class="text-xs-center link-block"><a href="#"></a> <button type="button" class="btn btn-outline-secondary btn-sm btn-invis">Change</button></div>
          <div class="progress-block"><progress class="progress progress-striped progress-animated" value="0" max="100"></progress></div>
          <div class="text-xs-center status-line"><span class="file-name"></span><span class="file-size"></span><span class="upload-speed"></span></div>
          <div class="text-xs-center upload-button-block"><button type="button" class="btn btn-primary" id="pickfiles">Upload file</button></div>
          <!--
          <div class="g-signin2" data-onsuccess="onSignIn"></div><a href="#" onclick="signOut();">Sign out</a>
          -->
          <?php print_r($_SESSION['social_network']) ;?>
            <?php if(!empty($_SESSION['social_network']['social_id'])) :?>
                <p><a href="index.php?action=logout">Выйти</a></p>
            <?php else:?>
                <p><a href="<?php echo $google->getLink(); ?>">Аутентификация через Google</a></p>
            <?php endif;?>
          <div class="m-x-auto">
          <table class="table table-hover table-sm table-sm-font history-block">
            <tbody>
            <?php if($files && is_array($files)): ?>
            <?php foreach ($files as $file): ?> 
              <tr>
                <td><a href="<?php echo $file->url; ?>" target="_blank"><?php echo $file->url; ?></a></td>
                <td><?php echo Files::human_filesize($file->file_size); ?></td>
                <td><?php echo array_pop(explode(DIRECTORY_SEPARATOR, $file->file)); ?></td>
              </tr>
            <?php endforeach; ?>
            <?php endif ?>
            </tbody>
          </table>
          </div>
          
        </div>

        <div class="col-md-2 col-lg-2">
        </div>
      </div>

    </div>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <script src="js/ie10-viewport-bug-workaround.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="/js/plupload.full.min.js"></script>
    <script type="text/javascript" src="/js/main.js?v3"></script>
  </body>  
</html>
