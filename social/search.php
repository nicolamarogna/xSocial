<?php
	if ($_GET['type'] != 'groups') {
		$title = '<img class="fright" src="files/img_private/thumb_friends.png">Cerca amici';
		$split = '<a class="bold" href="?p=search&type=groups&q='.$_GET['q'].'">Cerca fra i gruppi</a>';
	} else {
		$title = '<img class="fright" src="files/img_private/thumb_group.png">Cerca gruppi';
		$split = '<a class="bold" href="?p=search&&q='.$_GET['q'].'">Cerca fra gli amici</a>';
	}
	echo '<div id="head_under">'.$title.'</div>';
	echo '<div id="menu">
				<table><tr><td class="aright">
				'.$split.'
				</td></tr></table>
				</div><br>';
	if ($_GET['type'] != 'groups') {
		if ($_GET['q']) {
			echo '<div id="navbar">Amici nella tua citt&agrave;</div>';
			$mod = new Search;
			$check = $mod->search_friends($_GET['q']);
			//check friends in user city
			if ($check[0]) {
				foreach ($check[0] as $i) {
					$img = ($i->img) ? 'files/img/thumb_'.$i->img : 'files/img_private/thumb_img_profile_null.jpg'; 
					echo '<div id="menu"><table><tr><td width="50">
						<a href="?userboard='.$i->id.'">
						<img src="'.$img.'" title="'.stripslashes($i->nome).' '.stripslashes($i->nome).'">
						</a>
						</td>
						<td>
						<a href="?userboard='.$i->id.'" class="bold">'.stripslashes($i->nome).' '.stripslashes($i->cognome).'</a> ('.$i->citta.')
						</td>
						</tr></table>
						</div>
					';
				}
			} else {
				echo 'Nessuna persona trovata nella tua citt&agrave; con questi termini di ricerca.';
			}
			
			//check friends in other cities
			echo '<br><div id="navbar">Amici fuori dalla tua citt&agrave;</div>';
			if ($check[1]) {
				foreach ($check[1] as $i) {
					$img = ($i->img) ? 'files/img/thumb_'.$i->img : 'files/img_private/thumb_img_profile_null.jpg'; 
					echo '<div id="menu"><table><tr><td width="50">
						<a href="?userboard='.$i->id.'">
						<img src="'.$img.'" title="'.stripslashes($i->nome).' '.stripslashes($i->nome).'">
						</a>
						</td>
						<td>
						<a href="?userboard='.$i->id.'" class="bold">'.stripslashes($i->nome).' '.stripslashes($i->cognome).'</a> ('.$i->citta.')
						</td>
						</tr></table>
						</div>
					';
				}
			} else {
				echo 'Nessuna persona trovata fuori dalla tua citt&agrave;.';
			}
		} else {
			echo 'Nessuna corrispondenza trovata.';
		}
	
	
	
	} else {
		
		
		
		if ($_GET['q']) {
			$mod = new Search;
			$db = new Db;
			$check = $mod->search_groups($_GET['q']);
			//check friends in user city
			if ($check) {
				foreach ($check as $i) {
					$admin = $db->get_by_id('social_users', $i->group_admin);
					$img = ($i->img) ? 'files/img/thumb_'.$i->img : 'files/img_private/thumb_img_profile_null.jpg'; 
					echo '<div id="menu"><table><tr><td width="50">
						<a href="?userboard='.$i->id.'">
						<img src="'.$img.'">
						</a>
						</td>
						<td>
						<a href="?userboard='.$i->id.'" class="bold">'.stripslashes($i->group).'</a><br>(Amministratore: <a href="?userboard='.$admin->id.'">'.stripslashes($admin->nome).' '.stripslashes($admin->cognome).'</a>)
						</td>
						</tr></table>
						</div>
					';
				}
			} else {
				echo 'Nessuna gruppo trovato con questi termini di ricerca.';
			}
		} else {
			echo 'Nessuna corrispondenza trovata.';
		}	
	}
?>