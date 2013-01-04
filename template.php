<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php print $cms->display_header(); ?>
<title><?php print $cms->title; ?></title>
<link href="<? print $cms->base_url ?>/css/stylesheet.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<? print $cms->base_url ?>/l/fancybox/jquery.fancybox.css?v=2.0.5" type="text/css" media="screen" />
<script type="text/javascript" src="<? print $cms->base_url ?>/l/fancybox/jquery.fancybox.pack.js?v=2.0.5"></script>
<script type="text/javascript" src="<? print $cms->base_url ?>/l/jquery-validation/jquery.validate.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".fancybox").fancybox();
	$("#contact").validate({
		rules: {
			title: "required",
			bodytext: "required",
			email: {
				required: true,
				email: true
			}
		}
	})
});

</script>
<!--[if lt IE 7]>
<style type="text/css">
#menu-block {
	background: url('<? print $cms->base_url ?>/i/menu-block.png');
	border-right: none transparent thin;
	border-radius: 0;
	background-repeat:no-repeat;
	background-position: right center;
	height: 400px;
	text-align: right;
	margin:0;
	padding:0;
	position: relative;
}
</style>
<![endif]-->
</head>

<body>
<div id='body'>
	<div id='menu'>
		<a href='<? print $cms->base_url ?>'><img id='logo' src='<? print $cms->base_url ?>/i/logo.png'></a>
		<div id='menu-block'>
			<?php print @$cms->menu(); ?>
		</div>
	</div>

    <div id='content'>
	<div id='header'>
		<div id='submenu'>
			<?php print @$cms->submenu(); ?>
		</div>
		<div id='header-content'>
			<?php print @$cms->header_content(); ?>
			<?php print @$cms->return_login();?>
		</div>
	</div>
    	<?php print @$cms->content(); ?>
    </div>
	<div id='footer'>
		<?php print @$cms->footer(); ?>
	</div>
</div>
</body>
</html>