<?php
class Search {

	public function __construct()
	{
		
	}
	
	public function search_friends_module() {
		$_GET['q'] = (isset($_GET['q'])) ? $_GET['q'] : '';
		echo '<div id="search">
				<fieldset>
					<form name="search" method="get" action="">
						<input type="hidden" name="p" id="p" value="search"/>
						Cerca amici: <input class="ajaxsearch" type="text" name="q" id="q" value="'.$_GET['q'].'"/>
						<button type="submit">Cerca</button>
					</form>
				</fieldset>
			</div>
			';
	}
	
	public function search_friends($str) {
		$mod = new Db;
		$s = split(" ",$str);
		
		//friends in my city
		$where = ' ((nome LIKE "%'.$s[0].'%" AND cognome LIKE "%'.$s[1].'%") OR (nome LIKE "%'.$s[1].'%" AND cognome LIKE "%'.$s[0].'%")) AND (nome != "" AND cognome != "")';
		$where .= ' AND citta = "'.$_SESSION['user']->citta.'" ';
		$sql[] = $mod->query('SELECT * from social_users WHERE '.$where);

		//friends out of my city
		$where = ' ((nome LIKE "%'.$s[0].'%" AND cognome LIKE "%'.$s[1].'%") OR (nome LIKE "%'.$s[1].'%" AND cognome LIKE "%'.$s[0].'%")) AND (nome != "" AND cognome != "")';
		$where .= ' AND citta != "'.$_SESSION['user']->citta.'" ';
		$sql[] = $mod->query('SELECT * from social_users WHERE '.$where);
		return $sql;
	}	
	
		public function search_groups($str) {
		$mod = new Db;
		
		//friends in my city
		$where = ' (`group` LIKE "%'.$str.'%") AND (nome = "" AND cognome = "")';
		$sql = $mod->query('SELECT * from social_users WHERE '.$where.' ORDER BY `group` ASC');

		return $sql;
	}	

}



?>