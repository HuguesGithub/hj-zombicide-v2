<!DOCTYPE html>
<html dir="ltr" lang="fr">
  <head>
    <title>Zombicide</title>
<?php
wp_head();
$PageBean = MainPageBean::getPageBean();
$commonUrl = 'http://zombicidev2.jhugues.fr/wp-content/plugins/mycommon/';
$pluginUrl = 'http://zombicidev2.jhugues.fr/wp-content/plugins/hj-zombicide/';
?>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css"
      integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo $commonUrl; ?>web/rsc/css/jquery-ui.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="<?php echo $commonUrl; ?>web/rsc/css/bootstrap-4.min.css" type="text/css" media="all" />
    <link rel="stylesheet" href="<?php echo $pluginUrl; ?>web/rsc/zombicide.css" type="text/css" media="all" />
    <link rel="stylesheet" href="<?php echo $pluginUrl; ?>web/rsc/zombicide.min.css" type="text/css" media="all" />
  </head>
  <body>
    <div id="shell" class="shell <?php echo $PageBean->getShellClass(); ?>">
<?php
  echo $PageBean->displayPublicHeader();
  echo $PageBean->getContentPage();
  echo $PageBean->displayPublicFooter();
?>
    </div>
    <script type='text/javascript' src='<?php echo $commonUrl; ?>web/rsc/js/jquery-ui-min.js'></script>
    <script type='text/javascript' src='<?php echo $pluginUrl; ?>web/rsc/zombicide.js'></script>
<?php wp_footer(); ?>
  </body>
</html>
