<?php

	class UserInterface{
		var	$database,
			$fields,
			$title,
			$active,
			$sort,
			$table,
			$location,
			$pom;

		function __construct(&$pom, $database, $table, $title, $active, $sort, $fields, $location){
			$this->database = $database;
			$this->table = $table;
			$this->title = $title;
			$this->active = $active;
			$this->sort = $sort;
			$this->fields = $fields;
			$this->location = $location;
			$this->pom = $pom;

			$this->go();
		}

		private function go(){
			if($_POST['submitted'] == 'true'){
				$this->pom->prepend('page', 'content', '<div class = "alert">{message}</div>');

				if($this->parsePost()){
					if($_POST['id'] == '*NEW*')
						$this->newEntry();
					elseif($_POST['delete'] == 'on')
						$this->deleteEntry();
					else
						$this->updateEntry();
				}
			}

			$this->generateMenu();
		}

		private function parsePost(){
			foreach($_POST as $var => $val){
				if(substr($var, -4) == 'Date')
					$val = $this->dateToTimestamp($val);
				elseif($var == 'active')
					$val = ($val == 'on' ? 1 : 0);
				elseif($var == 'password'){
					if($val == '********'){
						unset($_POST['password']);
						unset($_POST['password2']);
						$this->fields = array_filter($this->fields, 'UserInterface::isNotPassword');
					}
					elseif($val == $_POST['password2']){
						$val = md5($val);
						unset($_POST['password2']);
					}
					else{
						$this->pom->set('vars', 'message', 'Passwords do not match.');
						return false;
					}
						
				}

				$_POST[$var] = mysql_real_escape_string(stripslashes($val));
			}
			
			return true;
		}

		static function isNotPassword($val){
			return ($val == 'password' ? false: true);
		}

		private function newEntry(){
			$fields = "";
			$values = "";
			foreach($this->fields as $field){
				$fields .= "$field,";
				$values .= "'$_POST[$field]',";
			}

			$fields = substr($fields, 0, strlen($fields) - 1);
			$values = substr($values, 0, strlen($values) - 1);
			
			$this->database->query("INSERT INTO $this->table ($fields) VALUES ($values)", false);

			$results = $this->database->query("SELECT MAX(id) AS id FROM $this->table");
			$_GET['id'] = $results[0]['id'];

			$this->pom->set('vars', 'message', 'Added');
		}

		private function deleteEntry(){
			$this->database->query("DELETE FROM $this->table WHERE id = '$_POST[id]'", false);
			$this->pom->set('vars', 'message', 'Deleted');
		}

		private function updateEntry(){
			$updates = "";
			foreach($this->fields as $field)
				$updates .= "$field = '$_POST[$field]',";

			$updates = substr($updates, 0, strlen($updates) - 1);
			$this->database->query("UPDATE $this->table SET $updates WHERE id = '$_POST[id]'", false);

			$this->pom->set('headers', 'location', "$this->location?id=$_POST[id]");
		}

		private function generateMenu(){
			$this->pom->append('vars', 'id', "<option value = \"*NEW*\">-- New --</option>");
			$results = $this->database->query("SELECT id, $this->title AS title, $this->active AS active FROM $this->table ORDER BY $this->sort");
			if($results != null){
				foreach($results as $result){
					$isSelected = ($_GET['id'] == $result['id'] ? ' selected' : '');
			
					if($result['active'] == 0)
						$this->pom->append('vars', 'id', "<option style = \"color: #FF0000;\" value = \"$result[id]\"$isSelected>$result[title]</option>");
					else
						$this->pom->append('vars', 'id', "<option value = \"$result[id]\"$isSelected>$result[title]</option>");
				}
			}
		}

		private function dateToTimestamp($date){
			$date = explode('/', $date);
                	$date = mktime(0, 0, 0, $date[0], $date[1], $date[2]);

			return $date;
		}
	}
?>
