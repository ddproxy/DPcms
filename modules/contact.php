<?php
$menu[]='contact';
class contact extends Node {
	protected $entity = array(
		"schema"=>contact,
		"data"=>array(
			array(name=>id,type=>'INT(150) NOT NULL AUTO_INCREMENT',p_show=>0,a_show=>1,edit=>1),
			array(name=>title,type=>'VARCHAR(150) NOT NULL',p_show=>1,a_show=>1),
			array(name=>email,type=>'VARCHAR(128) NOT NULL',p_show=>1,a_show=>1),
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
	*/
	
	public function add() {
		(isset($_POST['email'])) ? $this->write($_POST) : NULL;
		$entry_display .= "<div id='body' class='border admin node add $this->schema'>";
		$entry_display .= (($this->access == 1 ) ? "<div id='node-menu' class='border $this->schema'>" . menu($this->menu,$this->config[BaseUrl],$this->schema) . "</div>" : NULL);
		// Return add form if user has permission to access
		// add form is formatted by entity() with $this->entity information.
		$entry_display .= (($this->access == 0 ) ? entity($this->entity, add) : exit("You don't have administrator access to this content."));
		$entry_display .= "</div>";
		return $entry_display;
	}
	
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
	
	protected function display_public($entry=NULL) {
		//$entry_display .= "<div id='body' class='border node $this->schema'>";
		// If user is not admin show add new entry
		if ($this->access == 0) {
			$entry_display .= $this->add();
		}
		//$entry_display .= "</div>";
		return $entry_display;
	}
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
			<input type="hidden" id="id" name="id" value="$id" />
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
	
	public function write($p) {
		if(isset($_POST['email'])) {
		$to = "ddproxy@gmail.com";
		$subject =  "Digital Design Proxy ". $_POST['title'];
		$email_field = $_POST['email'];
		$message = $_POST['bodytext'];
	 
		$body = "E-Mail: $email_field\n Message:\n $message";
		}
		mail($to, $subject, $body);

		(isset($p[entity($this->entity,key)])) ? $return = entity($this->entity,modify,$p) : $return = entity($this->entity,write,$p);
		return $this->SQL->query($return);
	}
}
?>