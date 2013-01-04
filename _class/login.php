<?php
class user {
	public $username;
	private $password;
	private $SQL;
	private $last_login;

	public function __construct() {
			$this->SQL = new mysql();
	}
	public function login() {
			$this->username = stripslashes($_POST['username']);
			$this->password = md5($_POST['password']);
			$u_q = $this->SQL->query("SELECT * FROM users WHERE username='$this->username' AND password='$this->password' LIMIT 1");
			$u = $this->SQL->fetch_assoc($u_q);
			if($u['id']) {
				$_SESSION['id'] = $u['id'];
				$_SESSION['ddproxy_access'] = $u['access_level'];
				$_SESSION['started'] = time();
				foreach($u as $key => $value)
					$_SESSION[$key] = stripslashes($value);
				$this->last_login = date('c',time());
				// on successful login update users last login
				$this->SQL->query("UPDATE users SET last_login='$this->last_login' WHERE id='".$_SESSION['id']."' LIMIT 1"); 
				$return .= $this->show_login('success', $u['last_login']);
				unset($_SESSION['password']);
			} else
				$return .= $this->show_login('fail');
		return $return;
	}
	public function show_login($pass = 'true',$last_login = NULL) {
		$return .= '<div id="login">';
		// If successfull login, show last login time as $last_login
		if ($pass == 'success' && isset($last_login))
			$return .= "Your last login was <br>" . $last_login . "<br>";
		// Check Session to see if logged in, if so show logout form
		if (isset($_SESSION['username'])) 
			$return .= $this->logout_form();
		else {
		// Else show login form and if was failed login, report
		$return .= '<h3 class="login">Login</h3>';
		if ($pass == 'fail')
			$return .= '<div id="fail" class="info_div">Incorrect username or password...</div>';
		$return .= $this->login_form();
		}
		$return .= '</div>';
		return $return;
	}
	public function logout() {
		// Destroy this object and the session then print login form
		unset($_SESSION['username']);
		session_destroy();
		return $this->show_login();
	}
	private function logout_form() {
		$base = config(BaseUrl);
		// Formatting for logout form
		return <<<LOGOUT_FORM
		You are logged in : {$_SESSION['name']}
		<br><form id="loginform" action="{$base}/index.php" method="post" name="loginform">
		<input id="logout" class="submit" type="submit" name="logout" value="Log Out"/></form>
LOGOUT_FORM;
	}
	private function login_form() {
		// Formatting for login form
		$base = config(BaseUrl);
		return <<<LOGIN_FORM
		<div id="login_form" class="login_form">
		<form id="loginform" action="{$base}/index.php" method="post" name="loginform">
		<label><strong>Username</strong></label><input id="user_login" type="text" name="username" size="28" />
		<label><strong>Password</strong></label><input id="user_pass" type="password" name="password" size="28" />
		<strong>Remember Me</strong><input id="remember" type="checkbox" name="remember" />
		<input id="save" class="submit" type="submit" value="Log In" /></form>
		</div>
LOGIN_FORM;
	}
}
?>