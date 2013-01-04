<head>
<?php
	require('dpcms.php');
	$ddp = new dpcms();
	echo $ddp->display_header();
?>
	</head>

	<body><div id="body-wrapper" class="border">
	<div id="head" class="border">
	<div id="title"><? echo $ddp->head();?></div>
	<? echo $ddp->user(initiate);?>

	</div>
<?php
	if ( $_POST )
		$ddp->write($_POST);
	echo $ddp->module['content']->show();
	echo "<div id='dev_data' class='border'>";
	echo "POST:\n";
	var_dump($_POST);
	echo "\n\n<br>GET:\n";
	var_dump($_GET);
	echo "\n\n<br>SESSION:\n";
	var_dump($_SESSION);
	echo "</div>";
?>
</div>
</body>