<?php

class content {
	private $SQL;
	public function __construct() {
		$this->SQL = new mysql();
	}
	public function show() {
		return (( $_GET['admin'] == 1 ) ? $this->display_admin() : $this->display_public());
	}
	public function display_public() {
		// Construct pull content from tables limited by 3 rows
		$q = "SELECT * FROM content ORDER BY created DESC LIMIT 3";
		// Pull with query function
		$r = $this->SQL->query($q);
		// If entries exists
		if ( $r !== false && mysql_num_rows($r) > 0 ) {
			// Then assign row-vars to static's
			while ( $a = mysql_fetch_assoc($r) ) {
				$title = stripslashes($a['title']);
				$bodytext = stripslashes($a['bodytext']);
				// And construct the display print-out
				$entry_display .= <<<ENTRY_DISPLAY
		<article>
		<H2>$title</H2>
		<P>
			$bodytext
		</P>
		</article>
ENTRY_DISPLAY;
	}
	} else { // Else show no posts
		$entry_display .= <<<ENTRY_DISPLAY
	<H2>There are no posts</H2>
	<P>
		No entries have been made yet.
		Please check back soon.
	</P>
ENTRY_DISPLAY;
	}
	// If user is admin show add new entry
	if ($_SESSION['access_level'] == 1) {
	$entry_display .= <<<ADMIN_OPTION
	<P class="admin_link">
		<a href="{$_SERVER['PHP_SELF']}?admin=1">Add a New Entry</a>
	</P>
ADMIN_OPTION;
	}
	return $entry_display;
}
	public function display_admin() {
		return <<<ADMIN_FORM
		<form action="{$_SERVER['PHP_SELF']}" method="post">
			<label for="title">Title:</label>
			<input name="title" id="title" type="text" maxlength="150" />
			<label for="bodytext">Body Text:</label>
			<textarea name="bodytext" id="bodytext"></textarea>
			<input type="submit" value="Create Entry"/>
		</form>
ADMIN_FORM;
	}
	public function write($p) {
	if ( $p['title'] )
		$title = mysql_real_escape_string($p['title']);
	if ( $p['bodytext'] )
		$bodytext = mysql_real_escape_string($p['bodytext']);
	if ( $title && $bodytext ) {
		$created = time();
		$sql = "INSERT INTO content VALUES('$title','$bodytext','$created')";
		return $this->SQL->query($sql);
	} else
		return false;
	}
}
?>