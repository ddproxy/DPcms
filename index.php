<?php
require('_class/dpcms.php');
	global $cms;
	$cms = new dpcms();

if ($_SESSION['ddproxy_access']==1) {
	ob_start();
		var_dump($_POST);
	$post = ob_get_clean();
	ob_start();
		var_dump($_GET);
	$get = ob_get_clean();
	ob_start();
		var_dump($_SESSION);
	$session = ob_get_clean();
	ob_start();
		var_dump($cms->GET);
	$cms_get = ob_get_clean();
	
	$string = "<div id='dev_data' class='border'>
	POST:
	" . $post . "
	<br>GET:
	" . $get . "
	<br>SESSION:
	" . $session . "
	<br>CMS->GET:
	" . $cms_get . "
	</div>";
	$cms->add_error($string);
	}
	include_once('template.php');
?>
