<?php
$base_url = $this->config->item('base_url'); 
$assets = $this->config->item('assets');
?>


<!DOCTYPE html>
<!--[if IE 8]><html class="no-js lt-ie9" lang="en" ><![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en" ><!--<![endif]-->

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<title>miniMIPS</title>
	<link rel="stylesheet" href="<?php echo $assets; ?>stylesheets/app.css">
	<script src="<?php echo $assets; ?>javascripts/vendor/custom.modernizr.js"></script>
</head>
<body>