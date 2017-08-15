<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="description" content="Download photo albums of the facebook locally or to Google Drive. ">
    <title><?= $album['name'] ?></title>
    <!-- Favicons-->
    <link rel="apple-touch-icon-precomposed" href="assets/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="assets/favicon/mstile-144x144.png">
    <link rel="icon" href="assets/favicon/favicon-32x32.png" sizes="32x32">
    <!--  Android 5 Chrome Color-->
    <meta name="theme-color" content="#EE6E73">
    <!-- CSS-->
    <link href="assets/css/prism.css" rel="stylesheet">
    <link href="assets/css/ghpages-materialize.css" type="text/css" rel="stylesheet" media="screen,projection">
    <link href="https://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style type="text/css">
        h3,h5 {
         -webkit-text-stroke: .2px black;
            color: white;
            text-shadow:
                3px 3px 0 #000,
              -1px -1px 0 #000,  
               1px -1px 0 #000,
               -1px 1px 0 #000,
                1px 1px 0 #000;
            }
    </style>
  </head>
  <body><div class="slider fullscreen">
  <ul class="slides">
    <?php foreach ($album['photos'] as $key => $photo) : ?>
    <li>
      <img src="<?= $photo['images'][0]['source'] ?>"> <!-- random image -->
      <div class="caption <?= $key%2==0?'left':'right' ?>-align">
        <h3><?= isset($photo['name'])?$photo['name']:'&nbsp;' ?></h3>
        <h5 class="light grey-text text-lighten-3"><?= $photo['created_time']->format("l jS F, Y h:i:s A") ?></h5>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
</div>

    <!--  Scripts-->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script>if (!window.jQuery) { document.write('<script src="assets/jquery-3.2.1.min.html"><\/script>'); }
    </script>
    <script src="assets/js/jquery.timeago.min.js"></script>
    <script src="assets/js/prism.js"></script>
    <script src="assets/jade/lunr.min.js"></script>
    <script src="assets/jade/search.js"></script>
    <script src="assets/js/materialize.js"></script>
    <script src="assets/js/slider.js"></script>
  </body>
</html>