<?php 
class Form {
	
	public static $exclude = array('paragraph', 'fileprogress', 'img');
	
	public static function doform($name, $action, $fields = array(), $buttons = array('Cacella', 'Invia'), $method = 'post', $extra = '', $submit_action = '', $reset_action = '')
	{
		// extra can be enctype="multipart/form-data"
		
		//if (!empty($submit_action) ) $extra .= ' onsubmit="return false"';
		
		$str = '<form id="'.$name.'"  name="'.$name.'" action="'.$action.'" method="'.$method.'" '.$extra.'>
		<fieldset>';
		foreach($fields as $i)
		{
			if (!is_null($i['label'])) {
				$req = (isset($i['rule']) && strstr($i['rule'], 'required') != '') ? ' *' : '';
				$err = (isset($i['error'])) ? ' class="error"' : '';
				$str .= '
				<label for="'.$i['name'].'" '.$err.'>'.$i['label'].$req.'</label>';
			}
			
			switch($i['type'])
			{
			case 'loading':
				$str .= '<div id="loading" style="visibility:hidden;"><img src="'.ROOT.'files/ajax-loader.gif" alt="Loading..." /></div>';
				break;
			case 'paragraph':
				$str .= '<h4>'.$i['value'].'</h4>';
				break;
			case 'img':
				$str .= $i['value'];
				break;
			case 'hidden':
				$str .= '
				<input type="hidden" name="'.$i['name'].'" id="'.$i['name'].'" value="'.$i['value'].'" />';
				break;
			case 'text':
				$iextra = (isset($i['extra'])) ? $i['extra'] : '';
				if ($i['name'] == 'captcha') $i['value'] = '';
				$str .= '
				<input type="text" name="'.$i['name'].'" id="'.$i['name'].'" value="'.$i['value'].'" '.$iextra.' />';
				break;
			case 'file':
				$str .= '
				<input type="file" name="'.$i['name'].'" id="'.$i['name'].'" value="'.$i['value'].'" />';
				if (isset($i['old']) && !empty($i['old'])) {
					$imgdir = (isset($i['imgdir']) && !empty($i['imgdir'])) ? $i['imgdir'] : $i['name'];
					$slb = ($imgdir != '') && ($imgdir == 'img') ? ' class="fancybox" ' : ' target="_blank" ';
					$img = ($imgdir == 'img') ? '<img class="thumb" src="'.BASE_URL.'files/'.$imgdir.'/'.$i['old'].'" alt="thumb" />' : '';
					$str .= '<p class="xsmall"><a href="'.BASE_URL.'files/'.$imgdir.'/'.$i['old'].'" title="" '.$slb.'>'.$img.'</a>Valore attuale: <a href="'.BASE_URL.'files/'.$imgdir.'/'.$i['old'].'" title="" '.$slb.'>'.$i['old'].'</a><input type="hidden" name="old_'.$i['name'].'" id="old_'.$i['name'].'" value="'.$i['old'].'" /></p>';
				}
				if (isset($i['del_img']) && !empty($i['del_img'])) {
					$str .= '
					<p class="xsmall">
					<input type="checkbox" name="del_img" id="del_img" value="1" />
					Elimina immagine
					</p><br>
					';
				}
				break;
			case 'file_u_pop':
				$iextra = (isset($i['extra'])) ? $i['extra'] : '';
				$str_u_pop = '<button type="button" '.$iextra.' item="load_photo" style="cursor:pointer;"><i class="fa fa-camera" aria-hidden="true"></i> Carica foto/video</button>';
				$u_pops = '<div id="load_photo" style="display:none" class="checkIfEmpty">
				<div id="hideField"><span class="bold">Immagine:</span> <input type="file" name="'.$i['name'].'" id="'.$i['name'].'" value="'.$i['value'].'" /></div>';
				if (isset($i['old']) && !empty($i['old'])) {
					$imgdir = (isset($i['imgdir']) && !empty($i['imgdir'])) ? $i['imgdir'] : $i['name'];
					$slb = ($imgdir != '') && ($imgdir == 'img') ? ' class="fancybox" ' : ' target="_blank" ';
					$img = ($imgdir == 'img') ? '<img class="thumb" src="'.BASE_URL.'files/'.$imgdir.'/'.$i['old'].'" alt="thumb" />' : '';
					$u_pops .= '<p class="xsmall">'.$img.'Valore attuale: <a href="'.BASE_URL.'files/'.$imgdir.'/'.$i['old'].'" title="" '.$slb.'>'.$i['old'].'</a><input type="hidden" name="old_'.$i['name'].'" id="old_'.$i['name'].'" value="'.$i['old'].'" /></p>
					';
				}
				$u_pops .= '</div>';
				break;
			case 'youtube_u_pop':
				$iextra = (isset($i['extra'])) ? $i['extra'] : '';
				$str_u_pop .= '<button type="button" '.$iextra.' item="load_youtube" style="cursor:pointer;"><i class="fa fa-youtube-play" aria-hidden="true"></i> Carica video</button>';
				$u_pops .= '<div id="load_youtube" item="hide" class="checkIfEmpty">
				<span class="bold">Link youtube:</span> <input type="text" name="'.$i['name'].'" id="'.$i['name'].'" value="'.$i['value'].'" />
				</div>';
				break;
			case 'password':
				$iextra = (isset($i['extra'])) ? $i['extra'] : '';
				$str .= '
				<input type="password" name="'.$i['name'].'" id="'.$i['name'].'" value="'.$i['value'].'" '.$iextra.' />';
				break;
			case 'checkbox':
				$checked = (isset($i['checked']) && intval($i['checked']) > 0) ? 'checked="checked"' : '';
				$str .= '
				<input type="checkbox" class="checked" name="'.$i['name'].'" id="'.$i['name'].'" value="'.$i['value'].'" '.$checked.' />';
				break;
			case 'radio':
				foreach($i['options'] as $k => $v) {
					$checked = ($i['value'] == $k) ? 'checked="checked"' : '';
					$str .= '
					<input type="radio" class="checked" name="'.$i['name'].'" id="'.$i['name'].'" value="'.$k.'" '.$checked.' /> '.$v;
				}
				break;
			case 'textarea':
				$textra = (isset($i['extra'])) ? $i['extra'] : '';
				$str .= '
				<textarea cols="" rows="" name="'.$i['name'].'" id="'.$i['name'].'" '.$textra.'>'.$i['value'].'</textarea>';
				break;
			case 'textarea_liveurl':
				$textra = (isset($i['extra'])) ? $i['extra'] : '';
				$str .= '
				<textarea cols="" rows="" name="'.$i['name'].'" id="'.$i['name'].'" '.$textra.'>'.$i['value'].'</textarea>';
				$str .= '<div class="liveurl-loader"></div>
					<div class="liveurl">
						<div class="close" title="Chiudi"></div>
						<div class="inner">
							<div class="image"> </div>
							<div class="details">
								<div class="info">
									<div class="title"> </div>
									<div class="description"> </div> 
									<div class="url"> </div>
								</div>
			
								<div class="thumbnail">
									<div class="pictures">
										<div class="controls">
											<div class="prev button inactive"></div>
											<div class="next button inactive"></div>
											<div class="count">
												<span class="current">0</span><span> di </span><span class="max">0</span>
											</div>
										</div>
									</div>
								</div>
								<div class="video"></div>
							</div>
						</div>
					</div>';
				break;
			case 'select':
				if (isset($i['multiple']))
					$str .= '
					<select name="'.$i['name'].'[]" id="'.$i['name'].'" multiple="multiple" size="8">';
				else 
					$str .= '
					<select name="'.$i['name'].'" id="'.$i['name'].'">';
				// empty option
				if (isset($i['options'][3]))
					$str .= '
					<option>'.$i['options'][3].'</option>';
				// other options
				if (!empty($i['options'][0])) {
					foreach($i['options'][0] as $ii)
					{
						$sign = $dis = ' ';
						if (isset($i['disabled'])) {
							if ($ii->$i['disabled']) {
								$dis = ' disabled = "disabled"';
								$sign = 'x';
							}
						}
						
						if (is_array($i['value']))
							$sel = (in_array($ii->$i['options'][1], $i['value'])) ? 'selected="selected"' : '';
						else
							$sel = ($i['value'] == $ii->$i['options'][1]) ? 'selected="selected"' : '';
							$str .= '
						<option value="'.$ii->$i['options'][1].'" '.$sel.$dis.'>'.$sign.' '.$ii->$i['options'][2].'</option>';
					}
				}
				$str .= '
				</select>';
				break;
			case 'selectarray':
				if (isset($i['multiple']))
					$str .= '
					<select name="'.$i['name'].'[]" id="'.$i['name'].'" multiple="multiple" size="8">';
				else 
					$str .= '
					<select name="'.$i['name'].'" id="'.$i['name'].'">';
				// empty option
				if (isset($i['options'][3]))
					$str .= '
					<option>'.$i['options'][3].'</option>';
				// other options
				if (!empty($i['options'][0])) {
					foreach($i['options'][0] as $ii)
					{
						$sign = $dis = ' ';
						if (isset($i['disabled'])) {
							if ($ii->$i['disabled']) {
								$dis = ' disabled = "disabled"';
								$sign = 'x';
							}
						}
						
						if (is_array($i['value']))
							$sel = (in_array($ii->$i['options'][1], $i['value'])) ? 'selected="selected"' : '';
						else
							$sel = ($i['value'] == $ii) ? 'selected="selected"' : '';
							$str .= '
						<option value="'.$ii.'" '.$sel.$dis.'>'.$sign.' '.$ii.'</option>';
					}
				}
				$str .= '
				</select>';
				break;
			}
			if (isset($i['suggestion'])) $str .= '&nbsp;<span class="xsmall">&nbsp;('.$i['suggestion'].')</span>';
		}
		$reset = $submit = '';
		if (!is_null($buttons[0])) $reset = (empty($reset_action)) ? '<button type="reset">'.$buttons[0].'</button>' : '<button type="button" '.$reset_action.'>'.$buttons[0].'</button>';
		if (!is_null($buttons[1])) $submit = (empty($submit_action)) ? '<button name="'.strrev($name).'" type="submit">'.$buttons[1].'</button>' : '<button name="'.strrev($name).'" type="submit" '.$submit_action.'>'.$buttons[1].'</button>';
					
		$str .= '<div class="clear">'.$u_pops.'</div>';
		$str .= '<div class="clear"></div><div class="fright" style="width:auto;">'.$reset.$submit.'</div><div class="fleft">'.$str_u_pop.'</div>';
		$str .= '</fieldset>
			</form>';
		return $str;
	}
	
