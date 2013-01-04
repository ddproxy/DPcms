<?php
class Node {
	// Initiate database object
	protected $SQL;
	protected $node_assoc;
	public $access;
	protected $sql_array;
	protected $error = NULL;
	protected $schema;
	protected $config;
	protected $menu;
	protected $entity = array(
		// Include a "schema"=> as the table name
		"schema"=>prototype,
		// Must include a "data" to house column information.
		"data"=>array(
			// Each sql column should be defined via following syntax
			// array(
			// 	name=> column name
			// 	type=> column type and extra information (NOT NULL)
			// 	format=> optional html formatting for public view
			// 	p_show=> bitwise for whether the column can be show publicly
			// 	a_show=> bitwise for whether the column can be shown to administrator
			// 	edit=> default's as 0, 1 will prevent column from being edited or used in creation
			// 	group=> define which group's should be able to view this content (overrides p_show)
			// 	agroup=> define which group's can edit this content (overrides a_show)
			// )
			//
			// 'engine'=>'InnoDB',
			// 'pkey'=>'private key by column name'
			// 
			// The following is a prototype data array set modeled from 'content'
			//
			array(name=>id,type=>'int NOT NULL AUTO_INCREMENT',p_show=>0,a_show=>1,edit=>1),
			array(name=>title,type=>'VARCHAR(150)',p_show=>1,a_show=>1),
			array(name=>alias,type=>'VARCHAR(128)',p_show=>0,a_show=>1),
			array(name=>bodytext,type=>'TEXT',p_show=>1,a_show=>1),
			array(name=>created,type=>'TIMESTAMP',p_show=>0,a_show=>1,edit=>1),
			'engine'=>'InnoDB',
			'pkey'=>'id'
			)
		);
	// 
	//
	//
	//
	public function __construct() {
		// Pull configuration file - need the config[BaseUrl] value
		$this->config = parse_ini_file('./settings/config.ini');
			// Set this->schema so you don't have to call this->entity[schema]
			$this->schema = $this->entity[schema];
		// Set mysql object
		$this->SQL = new mysql();
		if (!isset($this->access))
			$this->access = $_SESSION[ddproxy_access];
		print $this->access;
		// Call entity to check entity array for errors.
		$this->error .= entity($this->entity, error);
		$this->menu = array('show','show_list','add');
		}
	public function add_error() {
		(isset($this->error) && $this->access == 1) ? $return = "<div class='error border'>" . $this->error . "</div>" : NULL;
		return $return;
	}
	public function __destruct() {
		foreach ($this as $key => $value) {
			unset($this->$key);
        } 
	}
	
	/* Initial functions
	* These should be the only functions directly referenced to by DPCMS
	* and will decide whether the user has access to the admin or public
	* content.
	* 
	* Default functions should include:
	*	settings()
	*	show($entry)
	*	show_front()
	*	edit($entry)
	*	add()
	*
	*
	*
	*/
	
