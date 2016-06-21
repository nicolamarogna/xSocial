<?php 
class Checker {
	
	public static function isDateTime($dateTime)
	{
		if (preg_match('/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/', $dateTime, $matches)) {
			if (checkdate($matches[2], $matches[3], $matches[1])) {
				return true;
			}
		}
		return false;
	}
	
	public static function isPIVA($pi){
	    
	    if( ! ereg("^[0-9]+$", $pi) )
		return false;
	    $s = 0;
	    for( $i = 0; $i <= 9; $i += 2 )
		$s += ord($pi[$i]) - ord('0');
	    for( $i = 1; $i <= 9; $i += 2 ){
		$c = 2*( ord($pi[$i]) - ord('0') );
		if( $c > 9 )  $c = $c - 9;
		$s += $c;
	    }
	    if( ( 10 - $s%10 )%10 != ord($pi[10]) - ord('0') )
		return false;
	    return true;
	}
	
	public static function isCF($cf){
		
		$cf = strtoupper($cf);
		if( ! ereg("^[A-Z0-9]+$", $cf) ){
			return false;
		}
		$s = 0;
		for( $i = 1; $i <= 13; $i += 2 ){
		$c = $cf[$i];
		if( '0' <= $c && $c <= '9' )
			$s += ord($c) - ord('0');
		else
			$s += ord($c) - ord('A');
		}
		for( $i = 0; $i <= 14; $i += 2 ){
			$c = $cf[$i];
			switch( $c ){
				case '0':  $s += 1;  break;
				case '1':  $s += 0;  break;
				case '2':  $s += 5;  break;
				case '3':  $s += 7;  break;
				case '4':  $s += 9;  break;
				case '5':  $s += 13;  break;
				case '6':  $s += 15;  break;
				case '7':  $s += 17;  break;
				case '8':  $s += 19;  break;
				case '9':  $s += 21;  break;
				case 'A':  $s += 1;  break;
				case 'B':  $s += 0;  break;
				case 'C':  $s += 5;  break;
				case 'D':  $s += 7;  break;
				case 'E':  $s += 9;  break;
				case 'F':  $s += 13;  break;
				case 'G':  $s += 15;  break;
				case 'H':  $s += 17;  break;
				case 'I':  $s += 19;  break;
				case 'J':  $s += 21;  break;
				case 'K':  $s += 2;  break;
				case 'L':  $s += 4;  break;
				case 'M':  $s += 18;  break;
				case 'N':  $s += 20;  break;
				case 'O':  $s += 11;  break;
				case 'P':  $s += 3;  break;
				case 'Q':  $s += 6;  break;
				case 'R':  $s += 8;  break;
				case 'S':  $s += 12;  break;
				case 'T':  $s += 14;  break;
				case 'U':  $s += 16;  break;
				case 'V':  $s += 10;  break;
				case 'W':  $s += 22;  break;
				case 'X':  $s += 25;  break;
				case 'Y':  $s += 24;  break;
				case 'Z':  $s += 23;  break;
			}
		}
		if( chr($s%26 + ord('A')) != $cf[15] )
		return false;
		return true;
	}
	
	private static function win_checkdnsrr($host, $type='MX') {
		if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') { return; }
		if (empty($host)) { return; }
		$types=array('A', 'MX', 'NS', 'SOA', 'PTR', 'CNAME', 'AAAA', 'A6', 'SRV', 'NAPTR', 'TXT', 'ANY');
		if (!in_array($type,$types)) {
			user_error("checkdnsrr() Type '$type' not supported", E_USER_WARNING);
			return;
		}
		@exec('nslookup -type='.$type.' '.escapeshellcmd($host), $output);
		foreach($output as $line)
		{
			if (preg_match('/^'.$host.'/',$line)) { return true; }
		}
	}
	
	public static function check_email($mail) {
		$pattern = "/^[\w-]+(\.[\w-]+)*@";
		$pattern .= "([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})$/i";
		if (preg_match($pattern, $mail)) {
		   $parts = explode("@", $mail);
		   if (!function_exists('checkdnsrr')) {
			   return Checker::win_checkdnsrr($parts[1], "MX");
		   }
		   else {
			   return checkdnsrr($parts[1], "MX");
		   }
		} else {
		   // e-mail address contains invalid charcters
		   return false;
		}
	}
	
	public static function check_url ($url)
	{
		$url = @parse_url($url);
		if ($url) {
			$url = array_map('trim', $url);
			$url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
			$path = (isset($url['path'])) ? $url['path'] : '/';
			
			$path .= ( isset ( $url['query'] ) ) ? "?$url[query]" : '';
			if ( isset($url['host']) && $url['scheme'] == 'http' && $url['host'] != gethostbyname($url['host'])) {
				$headers = get_headers($url['scheme'].'://'.$url['host'].':'.$url['port'].$path);
				$headers = (is_array($headers)) ? implode ("\n", $headers) : $headers;
				return ( bool ) preg_match ( '#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers );
			}
		}
		return false;
	}

}
