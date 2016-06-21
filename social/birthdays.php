<?php
	$mod = new Birthdays;
	$db = new Db;

	echo '<div id="head_under"><img class="fright" src="files/img_private/thumb_birthday.png">Compleanni di oggi</div>';
	echo '<div id="results"><table>';
	$bday = $mod->birthdays(date('m-d'));
	if ($bday) {
		foreach ($bday as $k => $i) {
			foreach ($i as $ii) {
				if ($ii->img) {
					$img = 'files/img/thumb_'.$ii->img;
				} else {
					$img = 'files/img_private/thumb_img_profile_null.jpg';
				}
				$years_old = date('Y') - substr($ii->birthday, 0, 4);
				echo '<tr><td width="50"><a href="?userboard='.$ii->id.'">
			<img src="'.$img.'" title="'.stripslashes($ii->nome).' '.stripslashes($ii->cognome).'">
			</a>
			</td>
			<td>
			<a href="?userboard='.$ii->id.'" class="bold">'.stripslashes($ii->nome).' '.stripslashes($ii->cognome).'</a><br><span class="xsmall">compie '.$years_old.' anni</span>
			</td></tr>';
			}
		}
	} else {
			echo 'Nessun compleanno.';
	}
	echo '</table>
		</div>
		<br>';
	


	echo '<div id="head_under"><img class="fright" src="files/img_private/thumb_birthday.png">Compleanni di domani</div>';
	echo '<div id="results"><table>';
	$bday = $mod->birthdays(date('m-d', mktime(0,0,0,date('m'), date('d')+1)));
	if ($bday) {
		foreach ($bday as $k => $i) {
			foreach ($i as $ii) {
				if ($ii->img) {
					$img = 'files/img/thumb_'.$ii->img;
				} else {
					$img = 'files/img_private/thumb_img_profile_null.jpg';
				}
				$years_old = date('Y') - substr($ii->birthday, 0, 4);
				echo '<tr><td width="50"><a href="?userboard='.$ii->id.'">
			<img src="'.$img.'" title="'.stripslashes($ii->nome).' '.stripslashes($ii->cognome).'">
			</a>
			</td>
			<td>
			<a href="?userboard='.$ii->id.'" class="bold">'.stripslashes($ii->nome).' '.stripslashes($ii->cognome).'</a><br><span class="xsmall">compie '.$years_old.' anni</span>
			</td></tr>';
			}
		}
	} else {
			echo 'Nessun compleanno.';
	}
	echo '</table>
		</div>
		<br>';
		
		
	echo '<div id="head_under"><img class="fright" src="files/img_private/thumb_birthday.png">Prossimi compleanni</div>';
	echo '<div id="results"><table>';
	$bday = $mod->birthdays('prossimi');
	if ($bday) {
		foreach ($bday as $k => $i) {
			foreach ($i as $ii) {
				if ($ii->img) {
					$img = 'files/img/thumb_'.$ii->img;
				} else {
					$img = 'files/img_private/thumb_img_profile_null.jpg';
				}
				$years_old = '';
				$years_old = date('Y') - substr($ii->birthday, 0, 4);
				if (date('Y-m-d') > date('Y').'-'.substr($ii->birthday, 5, 5)) {
					$years_old++;
				}
				echo '<tr><td width="50"><a href="?userboard='.$ii->id.'">
			<img src="'.$img.'" title="'.stripslashes($ii->nome).' '.stripslashes($ii->cognome).'">
			</a>
			</td>
			<td>
			<a href="?userboard='.$ii->id.'" class="bold">'.stripslashes($ii->nome).' '.stripslashes($ii->cognome).'</a><br><span class="xsmall">compie '.$years_old.' anni il '.substr(Utils::f_date($ii->birthday), 0, 5).'</span>
			</td></tr>';
			}
		}
	} else {
			echo 'Nessun compleanno.';
	}
	echo '</table>
		</div>
		<br>';


?>