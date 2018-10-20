<?php 
define('WP_USE_THEMES', false);
require('../../../wp-load.php');
if (!isset($_GET['url'])) {
  global $wp_query;
  $wp_query->set_404();
  status_header(404);
  nocache_headers();
  require get_404_template();
  wp_die();
}

// Count the occurrence of this click
if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
  $val = get_post_meta($_GET['id'], 'link_click_cnt', true);
  $val = preg_match('/^[0-9]+$/', $val) ? $val : 0;
  $val++;
  update_post_meta($_GET['id'], 'link_click_cnt', $val);
}

// wp_redirect($_GET['url']);
// die();

$url = $url . $_GET['url'];

$base_url = dirname($_SERVER['PHP_SELF']) . '/static/';
$img2_url = $base_url . 'logo.png';

?>

<!DOCTYPE html>
<html>
<head>
  <title>Redirecting</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montseratt" />
  <style type="text/css">
    html, body {
      width: 100%;
      height: 100%;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 100%;
      height: 100%;
      /* background-color: #fafafb; 
      color: #1d1d1b;  */
      background-color: rgb(79, 127, 225);
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .centered {
      text-align: center;
      max-width: 600px;
    }

    .text {
      font-size: 35px;
      font-family: "Montseratt", arial, sans serif;
      font-weight: normal;
    }

    .loader {
      height: 100px;
    }

    .lock {
      height: 50px;
    }

    .box {
      min-height: 200px;
      background-color: white;
      border-radius: 10px;
      padding: 40px;
    }

    .logo {
      width: 100%;
      max-width: 300px;
      display: block;
      margin: 0 auto;
    }
  </style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <!-- Taboola Pixel Code -->
  <script type='text/javascript'>
    window._tfa = window._tfa || [];
    window._tfa.push({notify: 'event', name: 'page_view', id: 1124491});
    !function (t, f, a, x) {
           if (!document.getElementById(x)) {
              t.async = 1;t.src = a;t.id=x;f.parentNode.insertBefore(t, f);
           }
    }(document.createElement('script'),
    document.getElementsByTagName('script')[0],
    '//cdn.taboola.com/libtrc/unip/1124491/tfa.js',
    'tb_tfa_script');
  </script>
  <noscript>
    <img src='//trc.taboola.com/1124491/log/3/unip?en=page_view'
        width='0' height='0' style='display:none'/>
  </noscript>
  <!-- End of Taboola Pixel Code -->
</head>
<body>
<div class="container">
  <div class="centered">
    <div class="lock">
      <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="361.118px" height="361.118px" viewBox="0 0 361.118 361.118" style="enable-background:new 0 0 361.118 361.118;" xml:space="preserve"><g><g id="_x32_37._Locked"><g><path d="M274.765,141.3V94.205C274.765,42.172,232.583,0,180.559,0c-52.032,0-94.205,42.172-94.205,94.205V141.3     c-17.34,0-31.4,14.06-31.4,31.4v157.016c0,17.344,14.06,31.402,31.4,31.402h188.411c17.341,0,31.398-14.059,31.398-31.402V172.7     C306.164,155.36,292.106,141.3,274.765,141.3z M117.756,94.205c0-34.69,28.12-62.803,62.803-62.803     c34.685,0,62.805,28.112,62.805,62.803V141.3H117.756V94.205z M274.765,329.715H86.354V172.708h188.411V329.715z      M164.858,262.558v20.054c0,8.664,7.035,15.701,15.701,15.701c8.664,0,15.701-7.037,15.701-15.701v-20.054     c9.337-5.441,15.701-15.456,15.701-27.046c0-17.348-14.062-31.41-31.402-31.41c-17.34,0-31.4,14.062-31.4,31.41     C149.159,247.102,155.517,257.117,164.858,262.558z" style="fill: rgb(255, 255, 255);"></path></g></g></g></svg>
    </div>

    <h3 class="text">We're <strong>tranferring you securely</strong> to compare this card with others</h3>

    <div class="box">

      <div class="loader">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-bars" style="animation-play-state: running; animation-delay: 0s; background: none;"><rect ng-attr-x="{{config.x1}}" y="30" ng-attr-width="{{config.width}}" height="40" fill="#4f80e1" x="15" width="10" style="animation-play-state: running; animation-delay: 0s;"><animate attributeName="opacity" calcMode="spline" values="1;0.2;1" keyTimes="0;0.5;1" dur="1" keySplines="0.5 0 0.5 1;0.5 0 0.5 1" begin="-0.6s" repeatCount="indefinite" style="animation-play-state: running; animation-delay: 0s;"></animate></rect><rect ng-attr-x="{{config.x2}}" y="30" ng-attr-width="{{config.width}}" height="40" fill="#292c44" x="35" width="10" style="animation-play-state: running; animation-delay: 0s;"><animate attributeName="opacity" calcMode="spline" values="1;0.2;1" keyTimes="0;0.5;1" dur="1" keySplines="0.5 0 0.5 1;0.5 0 0.5 1" begin="-0.4s" repeatCount="indefinite" style="animation-play-state: running; animation-delay: 0s;"></animate></rect><rect ng-attr-x="{{config.x3}}" y="30" ng-attr-width="{{config.width}}" height="40" fill="#18cdca" x="55" width="10" style="animation-play-state: running; animation-delay: 0s;"><animate attributeName="opacity" calcMode="spline" values="1;0.2;1" keyTimes="0;0.5;1" dur="1" keySplines="0.5 0 0.5 1;0.5 0 0.5 1" begin="-0.2s" repeatCount="indefinite" style="animation-play-state: running; animation-delay: 0s;"></animate></rect><rect ng-attr-x="{{config.x4}}" y="30" ng-attr-width="{{config.width}}" height="40" fill="#4f80e1" x="75" width="10" style="animation-play-state: running; animation-delay: 0s;"><animate attributeName="opacity" calcMode="spline" values="1;0.2;1" keyTimes="0;0.5;1" dur="1" keySplines="0.5 0 0.5 1;0.5 0 0.5 1" begin="0s" repeatCount="indefinite" style="animation-play-state: running; animation-delay: 0s;"></animate></rect></svg>
      </div>

      <img src="<?= $img2_url ?>" class="logo"/>
    </div>

  </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($){
  var url = '<?= $url ?>';
  setTimeout(function(){
    window.location.href = url
  },1000)
});
</script>

</body>
</html>

