<?php

class dpcms {
	// Initiate database object
	private $SQL;
	// Initiate query variables
	public $user;
	public $module;

	public function __construct() {
		session_start();
		$config = parse_ini_file('./settings/config.ini');
		$this->title = $config['title'];
		$this->gaccount = $config['Google Analytics'];
		$this->base_url = $config['BaseUrl'];
		require('mysql.php');
		require('content.php');
		$this->SQL = new mysql();
		$this->module['content'] = new content();
	}

	public function display_header() {
		return <<<HEADER
	<link href="$this->base_url/s/core.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="$this->base_url/l/jquery.js"></script>
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '$this->gaccount']);
		_gaq.push(['_trackPageview']);
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})(); </script>
	<script type="text/javascript"> 
	$(document).ready(function(){
	$(".login").click(function(){
	    $(".login_form").slideToggle("slow");
	  });
	});
	</script>
			<title> $this->title </title>
HEADER;
	}
	public function head() {
		return <<<HEADER
	<H1>$this->title </H1>
HEADER;
	}
	public function user($pass) {
		include_once('login.php');
		switch ($pass) {
			case initiate:
				$this->user = new user();
				if(isset($_POST['logout'])) {
					$return = $this->user->logout();
				} else {
				if(!$_POST['username'] || !$_POST['password'])
					$return = $this->user->show_login();
				else
					$return = $this->user->login();
				}
				return $return;
				break;
			case show:
				return "<div id='login'>" . $this->user->show_login() . "</div>";
				break;
			case login:
				return "<div id='login'>" . $this->user->login() . "</div>";
				break;
			case logout:
				$this->user->logout();
			default:
				return "Login Module failed";
			}
	}
}

?>