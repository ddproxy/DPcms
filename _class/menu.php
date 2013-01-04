<?php

class menu {
	protected $SQL;
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
	public function __construct() {
		// Pull configuration file - need the config[BaseUrl] value
		$this->config = parse_ini_file('./settings/config.ini');
			// Set this->schema so you don't have to call this->entity[schema]
			$this->schema = $this->entity[schema];
		// Set mysql object
		$this->SQL = new mysql();
		if (isset($_SESSION['ddproxy_access']))
			$this->access = $_SESSION['ddproxy_access'];
		// Call entity to check entity array for errors.
		$this->error .= entity($this->entity, error);
		$this->menu = array('show','show_list','add');
	}
}
?>