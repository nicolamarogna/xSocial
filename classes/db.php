<?php
class Db {
		
		public function __construct() {
			if (!defined('DATABASE')) define('DATABASE', 'hapasion_social'); //define('DATABASE', 'social');
			if (!defined('HOST'))define('HOST', '185.81.0.69'); //define('HOST', '62.149.150.110');
			if (!defined('DB_USER'))define('DB_USER', 'hapasion'); //define('DB_USER', 'Sql326497');
			if (!defined('DB_PASS'))define('DB_PASS', 'atetVadifdict3'); //define('DB_PASS', '5aab3ff5');
			$conn = mysql_connect(HOST, DB_USER, DB_PASS) or die("Errore nella connessione a MySql: " . mysql_error());
			mysql_select_db(DATABASE,$conn) or die("Errore nella selezione del db: " . mysql_error());
			mysql_query("SET NAMES 'utf8'") or die("Errore nella query: " . mysql_error());
		}
		
		// query
		public function query($sql = '') {
			$data = '';
			if ($sql == '') return FALSE;
			$ris = mysql_query($sql) or die("Errore nella query: " . mysql_error());
				while ($row = @mysql_fetch_object($ris)) {
					$data[] = $row;
				}
				return $data;
		}
	
		public function single_exec($sql) {
			if (empty($sql)) return false;
			return mysql_query($sql) or die("Errore nella query: " . mysql_error());
		}

		public function multi_exec($sql) {
			if (empty($sql) || !is_array($sql)) return false;
			foreach ($sql as $i) {
				$this->query($i);
			}
		}
		
		public function get_by_id($table, $id) {
			$data = $this->query('SELECT * FROM '.$table.' WHERE id = '.$id);
			if ($data) {
				return $data[0];
			}
		}
		
		public function get_by_type($table, $type) {
			return $this->query('SELECT * FROM '.$table.' WHERE type = "'.$type.'"');
		}
		
		public function get_by_const($const) {
			$ris = $this->query('SELECT what FROM settings WHERE const = "'.$const.'"');
			return $ris[0]->what;
		}

/**
	 * Insert a row in a table
	 *
	 * @return  array(rows affected, id row)
	 */
	public function insert($table, $array)
	{
		//if NOT multiple insert
		if (is_array($array[0])) {
			$array = array_shift($array);
		}
		
		// if multiple insert
		$field = $insert = '';
		foreach($array as $k => $v) {
			$field .= ', '.$k;
			$insert .= ', '.$this->escape($v);
		}
		//echo 'INSERT INTO '.$this->table.' (updated '.$field.') VALUES (NOW() '.$insert.')';die;
		$sql = $this->single_exec('INSERT INTO '.$table.' (updated '.$field.') VALUES (NOW() '.$insert.')');
		return $sql;
	}
	
	/**
	 * Update a row in a table
	 *
	 * @return  array(rows affected, id row)
	 */
	public function update($table, $id, $array)
	{
		//if NOT multiple insert
		if (is_array($array[0])) {
			$array = array_shift($array);
		}
		
		// if multiple insert
		$update = '';
		foreach($array as $k => $v) {
			$update .= ', '.$k.' = '.$this->escape($v);
		}
		//echo 'UPDATE '.$this->table.' SET updated = NOW() '.$update.' WHERE id = '.intval($id);die;
		$sql = $this->single_exec('UPDATE '.$table.' SET updated = NOW() '.$update.' WHERE id = '.intval($id));
		return $sql;
	}
	
	/**
	 * Delete a row from a table
	 *
	 * @return  array(rows affected, id row)
	 */
	public function delete($table, $id)
	{
		
		// delete file
		$file = $this->get_by_id($table, $id);
		
		$result = $this->single_exec('DELETE FROM '.$table.' WHERE id = '.intval($id));
		
		/*if ($result && file_exists(ROOT.'files/img/'.$file->img))
		{
			chmod(ROOT.'files/img/'.$file->img, 0766);
			unlink(ROOT.'files/img/'.$file->img);
		}*/
		return $result;
	}
		
	/**
	 * Escapes a value for a query.
	 *
	 * @param   mixed   value to escape
	 * @return  string
	 */
	public function escape($value)
	{
		$convert_quotes = str_replace ( '"', '&quot;', $value );
		if (strpos($value, 'nostriptag') !== false) {
			$escaped_string = '"'.mysql_real_escape_string($convert_quotes).'"';
		} else {
			$escaped_string = '"'.strip_tags(mysql_real_escape_string($convert_quotes), '<a>').'"';
		}
		return $escaped_string;
	}	
	
	public function notify($from_user, $to_user, $what, $id_what)
	{
		if (($from_user != $to_user) && (!Utils::isgroup($to_user))) {
			//$what = 'msg';
			$post[] = array(
						'from_user' => $from_user,
						'to_user' => $to_user,
						'what' => $what,
						'id_what' => $id_what,
						);
			DB::insert('social_notify', $post);
		}
		if (($from_user != $to_user) && (Utils::isgroup($to_user))) {
			$mod = new Db;
			$friends = $mod->query('SELECT * FROM social_isfriend WHERE (id_user = '.$to_user.' OR id_friend = '.$to_user.') AND confirmed = 1');
			if ($friends) {
				if ($what == 'msg') { $what = 'msg_group'; }
				//$what = 'msg_group';
				foreach ($friends as $i) {
					if (($i->id_user != $_SESSION['user']->id) && ($i->from_user != $_SESSION['user']->id)) {
						if ($i->id_user == $to_user) {
							$to_user = $i->id_friend;
						} else {
							$to_user = $i->id_user;
						}
						$post[] = array(
								'from_user' => $from_user,
								'to_user' => $to_user,
								'what' => $what,
								'id_what' => $id_what,
								);
					DB::insert('social_notify', $post);
					}
				}
			}
		}
	}
	
		
}

?>