	public function settings() {
		// Settings for this node type -- need to add functions to handle node settings
		echo ucfirst($this->schema). " Settings";
	}
	public function show($entry=NULL) {
		// Return correct display by user permission
		return (($this->access == 1) ? $this->display_admin($entry) : $this->display_public($entry));
	}
	public function show_front() {
		// Return correct display by user permission
		return (($this->access == 1) ? $this->display_admin(NULL) : $this->display_public(NULL));
	}
	public function show_list() {
		// Return list if user has permission to accesss
		if ($this->access == 1) {
			$q = "SELECT " . entity($this->entity, private_var) . " FROM " . $this->schema ;
			(isset($_POST[$this->entity[data][pkey]])) ? $q_u = "DELETE FROM " . $this->schema ." WHERE " . $this->entity[data][pkey] . "=" . $_POST[$this->entity[data][pkey]] : NULL ;
			(isset($q_u)) ? $r_u = $this->SQL->query($q_u) : NULL ;
		if ($q) {
			// Pull with query function
			$r = $this->SQL->query($q);
			$entry_display .= "<div id='body' class='border admin node list $this->schema'>";
			$entry_display .= "<div id='node-menu' class='border $this->schema'>" . menu($this->menu,$this->config[BaseUrl],$this->schema) . "</div>";
			// Return add form if user has permission to access
			// add form is formatted by entity() with $this->entity information.
			if ( $r !== false && $this->SQL->num_rows($r) > 0 ) {
			// Then assign row-vars to static's
			$entry_display .= "<table class='$schema'>";
			while ( $a = $this->SQL->fetch_assoc($r) ) {
				// Use Entity() to return a formatted node
				$entry_display .= entity($this->entity, show_list, $a);
			}
			$entry_display .= "</table>";
			}
		$entry_display .= "</div>";
		}
		return $entry_display;
		} else { exit("You don't have administrator permission to access this content."); }
	}
	public function edit($entry=NULL) {
		(isset($_POST[$this->entity[data][2][name]])) ? $this->write($_POST) : NULL;
		// Return edit form if user has permission to accesss
		if ($this->access == 1) {
		echo $entry;
		(is_numeric($entry)) ?$id = $entry : $alias = $entry;
		// Construct pull content from tables limited by 3 rows
		if ($alias)
			$q = "SELECT " . entity($this->entity, private_var, $a) . " FROM " . $this->entity[schema] . " WHERE alias='" . $alias . "'";
		elseif ($id)
			$q = "SELECT " . entity($this->entity, private_var, $a) . " FROM " . $this->entity[schema] . " WHERE " . entity($this->entity, key, $a) . "='" . $id . "'";
		if ($q) {
			// Pull with query function
			$r = $this->SQL->query($q);
			$entry_display .= "<div id='body' class='border admin node edit $this->schema'>";
			$entry_display .= "<div id='node-menu' class='border $this->schema'>" . menu($this->menu,$this->config[BaseUrl],$this->schema) . "</div>";
			// Return add form if user has permission to access
			// add form is formatted by entity() with $this->entity information.
			$entry_display .= (($this->access== 1 ) ? entity($this->entity, edit,$this->SQL->fetch_assoc($r)) : exit("You don't have administrator access to this content."));
			$entry_display .= "</div>";
		}
		return $entry_display;
		} else { exit("You don't have administrator permission to access this content."); }
	}
	public function add() {
		(isset($_POST[$this->entity[data][2][name]])) ? $this->write($_POST) : NULL;
		$entry_display .= "<div id='body' class='border admin node add $this->schema'>";
		$entry_display .= "<div id='node-menu' class='border $this->schema'>" . menu($this->menu,$this->config[BaseUrl],$this->schema) . "</div>";
		// Return add form if user has permission to access
		// add form is formatted by entity() with $this->entity information.
		$entry_display .= (($this->access== 1 ) ? entity($this->entity, add) : exit("You don't have administrator access to this content."));
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
	*	display_edit($entry)
	*/
	protected function display_public($entry=NULL) {
		$entry_display .= "<div id='body' class='border node $this->schema'>";
		// Check $entry	is numeric -> set as id else set as alias
		// Construct pull content from tables limited by 3 rows
		if ($alias)
			$q = "SELECT " . entity($this->entity, public_var) . " FROM " . $this->schema . " WHERE alias='" . $alias . "'";
		elseif ($id)
			$q = "SELECT " . entity($this->entity, public_var) . " FROM " . $this->schema . " WHERE " . entity($this->entity, key) . "='" . $id . "'";
		else
			$q = "SELECT " . entity($this->entity, public_var) . " FROM " . $this->schema . " ORDER BY " . $this->entity[data][pkey];
		// Pull with query function
		$r = $this->SQL->query($q);
		// If entries exists
		if ( $r !== false && $this->SQL->num_rows($r) > 0 ) {
			// Then assign row-vars to static's
			while ( $a = $this->SQL->fetch_assoc($r) ) {
				// Use Entity() to return a formatted node
				$entry_display .= entity($this->entity, format_p, $a);
			}
		} else { // Else show no posts
		$entry_display .= <<<ENTRY_DISPLAY
		<article>
	<H2>There are no nodes of $this->schema</H2>
	<P>
		No entries have been made yet.
		Please check back soon.
	</P>
	</article>
ENTRY_DISPLAY;
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
	public function write($p) {
		(isset($p[entity($this->entity,key)])) ? $return = entity($this->entity,modify,$p) : $return = entity($this->entity,write,$p);
		return $this->SQL->query($return);
	}
}

?>