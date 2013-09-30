<?php
/*
 * Instead of an output filter it would be smart to have a nice template operator later on that directly converts the urls
 * 
 */
class xrowCDNFilter {
	const DIR_NAME = '[.a-z0-9_-]+';
	const PATH_EXP = '(\/[.a-z0-9_-]+)*';
	const BASENAME_EXP = '[.a-z0-9_-]+';
	const MIN_BRACKETS = 11;
	static function buildRegExp($dirs, $suffixes) {
		
		$dirs = '(' . implode ( '|', $dirs ) . ')';
		$suffixes = '(' . implode ( '|', $suffixes ) . ')';
		// [shu][r][cel] improves performance
		return "/([shu][r][cel])(=['\"]|f=['\"]|(\s)*\((\s)*['\"]?(\s)*)(" . $dirs . self::PATH_EXP . '\/' . self::BASENAME_EXP . ')(\.' . $suffixes . ')/imU';
	}
	static function randomHost($rule) {
		$value = eZINI::instance ( 'xrowcdn.ini' )->variable ( 'Rule-' . $rule, 'Replacement' );
		if (is_array ( $value )) {
			return $value [array_rand ( $value, 1 )];
		} else {
			return $value;
		}
	}
	static function filter($output) {
$ini = eZINI::instance ( 'xrowcdn.ini' );
if( $ini->hasVariable ( 'Settings', 'ExcludeHostList') ) 
{
foreach( eZINI::instance ( 'xrowcdn.ini' )->variable ( 'Settings', 'ExcludeHostList' ) as $host ) 
{ 
 if(strpos( $_SERVER['HTTP_HOST'], $host) !== false )  
{ 
return $output;
}
}
}
# speed up string matching by removing whitespace
#	    $output = preg_replace('~>\s+<~', '><', $output);
		if (eZSys::isSSLNow ()) {
			return $output;
		}
		eZDebug::createAccumulatorGroup ( 'outputfilter_total', 'Outputfilter Total' );
		$ini = eZINI::instance ( 'xrowcdn.ini' );
		// Check if we can gzip content
		$canGzip = (substr_count ( $_SERVER ['HTTP_ACCEPT_ENCODING'], 'gzip' ) > 0);
		$useGZIP = false;
		$gzipSuffixes = array ();
		if ($ini->hasVariable ( 'Settings', 'UseGZIP' ) and trim ( $ini->variable ( 'Settings', 'UseGZIP' ) ) == "enabled") {
			$useGZIP = true;
			$gzipSuffixes = $ini->variable ( 'Settings', 'GZIPSuffixes' );
		}
		$patterns = array ();
		$replacements = array ();
		// Send extra Header information for Varnish if GZIP encoding is enabled
		if ($canGzip === true) {
			header ( 'Vary: Accept-Encoding', true );
		}
		if ($ini->hasVariable ( 'Rules', 'List' )) {
			foreach ( $ini->variable ( 'Rules', 'List' ) as $rule ) {
				$dirs = array ();
				$suffix = array ();
				
				if ($ini->hasSection ( 'Rule-' . $rule )) {
					if ($ini->hasVariable ( 'Rule-' . $rule, 'Dirs' ) and $ini->hasVariable ( 'Rule-' . $rule, 'Suffixes' ) and $ini->hasVariable ( 'Rule-' . $rule, 'Replacement' )) {
						$dirs = $ini->variable ( 'Rule-' . $rule, 'Dirs' );
						$suffix = $ini->variable ( 'Rule-' . $rule, 'Suffixes' );
						if ($useGZIP) {
							$suffixesnogzip = array_diff ( $suffix, $gzipSuffixes );
							$suffixesgzip = array_diff ( $suffix, $suffixesnogzip );
							
							if (count ( $suffixesnogzip ) > 0) {
								$reg = self::buildRegExp ( $dirs, $suffixesnogzip );
								$patterns [] = $reg;
								$count = 0;
								str_replace ( '(', '(', $reg, $count );
								$count -= self::MIN_BRACKETS;
								$functions [] = 'return $matches[1].$matches[2].xrowCDNFilter::randomHost(  "' . $rule . '" ) . $matches[6].".gz".$matches[' . (9 + $count) . '];';
							}
							
							if (count ( $suffixesgzip ) > 0) {
								$reg = self::buildRegExp ( $dirs, $suffixesgzip );
								$patterns [] = $reg;
								$count = 0;
								str_replace ( '(', '(', $reg, $count );
								$count -= self::MIN_BRACKETS;
								$functions [] = 'return $matches[1].$matches[2].xrowCDNFilter::randomHost(  "' . $rule . '" ) . $matches[6].".gz".$matches[' . (9 + $count) . '];';
							
							}
						} else {
							$reg = self::buildRegExp ( $dirs, $suffix );
							$patterns [] = $reg;
							$count = 0;
							str_replace ( '(', '(', $reg, $count );
							$count -= self::MIN_BRACKETS;
							$functions [] = 'return $matches[1].$matches[2].xrowCDNFilter::randomHost(  "' . $rule . '" ) . $matches[6].$matches[' . (9 + $count) . '];';
						}
					}
				}
			} // FOREACH
		} // IF ends
		

		eZDebug::accumulatorStart ( 'outputfilter', 'outputfilter_total', 'Output Filtering' );
		//$output = preg_replace($patterns, $replacements, $output );
		foreach ( $patterns as $key => $pattern ) {
			$output = preg_replace_callback ( $pattern, create_function ( '$matches', $functions [$key] ), $output );
		}
		eZDebug::accumulatorStop ( 'outputfilter' );
		
		return $output;
	}
}
?>
