<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<!--<![endif]-->
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $title; ?></title>
  <base href="<?php echo $base; ?>" />
  <?php if ($description) { ?>
  <meta name="description" content="<?php echo $description; ?>" />
  <?php } ?>
  <?php if ($keywords) { ?>
  <meta name="keywords" content= "<?php echo $keywords; ?>" />
  <?php } ?>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <?php if ($icon) { ?>
  <link href="<?php echo $icon; ?>" rel="icon" />
  <?php } ?>
  <?php foreach ($links as $link) { ?>
  <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
  <?php } ?>

  <script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
  <script  src="catalog/view/theme_new_mobile/default/javascript/fancybox/jquery.fancybox.pack.js" type="text/javascript"></script>
  <link href="catalog/view/theme_new_mobile/default/stylesheet/jquery.fancybox.css" rel="stylesheet" />

  <link href="catalog/view/theme/default/javascript/jasny-bootstrap/css/jasny-bootstrap.min.css" rel="stylesheet" media="screen" />

  <link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />

  <script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

  <link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
  <!--<link href="//fonts.googleapis.com/css?family=Open+Sans:400,400i,300,700" rel="stylesheet" type="text/css" />-->
  <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700,900 +' rel='stylesheet' type='text/css'>

  <link href="catalog/view/theme_new_mobile/default/stylesheet/stylesheet.css" rel="stylesheet" />
  <link href="catalog/view/theme_new_mobile/default/stylesheet/stylesheet-popup.css" rel="stylesheet" />


  <?php foreach ($styles as $style) { ?>
  <link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
  <?php } ?>
  <link href="catalog/view/theme_new_mobile/default/stylesheet/colors/<?php echo $color_template;?>/<?php echo $color_template;?>.css" rel="stylesheet" />
  <script src="catalog/view/javascript/common.js" type="text/javascript"></script>

  <?php foreach ($scripts as $script) { ?>
  <script src="<?php echo $script; ?>" type="text/javascript"></script>
  <?php } ?>
  <?php echo $google_analytics; ?>


</head>
<body class="<?php echo $class; ?> popup">