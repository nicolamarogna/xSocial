<?php 
class Utils {
	
	public static function logged($location = '?p=login')
	{
		if ((!$_SESSION['user']) || ($_SESSION['user'] != true)) {
			return 0;
		} else {
			return 1;
		}
	}
	
	public static function logout()
	{
		session_unset();
		session_destroy();
		Utils::logged();
		header('Location: index.php');
		die;
	}
	
	public static function offline($xon, $url)
	{
		if (!$xon && !isset($_SESSION['id_area'])) {
			header('Location: '.$url);
			die;
		}
	}
	
	public static function set_mybase_url($base_url)
	{
		define('MYBASE_URL', $base_url);
	}
	
	public static function reset_url($str)
	{
		$search = array('href="./', 'href="/');
		$replace = array('href="'.ROOT, 'href="'.ROOT);
		$cleaned = str_replace($search, $replace, $str);
		return $cleaned;
	}
	
	public static function set_tpl($template, $theme_folder)
	{
		return (!empty($template) && file_exists(PATH.'themes/'.$theme_folder.'/templates/'.$template.'.php')) ? 
				$theme_folder.'/templates/'.$template : 
				$theme_folder.'/templates/base';
	}
	
	public static function set_msg($res, $ok = '<br />Operazione eseguita', $ko = '<br />E\' avvenuto un errore')
	{
		switch(gettype($res))
		{
			case 'boolean':
				$_SESSION['msg'] = ($res) ? $ok : $ko;
				break;
			case 'array':
				$_SESSION['msg'] = ($res[1] >= 0) ? $ok : $ko;
				break;
		}
	}
	
	public static function set_error($fields, $title = '_form_not_valid')
	{
		//$dict = new X4Dict_model(X4Route::$folder, X4Route::$lang);
		//$msg = $dict->get_word($title, 'form');
		foreach($fields as $i)
		{
			if (isset($i['error'])) {
				$msg .= '<br /><u>'.$i['label'].'</u> '.$i['error'];
			}
		}
		$_SESSION['msg'] = $msg;
	}
	
	public static function ReadArrayRecursive($arr) {
		if(!is_array($arr)) {die ('L\'argomento passato non &egrave; un array');}
			foreach ($arr as $k => $value) {
				if(is_array($value)) {
					Utils::ReadArrayRecursive($value);
				} else {
					//print_r($value);
					
				}
			return array($k,$value);
			}
	}
	
	private static function to7bit($text,$from_enc) {
		$text = mb_convert_encoding($text,'HTML-ENTITIES',$from_enc);
		$text = preg_replace(
			array('/&szlig;/','/&(..)lig;/',
				 '/&([aouAOU])uml;/','/&(.)[^;]*;/'),
			array('ss',"$1","$1".'e',"$1"),
			$text);
		return $text;
	} 
	
	public static function unspace($str)
	{
		//$str = Utils::to7bit($str, 'UTF-8');
		$str = strtolower(html_entity_decode($str));
		// strip special chars
		$str = preg_replace('/[àèéìòùç]+/e', '_', $str);
		// clean 
		return preg_replace('/[^a-z0-9_\/\.]+/', '_', $str);
	}
	
	public static function module($site, $page, $args, $module, $param)
	{
		$plug = new X4Plugin_model();
		if ($plug->exists($module, $page->id_area) && file_exists(PATH.'plugins/'.$module.'/'.$module.'.php')) {
			X4Core::auto_load($module.'/'.$module);
			eval('$m = new '.ucfirst($module).'($site);');
			return $m->get_module($page, $args, $param);
		}
		else {
			return '';
		}
	}
	
	public static function excerpt($str, $sep = '<!--pagebreak-->')
	{
		$s = str_replace(array('<p>'.$sep.'</p>', '<p>'.$sep, $sep.'</p>'), array($sep, $sep.'<p>', '</p>'.$sep), $str, $count);
		if ($count == 0) $s = str_replace($sep, '</p>'.$sep.'<p>', $s);
		$s = explode($sep, $s);
		return $s;
	}
	