	public static function validation(&$fields)
	{
		$e = true;
		$n = sizeof($fields);
		for($i = 0; $i < $n; $i++)
		{
			// check errors
			if (isset($fields[$i]['rule']))
			{
				$token = explode('|', $fields[$i]['rule']);
				foreach($token as $ii)
				{
					$tok = explode('-', $ii);
					if ($tok[0] == 'onlyif' && !isset($_POST[$tok[1]])) break;
					
					switch($tok[0])
					{
					case 'required':
						if ($fields[$i]['type'] == 'file') {
							if (is_array($_FILES[$fields[$i]['name']])) {
								if ($_FILES[$fields[$i]['name']]['tmp_name'][0] == '' || strlen($_FILES[$fields[$i]['name']]['name'][0]) == 0) {
									$fields[$i]['error'] = '&egrave; un campo obbligatorio';
									$e = false;
								}
							}
							else if ($_FILES[$fields[$i]['name']]['tmp_name'] == '' || strlen($_FILES[$fields[$i]['name']]['name']) == 0) {
								$fields[$i]['error'] = '&egrave; un campo obbligatorio';
								$e = false;
							}
						}
						else {
							if (!isset($_POST[$fields[$i]['name']]) || strlen($_POST[$fields[$i]['name']]) == 0) {
								$fields[$i]['error'] = '&egrave; un campo obbligatorio';
								$e = false;
							}
						}
						break;
					case 'noempty':
						if (strlen($_POST[$fields[$i]['name']]) == 0) {
							$fields[$i]['error'] = '&egrave; un campo vuoto';
							$e = false;
						}
						break;
					case 'mail':
						$mail = strtolower(trim($_POST[$fields[$i]['name']]));
						if (!empty($mail) && !Checker::check_email($mail)) {
							$fields[$i]['error'] = 'non &egrave; un indirizzo email valido';
								$e = false;
						}
						break;
					case 'url':
						$url = strtolower(trim($_POST[$fields[$i]['name']]));
						if (!empty($url) && !Checker::check_url($url)) {
							$fields[$i]['error'] = 'non &egrave; un url valido';
								$e = false;
						}
						break;
					case 'phone':
						if (!preg_match('/^([0-9]*)+([\s|\-\/]([0-9])*)?$/', $_POST[$fields[$i]['name']])) {
							$fields[$i]['error'] = 'deve contenere solo numeri';
							$e = false;
						}
						break;
					case 'depends':
						if (!empty($_POST[$fields[$i]['name']])) {
							if (strlen($_POST[$tok[1]]) == 0) {
								$fields[$i]['error'] = '_depends';
								$e = false;
							}
						}
						break;
					case 'inarray':
						if (!empty($_POST[$fields[$i]['name']])) {
							if (!in_array($_POST[$fields[$i]['name']], $_POST[$tok[1]])) {
								$fields[$i]['error'] = '_inarray';
								$e = false;
							}
						}
						break;
					case 'minlength':
						$len = strlen($_POST[$fields[$i]['name']]);
						if ($len > 0 && $len < $tok[1]) {
							$fields[$i]['error'] = '&egrave; troppo corto';
							$e = false;
						}
						break;
					case 'maxlength':
						$len = strlen($_POST[$fields[$i]['name']]);
						if ($len > 0 && $len > $tok[1]) {
							$fields[$i]['error'] = '&egrave; troppo lungo';
							$e = false;
						}
						break;
					case 'equal':
						if ($_POST[$fields[$i]['name']] != $_POST[$tok[1]]) {
							$fields[$i]['error'] = 'non corrisponde';
							$e = false;
						}
						break;
					case 'alphanumeric':
						if (!preg_match('/^([a-zA-Z0-9._-]*)$/', $_POST[$fields[$i]['name']])) {
							$fields[$i]['error'] = 'deve essere un campo alfanumerico';
							$e = false;
						}
						break;
					case 'numeric':
						if (!preg_match('/^(|\-)+([0-9])+([\.|,]([0-9])*)?$/', $_POST[$fields[$i]['name']])) {
							$fields[$i]['error'] = 'deve essere un campo numerico';
							$e = false;
						}
						break;
					case 'date':
						if (!empty($_POST[$fields[$i]['name']]) && $_POST[$fields[$i]['name']] != '0000-00-00' ) {
							if(preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $_POST[$fields[$i]['name']])) {
								list($a, $m, $g) = explode('-', $_POST[$fields[$i]['name']]);
								if (!checkdate($m, $g, $a)) {
									$fields[$i]['error'] = 'deve essere una data';
									$e = false;
								}
							}
							else {
								$fields[$i]['error'] = 'deve essere una data';
								$e = false;
							}
						}
						break;
					case 'time':
						if (!empty($_POST[$fields[$i]['name']])) {
							if(!preg_match('/^([01][0-9]|2[0-3]):([0-5][0-9])$/', $_POST[$fields[$i]['name']])) {
								$fields[$i]['error'] = 'deve essere un\'ora';
								$e = false;
							}
						}
						break;
					case 'datetime':
						echo $_POST[$fields[$i]['name']];
						if (!empty($_POST[$fields[$i]['name']]) && !Checker::isDateTime($_POST[$fields[$i]['name']])) {
							$fields[$i]['error'] = 'data non valida';
							$e = false;
						}
						break;
					case 'captcha':
						if (!empty($_POST[$fields[$i]['name']]) && $_POST[$fields[$i]['name']] != $_SESSION['captcha']) {
							$fields[$i]['error'] = 'errore captcha';
							$e = false;
						}
						break;
					case 'fiscal':
						if (!empty($_POST[$fields[$i]['name']])) {
							switch(strlen($_POST[$fields[$i]['name']]))
							{
								case 11:
									if (!X4Checker::isPIVA($_POST[$fields[$i]['name']])) {
										$fields[$i]['error'] = 'non &egrave; una Partita IVA valida';
										$e = false;
									}
									break;
								case 16:
									if (!X4Checker::isCF($_POST[$fields[$i]['name']])) {
										$fields[$i]['error'] = 'non &egrave; un codice fiscale valido';
										$e = false;
									}
									break;
								default:
									$fields[$i]['error'] = 'non &egrave; un codice fiscale valido';
									$e = false;
									break;
							}
						}
						break;
					}
				}
			}
			
			// put value
			if (!in_array($fields[$i]['type'], Form::$exclude) && (isset($_POST[$fields[$i]['name']]) && !empty($_POST[$fields[$i]['name']]))) 
				$fields[$i]['value'] = $_POST[$fields[$i]['name']];
		}
		return $e;
	}
	
	public static function get_form($fields = array())
	{
		$elements = array();
		foreach($fields as $i)
		{
			if (!is_null($i['label'])) 	{
				$req = (isset($i['rule']) && strstr($i['rule'], 'required') != '') ? ' *' : '';
				$err = (isset($i['error'])) ? ' class="error"' : '';
				$lbl = '
				<label for="'.$i['name'].'" '.$err.'>'.$i['label'].$req;
			}
			else {
				$lbl = '';
			}
			
			switch($i['type'])
			{
				case 'select':
					$opt = '';
					// empty option
					if (isset($i['options'][3])) {
						$opt .= '
						<option value="'.$i['options'][3][0].'">'.$i['options'][3][1].'</option>';
					}
					// other options
					if (!empty($i['options'][0])) {
						foreach($i['options'][0] as $ii)
						{
							$sel = ($i['value'] == $ii->$i['options'][1]) ? 'selected="selected"' : '';
							$opt .= '
							<option value="'.$ii->$i['options'][1].'" '.$sel.'>'.$ii->$i['options'][2].'</option>';
						}
					}
					$elements[$i['name']] = array(
						'label' => $lbl,
						'value' => $i['value'],
						'options' => $opt
						);
					break;
				default:
					$elements[$i['name']] = array(
						'label' => $lbl,
						'value' => $i['value']
						);
					break;
			}
		}
		return $elements;
	}	
}
?>
