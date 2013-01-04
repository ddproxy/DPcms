<?php

class dpcms {
	// Initiate database object
	private $SQL;
	// Initiate query variables
	public $user;
	public $module;
	public $get;
	private $body;
	private $node;
	private $error;
	private $menu;
    public function __construct() {
        // Start session _SESSION variables are available
        session_start();
        // Manually setting access level -- 0 for anon, 1 for admin, etc not set
        $config = parse_ini_file('./settings/config.ini');
            $this->title = $config['title'];
            $this->gaccount = $config['Google Analytics'];
            $this->base_url = $config['BaseUrl'];

        require_once('common.php');
        require_once('mysql.php');
        require_once('node.php');
        require_once('menu.php');
		$this->SQL = new mysql();
		// Run modules indiscriminately
		$this->module();
		// If get
		$this->get = ( $_GET ) ? break_get($_GET) : NULL;
		// Default node type to 'content'
		(isset($this->get[0])) ? NULL : $this->get[0] = 'content';
		// If get is set then load node
		// ---- SECURITY FLAW - Pull from content indiscriminately ------
		try {
			(isset($this->get)) ? $this->node[$this->get[0]] = node($this->get[0]) : $this->node[$this->get[0]] = new content();
		} catch(Exception $e) {
			$this->add_error('Caught exception: ' . $e->getMessage());
			$this->node[$this->get[0]] = new content();
		}
		(isset($this->node[$this->get[0]])) ? $this->add_error($this->node[$this->get[0]]->add_error()) : NULL;
		($this->node[$this->get[0]]->ajax==1) ? NULL: NULL;
		($this->get[1] == 'write') ? $this->get[2] = $_POST : NULL;
		($this->get[1]) ? @($get_1 = $this->get[1] AND $this->add_body($this->node[$this->get[0]]->$get_1(@$this->get[2]))) : $this->add_body($this->node[$this->get[0]]->show_front());
		$this->menu = array('Services','Photography','Filmography','Projects','Websites');
		$this->footer .= 'Copyright Digital Design Proxy LLC';
		$this->footer .= menu(array('Analytics','Projects','Email','Contact'),$this->base_url);
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
				$return .= "<div id='login'>";
				$return .= $this->user->show_login();
				$return .= "</div>";
				return $return;
				break;
			case logout:
				$this->user->logout();
			default:
				return "Login Module failed";
			}
	}
	public function add_body($string) {
		$this->body .= $string;
	}
	public function add_error($string) {
		$this->error .= $string;
	}
	public function return_login(){
		$return = $this->user(initiate);
		return $return;
	}
	public function return_body() {
		$this->add_body($this->error);
		return $this->body;
	}
	public function menu() {
		print menu($this->menu,$this->base_url);
	}
	public function submenu() {
	
	}
	public function header_content()	{
		$return = $this->head();
		return $return;
	}
	public function content() {
		return $this->return_body();
	}
	public function footer() {
		return $this->footer;
	}
	protected function module() {
	if ($handle = opendir('./modules')) {
		while (false !== ($file = readdir($handle))) {
			if (strpos($file, '.php',1)||strpos($file, '.module',1)&&($file!='.')&&($file!='..')) {
				require_once("./modules/".$file."");
			}
		}
		//$this->add_body("<div id='module_menu' class='border $this->schema'>" . 
		//menu($menu,$this->base_url,NULL,NULL) .
		//"</div>");
		closedir($handle);
	}
	}
}

?>