	public static function navbar($array, $sep = ' > ')
	{
		$str = '';
		$item = array_pop($array);
		foreach($array as $k => $v)
		{
			$bc = (!is_int($k)) ? $k : $v;
			$str .= '<a href="'.BASE_URL.Core::$folder.'/'.strtolower($bc).'" title="'.$bc.'">'.$v.'</a>'.$sep;
		}
		$str .= $item;
		return $str;
	}
	
	public static function random_string($len)
	{
	   $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
				   'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
				   'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
				   'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
				   'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '1', '2',
				   '3', '4', '5', '6', '7', '8', '9');
	   
	   shuffle($chars);
	   $string = implode('', array_slice($chars, 0, $len));
	   return $string;
	}
	
	public static function currency($num, $value = '&euro;')
	{
		return number_format($num, 2, ',', '.').' '.$value;
	}
	
	public static function check_url($url)
	{
		if (substr($url, 0, 7) == 'http://') return $url;
		if (substr($url, 0, 4) == 'www.') return 'http://'.$url;
		return BASE_URL.$url;
	}
	
	public static function get_ordinal_value($ordinal, $token)
	{
		$a = explode('!', chunk_split(substr($ordinal, 1), 3, '!'));
		return base_convert($a[$token], 36, 10);
	}
	
		public static function get_level($id_who, $what, $id_what)
	{
		$priv = new User_model();
		$level = $priv->check_priv($id_who, $what, $id_what);
		return $level;
	}
	
	public static function chklevel($id_who, $what, $id_what, $value)
	{
		if ($_SESSION['level'] < 4) {
			$level = Utils::get_level($id_who, $what, $id_what);
			if ($level < $value) {
				header('Location: '.ROOT.X4Route::$area.'/msg/empty/msg/_not_permitted');
				die;
			}
		}
	}
	
	public static function get_type($filename) {
		//$images = array('.jpg', '.jpeg', '.gif', '.png');
		//$media = array('.swf');
		$path_parts = pathinfo($filename);

		switch($path_parts['extension']) {
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'png':
				return 0;
				break;
			case 'swf':
			case 'rm':
			case 'wmv':
			case 'mov':
			case 'mp4':
				return 2;
				break;
			case 'htm':
				return 3;
				break;
			default:
				return 1;
				break;
		}
	}
	
	public static function update_js_list($type) {
		$what = array('img', 'docs', 'media', 'template');
		
		// recreate js-list for tinymce
		$files = new Files_model();
		$array = $files->get_by_type($type);

		switch($what[$type]) {
			case 'img':
				$filename = 'image_list.js';
				$txt = '// images list'.NL.'var tinyMCEImageList = ['.NL.'// Name, URL, Description'.NL;
				break;
			case 'docs':
				$filename = 'link_list.js';
				$txt = '// link list'.NL.'var tinyMCELinkList = ['.NL.'// Name, URL, Description'.NL;
				break;
			case 'media':
				$filename = 'media_list.js';
				$txt = '// link list'.NL.'var tinyMCEMediaList = ['.NL.'// Name, URL, Description'.NL;
				break;
			case 'template':
				$filename = 'template_list.js';
				$txt = '// subtemplates list'.NL.'var tinyMCETemplateList = ['.NL.'// Name, URL, Description'.NL;
				break;
		}
		$txt .= Utils::write_js_array(BASE_URL.'files/'.$what[$type], $array);
		
		$txt .= NL.'];';
		$check = file_put_contents(ROOT.'plugin/'.$filename, $txt);
		return $check;
	}
	
	public static function write_js_array($path, $array, $comma = false) {
		// file header
		$txt = '';
		foreach ($array as $i) {
			if (!empty($txt)) $txt .= ',';
			$txt .= NL.'["'.$i->title.'", "'.$path.'/'.$i->name.'", "'.$i->title.'"]';
		}
		return $txt;
	}
	
	public static function updown($c, $n, $link)
	{
		if ($n > 1)	{
			switch($c)
			{
				case 1:
					// only down
					return '<a href="'.$link.'&move=1" title="Sposta gi&ugrave;"><img class="fleft" src="'.BASE_URL.'files/img_private/thumb_down.png"></a>';
					break;
				case ($n):
					// only up
					return '<a href="'.$link.'&move=-1" title="Sposta su"><img class="fleft" src="'.BASE_URL.'files/img_private/thumb_up.png"></a>';
					break;
				default:
					return '<a href="'.$link.'&move=-1" title="Sposta su"><img class="fleft" src="'.BASE_URL.'files/img_private/thumb_up.png"></a> <a href="'.$link.'&move=1" title="Sposta gi&ugrave;"><img class="fleft" src="'.BASE_URL.'files/img_private/thumb_down.png"></a>';
					break;
			}
		}
	}
	
	public static function get_max_xpos($tab, $id)
	{
		$db = new Db;
		// max pos into menu
		return $db->query('SELECT MAX(xpos) as xpos FROM '.$tab.' WHERE id_calendar = '.$id);
	}
	
	public static function move_page($id, $pos, $value, $id_calendar)
	{
		$db = new Db;
		// max pos into menu
		$sql = array();
		$sql[] = ('UPDATE social_calendar_items SET xpos = '.($pos).' WHERE xpos = '.($pos+$value).' AND id_calendar = '.$id_calendar);
		$sql[] = ('UPDATE social_calendar_items SET xpos = '.($pos+$value).' WHERE id = '.$id);
		
		return $db->multi_exec($sql);
	}
	
	public function reorder_xpos($maxpos, $xpos, $id_calendar)
	{
		$db = new Db;
		// reorder pos into menu
		for ($i=$xpos; $i<$maxpos; $i++) {
		  $sql = ('UPDATE social_calendar_items SET xpos = '.$i.' WHERE xpos = '.($i+1).' AND id_calendar = '.$id_calendar);
		 // echo $sql.'<br>';
		  $db->single_exec($sql);
		}
		return;
		
	}
	
	public static function upload($file, $path, $prefix = '', $zip = 0)
	{
		if (is_array($_FILES[$file]['name'])) {
			return Utils::upload_files($file, $path, $prefix, $zip);
		}
		else {
			if ($_FILES[$file]['name'] != '') {
				return Utils::upload_file($file, $path, $prefix, $zip);
			}
		}
	}
	
	private static function upload_file($file, $path, $prefix, $zip)
	{
		if (is_uploaded_file($_FILES[$file]['tmp_name'])) {
			//print_r($_FILES[$file]);die;
			$mime = array();
			$mime['img'] = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'video/mp4');
			//$mime['video'] = array('video/mp4');
			//$mime['media'] = array('video/quicktime', 'application/vnd.rn-realmedia', 'audio/x-pn-realaudio', 'application/x-shockwave-flash', 'video/x-ms-wmv');
			//$mime['docs'] = array('text/html', 'application/pdf', 'application/zip', 'text/plain', 'application/msword', 'application/vnd.ms-excel', 'audio/mpeg');

			//$type = 'img';

			foreach($mime as $k => $v) {
				if (in_array($_FILES[$file]['type'], $v)) $type = $k;
			}
			if ($type == 'img' && $path == ROOT.'files/') {
				// too big
				if ($_FILES[$file]['size'] > (MAX_IMG*1024)) { 
					header('Location: '.BASE_URL.'?p=msg&msg=filesize_too_big');
					die;
				}
				$imageinfo = getimagesize($_FILES[$file]['tmp_name']);
				// pixel dimensions
				if (!Utils::checkImageSize($imageinfo)) {
					header('Location: '.BASE_URL.'?p=msg&msg=image_size_too_big');
					die;
				}
			} else {
				// not supported
				header('Location: '.BASE_URL.'?p=msg&msg=file_not_supported');
				die;
			}

			// file name
			$tmpname = Utils::unspace(strtolower($prefix.$_FILES[$file]['name']));
			// exists?
			$name = Utils::get_final_name($path.$type.'/', $tmpname);
			
			// copy
			$check = Utils::copy_file($path.$type.'/', $name, $_FILES[$file]['tmp_name']);
			//print_r($path.$type);die;
			
			if ($check)	
				return $name;
			else 
				$_SESSION['msg'] = '<br>E\' avvenuto un errore';
				return false;
				die;
		}
		else 
			header('Location: '.BASE_URL.'msg/message/_upload_error');
			die;
	}
	
	private static function upload_files($file, $path, $prefix, $zip)
	{
		$names = array();
		$n = sizeof($_FILES[$file]['tmp_name']);
		$mime = array();
		$mime['img'] = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png');
		$mime['media'] = array('video/quicktime', 'application/vnd.rn-realmedia', 'audio/x-pn-realaudio', 'application/x-shockwave-flash', 'video/x-ms-wmv');
		$mime['template'] = array('text/html');
		for($i = 0; $i < $n; $i++) 
		{
			if (is_uploaded_file($_FILES[$file]['tmp_name'][$i])) {
				$type = 'files';
				
				foreach($mime as $k => $v) {
					if (in_array($_FILES[$file]['type'][$i], $v)) $type = $k;
				}
				
				if ($type == 'img' && $path == APATH.'files/') {
					// too big
					if ($_FILES[$file]['size'][$i] > (MAX_IMG*1024)) { 
						header('Location: '.BASE_URL.'msg/message/_file_size_is_too_big');
						die;
					}
					$imageinfo = getImageSize($_FILES[$file]['tmp_name'][$i]);
					// pixel dimensions
					if (!Utils::checkImageSize($imageinfo)) {
						header('Location: '.BASE_URL.'msg/message/_image_size_is_too_big');
						die;
					}
				}
				else {
					// too big
					if ($_FILES[$file]['size'][$i] > (MAX_DOC*1024) && $path == APATH.'files/') { 
						header('Location: '.BASE_URL.'msg/message/_file_size_is_too_big');
						die;
					}
				}
				
				// file name
				$tmpname = X4Utils::unspace(strtolower($prefix.$_FILES[$file]['name'][$i]));
				// exists?
				$name = Utils::get_final_name($path.$type.'/', $tmpname);
				// copy
				$check = Utils::copy_file($path.$type.'/', $name, $_FILES[$file]['tmp_name'][$i]);
				if ($check)	
					$names[] = $name;
				else {
					header('Location: '.BASE_URL.'msg/message/_upload_error');
					die;
				}
			}
			else {
				if (empty($names)) {
					header('Location: '.BASE_URL.'msg/message/_upload_error');
					die;
				}
			}
		}
		return $names;
	}
	
	private static function checkImageSize($size) {
		return ($size[0] <= MAX_W && $size[1] <= MAX_H) ? true : false;
	}
	
	public static function create_resized($src_img, $new_img, $sizes)
	{
		list($w, $h, $image_type) = getimagesize($src_img);
		if ($w > $sizes[0] || $h > $sizes[1]) {
			$nw = ($w > $sizes[0]) ? $sizes[0] : $w;
			$nh = ($h > $sizes[1]) ? $sizes[1] : $h;
			$ratio = $w/$h;
			
			if ($nw < $w) {
				// larga, ricavo h
				$nh = min($sizes[1], floor($nw/$ratio));
			}
			if ($nh < $h) {
				// alta, ricavo w
				$nw = $nh*$ratio;
			}
			// riduzione ricorsiva (quasi)
			while($nw > $w || $nh > $h) {
				if ($nw < $w) {
					// larga, ricavo h
					$nh = min($sizes[1], floor($nw/$ratio));
				}
				if ($nh < $h) {
					// alta, ricavo w
					$nw = $nh*$ratio;
				}
			}
			$tn = imagecreatetruecolor($nw, $nh);

			switch ($image_type)
			{
				case 1: $image = imagecreatefromgif($src_img); break;
				case 2: $image = imagecreatefromjpeg($src_img);  break;
				case 3: $image = imagecreatefrompng($src_img); break;
				default: $image = false; // trigger_error('File non supportato!', E_USER_WARNING);  break;
			}
			
			if ($image) {
				        
				imagecolortransparent($tn, imagecolorallocatealpha($tn, 0, 0, 0, 127));
				imagealphablending($tn, false);
				imagesavealpha($tn, true);
				
				imagecopyresampled($tn, $image, 0, 0, 0, 0, $nw, $nh, $w, $h);
				
				if (function_exists('imagefilter')) imagefilter($tn, IMG_FILTER_CONTRAST, -10);
				switch ($image_type)
				{
					case 1: imagegif($tn, $new_img, 100); break;
					case 2: imagejpeg($tn, $new_img, 100);  break;
					case 3: imagepng($tn, $new_img); break;
					//default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
				}
				
				return file_exists($new_img);
			}
			else return 0;
		
		}
		else return 0;
	}
	
	public static function create_crop($src_img, $new_img, $x, $y, $x2, $y2, $w, $h) {
		$jpeg_quality = 100;
		
		$img_r = imagecreatefromjpeg($src_img);
		$dst_r = imagecreatetruecolor($x2,$y2);
		$sizes = getimagesize($src_img);
				
		imagecopyresampled($dst_r,$img_r,0,0,$x,$y,$x2,$y2,$w, $h);

		header('Content-type: image/jpeg');
		imagejpeg($dst_r, $new_img, $jpeg_quality);

		return file_exists($new_img);
	}

	public static function get_final_name($path, $name) 
	{
		while (file_exists($path.$name)) {
			$token = explode('.', $name);
			$tok = explode('-', $token[0]);
			if (!isset($tok[1])) {
				// first
				$token[0] .= '-1';
			}
			else {
				// next
				$token[0] = $tok[0].'-'.strval(intval($tok[1]) + 1);
			}
			$name = implode('.', $token);
		}
		return $name;
	}

	public static function copy_file($path, $name, $obj) 
	{
		$check = move_uploaded_file($obj, $path.$name);
		if ($check) {
			chmod($path.$name, 0777);
		}
		return $check;
	}
	
	public static function clone_file($source) 
	{
		// exists?
		$name = Utils::get_final_name(ROOT.'files/img/', $source);
		$check = copy(ROOT.'files/img/'.$source, ROOT.'files/img/'.$name);
		
		if ($check) {
			chmod(ROOT.'files/img/'.$name, 0777);
		}
		return $name;
	}
	
	public static function del_file($path, $name) 
	{
		if (file_exists($path.$name)) {
			@chmod($path.$name, 0777);
			$check = @unlink($path.$name);
		}
	}
	
	public static function get_opt($array, $value, $name, $selected = '')
	{
		$opt = '';
		foreach($array as $i) {
			$sel = (!empty($selected) && $i->$value == $selected) ? SELECTED : '';
			$opt .= '<option value="'.$i->$value.'" '.$sel.'>' . $i->$name . '</option>';
		}
		return $opt;
	}
	
	public static function now()
	{
		return date('Y-m-d').' '.date('H:i:s');
	}
	
	//format the date
	public static function f_date($date, $ext = false)
	{
		$date = explode(' ',$date);
		$newdate = explode('-',$date[0]);
		$mesi = array('', 'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio',  'agosto', 'settembre', 'ottobre', 'novembre','dicembre');
		
		if ($ext) {
			if (substr($newdate[1],0,1) == '0') {
				$newdate[1] = str_replace('0', '', $newdate[1]);
			}
			return $newdate[2].' '.$mesi[$newdate[1]].' '.$newdate[0];
		} else {
			return $newdate[2].'-'.$newdate[1].'-'.$newdate[0].' '.$date[1];	
		}
	}
	
	public static function date_it($date)
	{
		$newdate = explode('-',$date);
		return $newdate[2].'-'.$newdate[1].'-'.$newdate[0];
	}
	
	public static function unescape($str)
	{
		return str_replace ('&quot;', '"', $str);
	}
	
	public static function youtube($url) {
		preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $match);
		if ($match) {
			/*echo '<object width="430" height="280"><param name="movie" value="http://www.youtube.com/v/'.$match[1].'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$match[1].'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="430" height="280"></embed></object>';*/
			echo '<a href="http://www.youtube.com/v/'.$match[1].'?hl=en&autoplay=1&showsearch=0&rel=0&TB_iframe=true&width=430&height=280&background=#000" class="fancybox-media"><img class="borded pad_all" src="http://img.youtube.com/vi/'.$match[1].'/1.jpg"></a><br><span class="xsmall">Youtube Video</span><img class="fleft padright" src="'.BASE_URL.'files/img_private/thumb_youtube.png">';
		}
	}
	
	public static function isfriend($id) {
		$mod = new Db();
		$friend = $mod->query('SELECT * FROM social_isfriend WHERE (id_user = '.$_SESSION['user']->id.' AND id_friend = '.$id.') OR (id_friend = '.$_SESSION['user']->id.' AND id_user = '.$id.')');
		if ($friend) {
			return ($friend[0]->confirmed == 1) ? 1 : 0;
		} else {
			return 2;	
		}
	}
	
	public static function isfriend_of_friend($id, $id_friend) {
		$mod = new Db();
		$friend = $mod->query('SELECT * FROM social_isfriend WHERE (id_user = '.$id.' AND id_friend = '.$id_friend.') OR (id_friend = '.$id.' AND id_user = '.$id_friend.')');
		if ($friend) {
			return ($friend[0]->confirmed == 1) ? 1 : 0;
		} else {
			return 2;	
		}
	}
	
	public static function isgroup($id) {
		$mod = new Db();
		$group = $mod->get_by_id('social_users', $id);
		if ($group->group) {
			return true;	
		}
	}
	/*
	public static function flipButton($title, $params, $msg = 'Sei sicuro?', $yes = 'Si', $no = 'No') {
		echo '<div class="btn">
			<div class="btn-back">
				<p>'.$msg.'</p>
				<button class="yes ajaxsubmit" '.$params.'>'.$yes.'</button>
				<button class="no" type="button">'.$no.'</button>
			</div>
			<div class="btn-front"><p>'.$title.'</p></div>
		</div>';
	}
	*/
	public static function ajaxLink($title, $params, $askConfirm = FALSE) {
		$askConfirm = ($askConfirm) ? ' askConfirm="true" ' : '';
		echo '<a class="ajaxsubmit" '.$params.$askConfirm.'>'.$title.'</a>';
	}
	
	public static function ajaxButton($title, $submit_action, $askConfirm = FALSE, $iconCSS = FALSE) {
		$askConfirm = ($askConfirm) ? ' askConfirm="true" ' : '';
		$icon = ($iconCSS) ? '<i class="'.$iconCSS.'" aria-hidden="true"></i> ' : '';
		echo '<button class="ajaxsubmit buttonGrey" type="submit" style="cursor:pointer;" '.$submit_action.$askConfirm.'>'.$icon.$title.'</button>';
	}
	
	public static function textToLink($text) {
  		$text = ereg_replace( "www\.", "http://www.", $text );
		// eliminate duplicates after force
		$text = ereg_replace( "http://http://www\.", "http://www.", $text );
		$text = ereg_replace( "https://http://www\.", "https://www.", $text );
	  
		// The Regular Expression filter
		$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
		// Check if there is a url in the text
		if(preg_match($reg_exUrl, $text, $url)) {
			   // make the urls hyper links
			   $text = preg_replace($reg_exUrl, '<a href=\''.$url[0].'\' target=\'_blank\'>'.$url[0].'</a>', $text);
		}   
		// if no urls in the text just return the text
		return ($text);
	}

	
}
?>