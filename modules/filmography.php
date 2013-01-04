<?php
$menu[]='Filmography';
class Filmography extends Node {
	protected $entity = array(
		"schema"=>filmography,
		"data"=>array(
			array(name=>id,type=>'INT(150) NOT NULL AUTO_INCREMENT',p_show=>0,a_show=>1,edit=>1),
			array(name=>title,type=>'VARCHAR(150) NOT NULL',format=>'h2',p_show=>1,a_show=>1),
			array(name=>alias,type=>'VARCHAR(128)',p_show=>0,a_show=>1),
			array(name=>bodytext,type=>'TEXT',format=>'p',p_show=>1,a_show=>1),
			array(name=>created,type=>'TIMESTAMP',p_show=>0,a_show=>1,edit=>1),
			'engine'=>'InnoDB',
			'pkey'=>'id',
			'notes'=>true,
			)
		);
		
	/* Initial functions
	* These should be the only functions directly referenced to by DPCMS
	* and will decide whether the user has access to the admin or public
	* content.
	* 
	* Current functions handled by the parent include:
	*	settings()
	*	show($entry)
	*	show_front()
	*	edit()
	*	add()
	*
	* These functions can be overriden by child node types by removing the
	* comment tags.
	* 
	*/
	
	/*
	
	public function settings() {
		// Settings for this node type -- need to add functions to handle node settings
		echo ucfirst($this->schema). " Settings";
	}
	public function show($entry=NULL) {
		// Return correct display by user permission
		return (( $_SESSION['ddp_access'] == 1 ) ? $this->display_admin($entry) : $this->display_public($entry));
	}
	public function show_front() {
		// Return correct display by user permission
		return (( $_SESSION['ddp_access'] == 1 ) ? $this->display_admin(front) : $this->display_public(front));
	}
	public function edit() {
		// Return edit form if user has permission to accesss
		return (( $_SESSION[ddp_access] == 1) ? $this->display_edit($entry) : exit("You don't have administrator access to this content."));
	}
	public function add() {
		// Return add form is user has permission to access
		// add form is formatted by entity() with $this->entity information.
		return (($_SESSION[ddp_access]== 1 ) ? entity($this->entity, add) : exit("You don't have administrator access to this content."));
	}
	
	*/
	
	/* Display functions
	* These are called by the initial functions after access has been determined
	* and should be protected functions. However, there should still be access checks
	* or group access checks to verify user does have access and to prevent errors
	* or accidental admin access.
	*
	* Current functions handled by parent class
	*	display_public($entry)
	*	display_admin($entry)
	*/
	/*
	protected function display_public($entry=NULL) {
		// Check $entry	else
		if (is_numeric($entry))
			$id = $entry;
		else
			$alias = $entry;
		// Construct pull content from tables limited by 3 rows
		if ($alias)
			$q = "SELECT " . entity($this->entity, public_var) . " FROM " . $this->entity[schema] . " WHERE alias='" . $alias . "'";
		elseif ($id)
			$q = "SELECT " . entity($this->entity, public_var) . " FROM " . $this->entity[schema] . " WHERE " . entity($this->entity, key) . "='" . $id . "'";
		else
			$q = "SELECT " . entity($this->entity, public_var) . " FROM " . $this->entity[schema] . " ORDER BY created DESC LIMIT 3";
		// Pull with query function
		$r = $this->SQL->query($q);
		// If entries exists
		if ( $r !== false && $this->SQL->num_rows($r) > 0 ) {
			// Then assign row-vars to static's
			while ( $a = $this->SQL->fetch_assoc($r) ) {
				$entry_display .= entity($this->entity, format_p, $a);
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
*/
/*	protected function display_admin($entry=NULL) {
		if (is_numeric($entry))
			$id = $entry;
		else
			$alias = $entry;
		if ($alias)
			$q = "SELECT " . entity($this->entity, private_var, $a) . " FROM " . $this->entity[schema] . " WHERE alias='" . $alias . "'";
		elseif ($id)
			$q = "SELECT " . entity($this->entity, private_var, $a) . " FROM " . $this->entity[schema] . " WHERE " . entity($this->entity, key, $a) . "='" . $id . "'";
		else
		return <<<ADMIN_FORM
		<form action="{$_SERVER['PHP_SELF']}" method="post">
			<label for="title">Title:</label>
			<input type="hidden" id="form_id" name="id" value="$id" />
			<input name="title" id="title" type="text" maxlength="150" />
			<label for="bodytext">Body Text:</label>
			<textarea name="bodytext" id="bodytext"></textarea>
			<input type="submit" value="Create Entry"/>
		</form>
ADMIN_FORM;
		if ($q) {
		// Pull with query function
		$r = $this->SQL->query($q);
		// If entries exist
		if ( $r !== false && $this->SQL->num_rows($r) > 0 ) {
			// Then assign row-vars to static's
			while ( $a = $this->SQL->fetch_assoc($r) ) {
				$id = ($a['id']);
				$title = stripslashes($a['title']);
				$bodytext = stripslashes($a['bodytext']);
				$entry_display .= <<<ENTRY_DISPLAY
		<form action="{$_SERVER['PHP_SELF']}" method="post">
			<input type="hidden" id="id" name="id" value="$id" />
			<label for="title">Title:</label>
			<input name="title" id="title" type="text" maxlength="150" value="$title" / >
			<label for="bodytext">Body Text:</label>
			<textarea name="bodytext" id="bodytext">$bodytext</textarea>
			<input type="submit" value="Modify Entry"/>
		</form>
ENTRY_DISPLAY;
				}
			return $entry_display;
			}
		}
	}
	*/
}
?>