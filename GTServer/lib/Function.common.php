<?php
/**
 * 通用方法集合
 * @author wenyj
 * @version 
 * 	- 20141210, init
 */
function str_return($data) {
	$ret = '';
	if ( is_array($data) ) {
		foreach ( $data as $type => $conf ) {
			switch ($type) {
				case 'array':
					$conf = str_return_array($conf);
					$ret .= "return {$conf};" . PHP_EOL;
					break;
				case 'boolean':
					if ( is_bool($conf) ) {
						$conf = ($conf) ? 'true' : 'false';
						$ret .= "return {$conf};" . PHP_EOL;
					}
					break;
				case 'string':
					if ( is_string($conf) ) {
						$ret .= "return '{$conf}';" . PHP_EOL;
					}
					break;
				case 'integer':
					if ( is_int($conf) ) {
						$conf = intval($conf);
						$ret .= "return {$conf};" . PHP_EOL;
					}
					break;
				case 'float':
					if ( is_float($conf) ) {
						$conf = floatval($conf);
						$ret .= "return {$conf};" . PHP_EOL;
					}
					break;
			}
		}
	}
	return $ret;
}

function str_return_array($data, $tab='') {
	$ret = '';
	if ( is_array($data) ) {
		$ret .= "array(\r\n{$tab}";
		foreach ( $data as $k => $v ) {
			$ret .= "'{$k}' => ";
			if ( is_bool($v) ) {
				$v = ($v) ? 'true' : 'false';
				$ret .= $v . ',' . PHP_EOL . $tab;
			} elseif ( is_string($v) ) {
				$ret .= "'{$v}'," . PHP_EOL . $tab;
			} elseif ( is_int($v) ) {
				$v = intval($v);
				$ret .= $v . ',' . PHP_EOL . $tab;
			} elseif ( is_array($v) ) {
				$ret .= str_return_array($v, $tab.'	') . ',' . PHP_EOL . $tab;
			} elseif ( is_float($v) ) {
				$v = floatval($v);
				$ret .= $v . ',' . PHP_EOL . $tab;
			} else {
				continue;
			}
		}
		$ret .= ')';
	}
	return $ret;
}

function str_define($data) {
	$ret = '';
	if ( is_array($data) ) {
		foreach ( $data as $k => $v ) {
			if ( empty($v) ) {// 
				continue;
			}
			$k = strtoupper($k);
			$ret .= "define('{$k}', {$v});" . PHP_EOL;
		}
	}
	return $ret;
}

function str_defined($data) {
	$ret = '';
	if ( is_array($data) ) {
		foreach ( $data as $k => $v ) {
			if ( empty($v) ) {// 
				continue;
			}
			$k = strtoupper($k);
			$ret .= "defined('{$k}') {$v};" . PHP_EOL;
		}
	}
	return $ret;
}

function str_load($data) {
	$ret = '';
	if ( is_array($data) ) {
		foreach ( $data as $k => $v ) {
			if ( empty($v) ) {// 
				continue;
			}
			$tmp = eval("return {$v};");
			$ret .= "require_once {$v};" . PHP_EOL;
		}
	}
	return $ret;
}
