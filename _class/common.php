<?php
@require_once('mysql.php');
@require_once('node.php');

function config($value) {
	$config = parse_ini_file('./settings/config.ini');
	if ($value!=password) {
		return $config[$value];
	} else { return NULL; }
}
function break_get($array) {
	$break = explode("/",$array[query]);
	return $break;
}
function form($d,$sql=NULL) {
	$name = $d[name];
	$type = $d[type];
	$p_show = $d[p_show];
	$a_show = $d[a_show];
	$req = strpos($type,"NOT NULL");
	if ($type=='LONG'||$type=='TEXT') {
		$return .= "<label class='" . (($req) ? "required" : "") . "' for='" . stripslashes($name) . "'>" . ucfirst(stripslashes($name)) . ":</label>";
		$return .= "<textarea id='" . stripslashes($name) . "' name='" . stripslashes($name) . "' />" . ((isset($sql)) ? $sql[$name] : stripslashes($name)) . "</textarea>";
	} elseif($name=='email') {
		$return .= "<label class='" . (($req) ? "required" : "") . "' for='" . stripslashes($name) . "'>" . ucfirst(stripslashes($name)) . ":</label>";
		$return .= "<input type='text' class='email' id='" . stripslashes($name) . "' name='" . stripslashes($name) . "' value='" . ((isset($sql)) ? $sql[$name] : stripslashes($name)) . "' />";
	} else	{
		$return .= "<label class='" . (($req) ? "required" : "") . "' for='" . stripslashes($name) . "'>" . ucfirst(stripslashes($name)) . ":</label>";
		$return .= "<input type='text' id='" . stripslashes($name) . "' name='" . stripslashes($name) . "' value='" . ((isset($sql)) ? $sql[$name] : stripslashes($name)) . "' />";
	}
	return $return;
}
function entity($array = NULL, $option = NULL, $sql = NULL) {
	(is_array($array)) ? NULL : $return .= "You don't have an array being passed to entity().<br>";
	(array_key_exists(schema, $array)) ? $schema = $array[schema] : $return .= "You need to pass a schema to entity().<br>";
	($schema == 'prototype') ? $return .= "You need to define an entity for this node type other than '$schema'.<br>" : NULL;
	(array_key_exists(data, $array)) ? $data = $array[data] : $return .= "You need to pass schema information via $array('data'=>array(array(name=>'column',type=>'data type',p_show=>'bitwise for public display',a_show=>'optional bitwise for admin display')),'pkey'=>'primary key(usually ID)')";
	$config = parse_ini_file('./settings/config.ini');
	(array_key_exists(pkey,$data)) ? NULL : exit("You need to define a pkey in your entity");
	foreach ($data as $d) {
		(is_array($d)) ? $data_a[] = $d : NULL;
		}
	switch ($option) {
		case "write":
			foreach($data as $d) {
				// Check if item is editable/addable and if it exists in the submit query
				(isset($sql[$d[name]])) ?
				// Set values
					($values .= (isset($values) ? "," : NULL) . $d[name])
				&&
				// Set write
					($write .= (isset($write) ? "," : NULL) . "'" . mysql_real_escape_string($sql[$d[name]]) . "'")
				: NULL;
			}
			// Set return as completed SQL query
			$return .= "INSERT INTO " . $schema . " (" . $values . ") VALUES(" . $write . ")";
			break;
		case "modify":
			foreach($data as $d) {
				// Check if item is editable/addable and if it exists in the submit query
				($d[a_show]==1 && isset($sql[$d[name]])) ?
				// Set update
					($update .= (isset($update) ? "," : NULL) . "$d[name]='" . mysql_real_escape_string($sql[$d[name]]) . "'")
				: NULL;
			}
			$return .= "UPDATE " . $schema . " SET " . $update . "WHERE $data[pkey]='" . $sql[$data[pkey]] . "'";
			break;
		case "value":
			$return .= "";
			break;
		case "raw_echo":
			// return a drop of the sql
			$return .= print_r($sql);
			$return .= "<br>\n";
			break;
		case "public_var":
			// show public variables
			foreach($data as $d) {
				($d[p_show]==1) ? ($return .= (isset($return) ? ", " : NULL) . $d[name] ) : NULL;
			}
			break;
		case "private_var":
			// show private variables
			foreach($data as $d) {
				($d[a_show]==1) ? ($return .= (isset($return) ? ", " : NULL) . $d[name] ) : NULL;
			}
			break;
		case "format_a":
			// format for administrator view
			$return .= "<article class='$schema'>";
			foreach ($data as $d) {
				(isset($sql[$d[name]])) ? $return .= (($d[format]) ? "<$d[format]>" . stripslashes($sql[$d[name]]) . "</$d[format]>" : "<label for=" . $d[name] . ">" . (($d[name]==$data[pkey]) ? ucfirst($data[pkey]) . ": " : NULL) . stripslashes($sql[$d[name]]) . " </label>") . "</br>" :  NULL;
			}
			$return .= "</article>";
			break;
		case "format_p":
			// format for public view
			$return .= "<article class='$schema'>";
			foreach ($data as $d) {
				(isset($sql[$d[name]])) ? $return .= (($d[format]) ? "<$d[format]>" . stripslashes($sql[$d[name]]) . "</$d[format]>" : "<label for=" . $d[name] . ">" . stripslashes($sql[$d[name]]) . " </label>" ) ."</br>" : NULL;
			}
			$return .= "</article>";
			break;
		case "add":
			// format default add node page 
			$return .= "<form class='" . $schema . "' id='" . $schema . "' action='" . $config[BaseUrl] . "/" . $schema . "/add' method='post'>";
			foreach ($data as $d) {
				if (isset($d[name]) && ($d[a_show] ==1) && (!$d[edit]==1)) {
					if (!$d[edit] == 1) {
						$return .= form($d);
					} else {
						$return .= "<label for='" . stripslashes($d[name]) . "'>" . ucfirst(stripslashes($d[name])) . ":</label>";
						$return .= "<input type='text' id='" . stripslashes($d[name]) . "' name='" . stripslashes($d[name]) . "' disabled value='Set by CMS' />";
					}
				}
			}
			$return .= "<br><input type='submit' value='Create Entry'/></form>";
			break;
		case "edit":
			// format default edit node page
			$return .= "<form action='" . $config[BaseUrl] . "/" . $schema . "/edit' method='post'>";
			foreach ($data as $d) {
				if (isset($d[name]) && ($d[a_show] ==1)) {
					if ($d[name]==$data[pkey]) {
						$return .="<input type='hidden' id='" . $d[name] . "' name='" . $d[name] . "' value='" . $sql[$d[name]] . "' />";
					} elseif (!$d[edit] == 1) {
						$return .= form($d,$sql);
					}
				}
			}
			$return .= "<br><input type='submit' value='Update Entry'/></form>";
			break;
		case "key":
				$return .= $data[pkey];
			break;
		case "error":
			$return .= "Error reporting on";
			break;
		case "show_list":
			// format as a list
			foreach ($data as $d) {
				($d[type]!=="TEXT"&&"LONG"&&"TIMESTAMP") ? $return .= "<th>$d[name]</th>" : NULL;
			}
			$return .= "<tr>";
			foreach ($data as $d) {
				(isset($sql[$d[name]])) ? $return .= "<td class='" . $d[name] . "'>" . stripslashes($sql[$d[name]]) . "</td>" : NULL;
			}
			$return .= "<td class='edit'>
			<a href='$config[BaseUrl]/$schema/edit/" 
			. $sql[$data[pkey]]. "'>Edit</a></td><td class='delete'><form action='" 
			. $config[BaseUrl] . "/" 
			. $schema . "/show_list' method='post'><input type='hidden' id='" 
			. $data[pkey]. "' name='" 
			. $data[pkey] . "' value='" 
			. $sql[$data[pkey]] . "' />
			<input type='submit' value='Delete'/></form></td>";
			$return .= "</tr>";
			break;
		default:
			$return .= "No option selected.";
	}
	return $return;
	}
function menu($object=NULL,$c=NULL,$s=NULL,$i=NULL) {
	(!isset($object)) ? $return .= 'Menu is not defined.' : NULL;
		$return .= "<ul>";
	foreach ($object as $m) {
		$m = str_replace('.php', '', $m);
		$return .= "<li><a href='$c" . ((isset($s)) ? "/" . $s : NULL). "/$m". ((isset($i)) ? "/" . $i : NULL) . "'>$m</a></li>";
	}
	$return .= "</ul>";
	return $return;
}
function node($n) {
	if (!class_exists($n)) {
		throw new Exception('Node type does not exist.');
	} else
	return new $n();
}
?>