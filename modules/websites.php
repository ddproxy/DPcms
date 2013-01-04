<?php
$menu[]='Websites';
class Websites extends Node {
	protected $entity = array(
		"schema"=>websites,
		"data"=>array(
			array(name=>id,type=>'INT(150) NOT NULL AUTO_INCREMENT',p_show=>0,a_show=>1,edit=>1),
			array(name=>title,type=>'VARCHAR(150) NOT NULL',format=>'h2',p_show=>1,a_show=>1),
			array(name=>alias,type=>'VARCHAR(128)',p_show=>0,a_show=>1),
			array(name=>url,type=>'VARCHAR(150)',format=>'screen',p_show=>1,a_show=>1),
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

	protected function display_public($entry=NULL) {
		$entry_display .= "<div id='body' class='border node $this->schema'>";
		// Check $entry	else
		(is_numeric($entry)) ?$id = $entry : $alias = $entry;
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
				$entry_ = entity($this->entity, format_p, $a);
				$entry_ = str_replace("<screen>","<div class='screen'><a class='fancybox' rel='group' href='" . config(BaseUrl) . "/i/public/$a[url].png'><img class='screen' src='" . config(BaseUrl) . "/i/public/$a[url].png'></a><a href='http://$a[url]'>",$entry_);
				$entry_ = str_replace("</screen>","</a></div>",$entry_);
				$entry_display .= $entry_;				
			}
		} else { // Else show no posts
		$entry_display .= <<<ENTRY_DISPLAY
		<article>
	<H2>There are no posts</H2>
	<P>
		No entries have been made yet.
		Please check back soon.
	</P>
	</article>
ENTRY_DISPLAY;
		}
		// If user is admin show add new entry
		if ($_SESSION['access_level'] == 1) {
		$entry_display .= "
		<P class='admin_link'>
			<a href='" . $this->config['BaseUrl'] . "/$this->schema/add'>Add a New Entry</a>
		</P>";
		}
		$entry_display .= "</div>";
		return $entry_display;
	}
	protected function display_admin($entry=NULL) {
		$entry_display .= "<div id ='body' class='border admin node $this->schema'>";
		$entry_display .= "<div id='node-menu' class='border $this->schema'>" . menu($this->menu,$this->config[BaseUrl],$this->schema) . "</div>";
		// Check $entry	is numeric -> set as id else set as alias
		(is_numeric($entry)) ?$id = $entry : $alias = $entry;
		// Construct pull content from tables limited by 3 rows
		if ($alias)
			$q = "SELECT " . entity($this->entity, private_var, $a) . " FROM " . $this->entity[schema] . " WHERE alias='" . $alias . "'";
		elseif ($id)
			$q = "SELECT " . entity($this->entity, private_var, $a) . " FROM " . $this->entity[schema] . " WHERE " . entity($this->entity, key, $a) . "='" . $id . "'";
		else
			$q = "SELECT " . entity($this->entity, private_var) . " FROM " . $this->schema . " ORDER BY " . $this->entity[data][pkey];

		if ($q) {
		// Pull with query function
		$r = $this->SQL->query($q);
		// If entries exists
		if ( $r !== false && $this->SQL->num_rows($r) > 0 ) {
			// Then assign row-vars to static's
			while ( $a = $this->SQL->fetch_assoc($r) ) {
				// Use Entity() to return a formatted node
				$entry_display .= entity($this->entity, format_a, $a);
				// Use menu to link to edit
				$entry_display .= "$a[id]<div id='node-menu' class='border edit $this->schema'>" . menu(array('edit'),$this->config[BaseUrl],$this->schema,$a[entity($this->entity,key,$a)]) . "</div>";
			}
		} else { // Else show no posts
		$entry_display .= <<<ENTRY_DISPLAY
	<H2>There are no nodes of $this->schema</H2>
	<P>
		No entries have been made yet.
		Please check back soon.
	</P>
ENTRY_DISPLAY;
		}
	}
		// If user is admin show add new entry
		if ($this->access == 1) {
		$entry_display .= "
	<P class='admin_link'>
		<a href='" . $this->config['BaseUrl'] . "/$this->schema/add'>Add a New Entry</a>
	</P>";
		}
		$entry_display .= "</div>";
		return $entry_display;
	}
}
?>