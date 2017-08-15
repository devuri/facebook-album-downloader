<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="description" content="Download photo albums of the facebook locally or to Google Drive.">
    <title>Facebook Downloader</title>
  <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
          <?php if(!$isGoogle): ?>
        $(".move_to_google").click(function(){
            var $toastContent = $('<span>You have to Login to Google</span>').add($('<button onclick="window.location=\'connect_google.php\';" class="btn-flat toast-action">Login</button>'));
            Materialize.toast($toastContent, 10000);
          });
        <?php else: ?>
        $("#move_to_google_selected").click(function(){
            var selected = [];
            $("input:checked").each(function () {
                var id = $(this).val();
                selected.push(id);
                var anch = $('[data-value="'+id+'"]');
                anch.off('click');
                $("#icon"+id).hide();
                $("#loader"+id).show();
            });
            $.ajax({
               type: "POST",
               url: "upload.php",
               data: { id: selected },
               success: function(data) {
                    for(var i in selected){
                        $("input#"+selected[i]).prop('checked', false);
                        $("#loader"+selected[i]).hide();
                        $("#icon"+selected[i]).show();
                        var anch = $('[data-value="'+selected[i]+'"]');
                        anch.removeClass('waves-effect waves-light');
                        anch.addClass("green");
                    }
                    var $toastContent = $('<span>Total Photo Moved :<a  class="btn-flat toast-action">'+data+'</a></span>');
                    Materialize.toast($toastContent, 10000);
               }
            });
        });
        $(".move_to_google").click(function(){
            var id=$(this).data("value");            
            var anch = $(this);
            anch.off('click');
            $("#icon"+id).hide();
            $("#loader"+id).show();
                $.post("upload.php",{id: id.toString()},
                    function(data, status){
                        $("#loader"+id).hide();
                        $("#icon"+id).show();
                        anch.removeClass('waves-effect waves-light');
                        anch.addClass("green");
                        var $toastContent = $('<span>Total Photo Moved :<a  class="btn-flat toast-action">'+data+'</a></span>');
                        Materialize.toast($toastContent, 10000);
                    });
        });
        $("#move_to_google_all").click(function(){
            var albums = [<?= $allIds ?>];
            var anch = $('.move_to_google');
                anch.off('click');
                $("[id^=icon").hide();
                $("[id^=loader]").show();
            $.ajax({
               type: "POST",
               url: "upload.php",
               data: { id: albums },
               success: function(data) {
                    $("[id^=loader]").hide();
                    $("[id^=icon]").show();
                    var anch = $('.move_to_google');
                    anch.removeClass('waves-effect waves-light');
                    anch.addClass("green");
                    var $toastContent = $('<span>Total Photo Moved :<a  class="btn-flat toast-action">'+data+'</a></span>');
                    Materialize.toast($toastContent, 10000);
               }
            });
        });
        
        <?php endif; ?>
      });
    </script>
    <link rel="apple-touch-icon-precomposed" href="assets/favicon/apple-touch-icon-152x152.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="assets/favicon/mstile-144x144.png">
    <link rel="icon" href="assets/favicon/favicon-32x32.png" sizes="32x32">
    <meta name="theme-color" content="#EE6E73">
  <!-- CSS  -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="assets/css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
  <link href="assets/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
  <div class="navbar-fixed">
     <!-- Dropdown Structure -->
      <ul id="file" class="dropdown-content">
        <li><a>Selected</a></li>
        <li><a>All</a></li>
      </ul>
      <!-- Dropdown Structure -->
      <ul id="cloud" class="dropdown-content">
        <li><a id="move_to_google_selected">Selected</a></li>
        <li><a id="move_to_google_all">All</a></li>
      </ul>
     <nav>
      <div class="nav-wrapper">
        <a href="#!" class="brand-logo center">Facebook Album</a>
        <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
        <ul class="right hide-on-med-and-down">
          <li><a class="dropdown-button"  data-activates="file"><i class="material-icons left">file_download</i>Download</a></li>
          <li><a class="dropdown-button" data-activates="cloud"><i class="material-icons left">cloud_download</i>Move to Google Drive</a></li>
        </ul>
        <ul class="side-nav" id="mobile-demo">
          <li><a><i class="material-icons left">file_download</i>Download Selected</a></li>
          <li><a><i class="material-icons left">file_download</i>Download All</a></li>
          <li><a><i class="material-icons left">cloud_download</i>Move to Drive Selected</a></li>
          <li><a><i class="material-icons left">cloud_download</i>Move to Drive All</a></li>
        </ul>
      </div>
    </nav>
  </div>
  <div class="section no-pad-bot" id="index-banner">
    <div class="container">
      <br><br>
      <h1 class="header center orange-text"><?= $_SESSION['profile']['first_name'].' '.$_SESSION['profile']['last_name'] ?></h1>
      <div class="row">
        <!-- CARD START -->
    <?php foreach ($albums as $album): ?>
        <div class="col s12 m6 l4">
          <div class="card">
            <div class="card-image">
              <img src="<?= $album['cover_photo'] ?>" width="300" height="225" >
              <span class="card-title"><?= $album['name'] ?></span>
              <a class="move_to_google btn-floating btn-large halfway-fab waves-effect waves-light red" data-value="<?= $album['id'] ?>"><i id='icon<?= $album['id'] ?>' class="material-icons">cloud_download</i>
                <div style="margin-top: .55em" class="preloader-wrapper small active" id='loader<?= $album['id'] ?>'>
                  <div class="spinner-layer spinner-green-only">
                      <div class="circle-clipper left">
                        <div class="circle"></div>
                      </div><div class="gap-patch">
                        <div class="circle"></div>
                      </div><div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
              </div>
              </a>
            </div>
            <br />
            <br />
            <div class="card-action">
              <a href="album.php?id=<?= $album['id'] ?>"">View</a>
              <a href="#">Download</a>
              <input name="albums" value="<?= $album['id'] ?>" type="checkbox" id="<?= $album['id'] ?>" />
              <label for="<?= $album['id'] ?>">&nbsp;</label>
            </div>
            </div>
          </div>
          <!-- CARD END -->      
      <?php endforeach; ?>
        </div>
      </div>
      <br><br>
    </div>
  </div>
  <div class="container">
    <div class="section">

      <!--   Icon Section   -->
      <div class="row">
        
      </div>

    </div>
    <br><br>
  </div>

  <footer class="page-footer orange">
    
    <div class="footer-copyright">
      <div class="container">
      Developed by <a class="orange-text text-lighten-3" href="http://materializecss.com">Dharmin</a>
      </div>
    </div>
  </footer>


  <!--  Scripts-->
  <script src="assets/js/materialize.js"></script>
  <script src="assets/js/init.js"></script>

  </body>
</html>