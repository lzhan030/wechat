<?php
/**
 *    全局公用函数
 *
 *    $sn$
 */
 
defined('IN_IA') or exit('Access Denied');

function ihtmlspecialchars($var) {
	if (is_array($var)) {
		foreach ($var as $key => $value) {
			$var[htmlspecialchars($key)] = ihtmlspecialchars($value);
		}
	} else {
		$var = str_replace('&amp;', '&', htmlspecialchars($var, ENT_QUOTES));
	}
	return $var;
}

function checksubmit(){
	global $_W;
	return $_W['ispost'];
}

function message($msg, $redirect = '', $type = '') {
	global $_W;
	if($redirect == 'refresh') {
		$redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
	}
	if($redirect == '') {
		$type = in_array($type, array('success', 'error', 'tips', 'ajax', 'sql')) ? $type : 'error';
	} else {
		$type = in_array($type, array('success', 'error', 'tips', 'ajax', 'sql')) ? $type : 'success';
	}
	if($_W['isajax'] || $type == 'ajax') {
		$vars = array();
		$vars['message'] = $msg;
		$vars['redirect'] = $redirect;
		$vars['type'] = $type;
		exit(json_encode($vars));
	}
	if (defined('IN_MOBILE')) {
		$message = "<script type=\"text/javascript\">alert('$msg');";
		$redirect && $message .= "location.href = \"{$redirect}\";";
		$message .= "</script>";
		include template('message', TEMPLATE_INCLUDEPATH);
		exit();
	}
	if (empty($msg) && !empty($redirect)) {
		header('Location: '.$redirect);
	}
	include template('common/message', TEMPLATE_INCLUDEPATH);
	exit();
}

function template($filename, $flag = TEMPLATE_DISPLAY) {
	global $_W;
	$paths = explode('/', $filename);
	if (defined('IN_MOBILE')) {
		$source = PUBLIC_TEMPLATE_DIR.'wesite/common/'.implode('/', $paths).".tpl.php";
	} else {
		$source = PUBLIC_TEMPLATE_DIR.'wesite/common/'."{$filename}.tpl.php";
	}
	
	return $source;
}

function strexists($string, $find) {
	return !(strpos($string, $find) === FALSE);
}

//$attach 为URL中附加字段
function pagination($tcount, $pindex, $psize = 15, $url = '', $context = array('before' => 5, 'after' => 4, 'ajaxcallback' => ''), $attach = array()) {
	global $_W;
	$pdata = array(
		'tcount' => 0,
		'tpage' => 0,
		'cindex' => 0,
		'findex' => 0,
		'pindex' => 0,
		'nindex' => 0,
		'lindex' => 0,
		'options' => ''
	);
	if($context['ajaxcallback']) {
		$context['isajax'] = true;
	}

	$pdata['tcount'] = $tcount;
	$pdata['tpage'] = ceil($tcount / $psize);
	if($pdata['tpage'] <= 1) {
		return '';
	}
	$cindex = $pindex;
	$cindex = min($cindex, $pdata['tpage']);
	$cindex = max($cindex, 1);
	$pdata['cindex'] = $cindex;
	$pdata['findex'] = 1;
	$pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
	$pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
	$pdata['lindex'] = $pdata['tpage'];

	if($context['isajax']) {
		if(!$url) {
			$url = $_W['script_name'] . '?' . http_build_query($_GET);
		}
		$pdata['faa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['findex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['paa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['pindex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['naa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['nindex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['laa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['lindex'] . '\', ' . $context['ajaxcallback'] . ')"';
	} else {
		if($url) {
			$pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
			$pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
			$pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
			$pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
		} else {
			$_GET['page'] = $pdata['findex'];
			$pdata['faa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['pindex'];
			$pdata['paa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['nindex'];
			$pdata['naa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['lindex'];
			$pdata['laa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
		}
	}

	$html = '<div class="pagination pagination-centered"><ul>';
	if($pdata['cindex'] > 1) {
		$html .= "<li><a {$pdata['faa']} class=\"pager-nav\">首页</a></li>";
		$html .= "<li><a {$pdata['paa']} class=\"pager-nav\">&laquo;上一页</a></li>";
	}
	//页码算法：前5后4，不足10位补齐
	if(!$context['before'] && $context['before'] != 0) {
		$context['before'] = 5;
	}
	if(!$context['after'] && $context['after'] != 0) {
		$context['after'] = 4;
	}

	if($context['after'] != 0 && $context['before'] != 0) {
		$range = array();
		$range['start'] = max(1, $pdata['cindex'] - $context['before']);
		$range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
		if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
			$range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
			$range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
		}
		for ($i = $range['start']; $i <= $range['end']; $i++) {
			if($context['isajax']) {
				$aa = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $i . '\', ' . $context['ajaxcallback'] . ')"';
			} else {
				if($url) {
					$aa = 'href="?' . str_replace('*', $i, $url) . '"';
				} else {
					$_GET['page'] = $i;
					$aa = 'href="?' . http_build_query($_GET) . '"';
				}
			}
			$html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
		}
	}

	if($pdata['cindex'] < $pdata['tpage']) {
		$html .= "<li><a {$pdata['naa']} class=\"pager-nav\">下一页&raquo;</a></li>";
		$html .= "<li><a {$pdata['laa']} class=\"pager-nav\">尾页</a></li>";
	}
	$html .= '</ul></div>';
	return $html;
}

function cutstr($string, $length, $havedot=0, $charset='') {
	global $_W;
	if(empty($charset)) {
		$charset = $_W['charset'];
	}
	if(strtolower($charset) == 'gbk') {
		$charset = 'gbk';
	} else {
		$charset = 'utf8';
	}
	if(istrlen($string, $charset) <= $length) {
		return $string;
	}
	if(function_exists('mb_strcut')) {
		$string = mb_substr($string, 0, $length, $charset);
	} else {
		$pre = '{%';
		$end = '%}';
		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

		$strcut = '';
		$strlen = strlen($string);

		if($charset == 'utf8') {
			$n = $tn = $noc = 0;
			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1; $n++; $noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2; $n += 2; $noc++;
				} elseif(224 <= $t && $t <= 239) {
					$tn = 3; $n += 3; $noc++;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4; $n += 4; $noc++;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5; $n += 5; $noc++;
				} elseif($t == 252 || $t == 253) {
					$tn = 6; $n += 6; $noc++;
				} else {
					$n++;
				}
				if($noc >= $length) {
					break;
				}
			}
			if($noc > $length) {
				$n -= $tn;
			}
			$strcut = substr($string, 0, $n);
		} else {
			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t > 127) {
					$tn = 2; $n += 2; $noc++;
				} else {
					$tn = 1; $n++; $noc++;
				}
				if($noc >= $length) {
					break;
				}
			}
			if($noc > $length) {
				$n -= $tn;
			}
			$strcut = substr($string, 0, $n);
		}
		$string = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
	}

	if($havedot) {
		$string = $string . "...";
	}

	return $string;
}

function istrlen($string, $charset='') {
	global $_W;
	if(empty($charset)) {
		$charset = $_W['charset'];
	}
	if(strtolower($charset) == 'gbk') {
		$charset = 'gbk';
	} else {
		$charset = 'utf8';
	}
	if(function_exists('mb_strlen')) {
		return mb_strlen($string, $charset);
	} else {
		$n = $noc = 0;
		$strlen = strlen($string);

		if($charset == 'utf8') {

			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$n++; $noc++;
				} elseif(194 <= $t && $t <= 223) {
					$n += 2; $noc++;
				} elseif(224 <= $t && $t <= 239) {
					$n += 3; $noc++;
				} elseif(240 <= $t && $t <= 247) {
					$n += 4; $noc++;
				} elseif(248 <= $t && $t <= 251) {
					$n += 5; $noc++;
				} elseif($t == 252 || $t == 253) {
					$n += 6; $noc++;
				} else {
					$n++;
				}
			}

		} else {

			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t>127) {
					$n += 2; $noc++;
				} else {
					$n++; $noc++;
				}
			}

		}

		return $noc;
	}
}


function ihttp_request($url, $post = '', $extra = array(), $timeout = 60) {
	$urlset = parse_url($url);
	if(empty($urlset['path'])) {
		$urlset['path'] = '/';
	}
	if(!empty($urlset['query'])) {
		$urlset['query'] = "?{$urlset['query']}";
	}
	if(empty($urlset['port'])) {
		$urlset['port'] = '80';
	}

	if(function_exists('curl_init') && function_exists('curl_exec')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $urlset['scheme']. '://' .$urlset['host'].($urlset['port'] == '80' ? '' : ':'.$urlset['port']).$urlset['path'].$urlset['query']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		if($post) {
			curl_setopt($ch, CURLOPT_POST, 1);
			if (is_array($post)) {
				$post = http_build_query($post);
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
		if (!empty($extra) && is_array($extra)) {
			foreach ($extra as $opt => $value) {
				if (strexists($opt, 'CURLOPT_')) {
					curl_setopt($ch, constant($opt), $value);
				}
				if (is_numeric($opt)) {
					curl_setopt($ch, $opt, $value);
				}
			}
		}
		$data = curl_exec($ch);
		$status = curl_getinfo($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		if($errno || empty($data)) {
			return false;
		} else {
			return http_response_parse($data);
		}
	}
	$method = empty($post) ? 'GET' : 'POST';
	$fdata = "{$method} {$urlset['path']}{$urlset['query']} HTTP/1.1\r\n";
	$fdata .= "Host: {$urlset['host']}\r\n";
	if(function_exists('gzdecode')) {
		$fdata .= "Accept-Encoding: gzip, deflate\r\n";
	}
	$fdata .= "Connection: close\r\n";
	if (!empty($extra) && is_array($extra)) {
		foreach ($extra as $opt => $value) {
			if (!strexists($opt, 'CURLOPT_')) {
				$fdata .= "{$opt}: {$value}\r\n";
			}
		}
	}
	$body = '';
	if ($post) {
		if (is_array($post)) {
			$body = http_build_query($post);
		} else {
			$body = urlencode($post);
		}
		$fdata .= 'Content-Length: ' . strlen($body) . "\r\n\r\n{$body}";
	} else {
		$fdata .= "\r\n";
	}
	$fp = fsockopen($urlset['host'], $urlset['port']);
	stream_set_blocking($fp, true);
	stream_set_timeout($fp, $timeout);
	if (!$fp) {
		return false;
	} else {
		fwrite($fp, $fdata);
		$content = '';
		while (!feof($fp))
			$content .= fgets($fp, 512);
		fclose($fp);
		return http_response_parse($content);
	}
}
function http_response_parse($data) {
	$rlt = array();
	$pos = strpos($data, "\r\n\r\n");
	$split1[0] = substr($data, 0, $pos);
	$split1[1] = substr($data, $pos + 4, strlen($data));
	
	$split2 = explode("\r\n", $split1[0], 2);
	preg_match('/^(\S+) (\S+) (\S+)$/', $split2[0], $matches);
	$rlt['code'] = $matches[2];
	$rlt['status'] = $matches[3];
	$rlt['responseline'] = $split2[0];
	$header = explode("\r\n", $split2[1]);
	$isgzip = false;
	foreach ($header as $v) {
		$row = explode(':', $v);
		$key = trim($row[0]);
		$value = trim($row[1]);
		if (is_array($rlt['headers'][$key])) {
			$rlt['headers'][$key][] = $value;
		} elseif (!empty($rlt['headers'][$key])) {
			$temp = $rlt['headers'][$key];
			unset($rlt['headers'][$key]);
			$rlt['headers'][$key][] = $temp;
			$rlt['headers'][$key][] = $value;
		} else {
			$rlt['headers'][$key] = $value;
		}
		if(!$isgzip && strtolower($key) == 'content-encoding' && strtolower($value) == 'gzip') {
			$isgzip = true;
		}
	}
	if($isgzip && function_exists('gzdecode')) {
		$rlt['content'] = gzdecode($split1[1]);
	} else {
		$rlt['content'] = $split1[1];
	}
	$rlt['meta'] = $data;
	return $rlt;
}
function emotion($message = '', $size = '24px') {
	$emotions = array(
		"/::)","/::~","/::B","/::|","/:8-)","/::<","/::$","/::X","/::Z","/::'(",
		"/::-|","/::@","/::P","/::D","/::O","/::(","/::+","/:--b","/::Q","/::T",
		"/:,@P","/:,@-D","/::d","/:,@o","/::g","/:|-)","/::!","/::L","/::>","/::,@",
		"/:,@f","/::-S","/:?","/:,@x","/:,@@","/::8","/:,@!","/:!!!","/:xx","/:bye",
		"/:wipe","/:dig","/:handclap","/:&-(","/:B-)","/:<@","/:@>","/::-O","/:>-|",
		"/:P-(","/::'|","/:X-)","/::*","/:@x","/:8*","/:pd","/:<W>","/:beer","/:basketb",
		"/:oo","/:coffee","/:eat","/:pig","/:rose","/:fade","/:showlove","/:heart",
		"/:break","/:cake","/:li","/:bome","/:kn","/:footb","/:ladybug","/:shit","/:moon",
		"/:sun","/:gift","/:hug","/:strong","/:weak","/:share","/:v","/:@)","/:jj","/:@@",
		"/:bad","/:lvu","/:no","/:ok","/:love","/:<L>","/:jump","/:shake","/:<O>","/:circle",
		"/:kotow","/:turn","/:skip","/:oY","/:#-0","/:hiphot","/:kiss","/:<&","/:&>"
	);
	foreach ($emotions as $index => $emotion) {
		$message = str_replace($emotion, '<img style="width:'.$size.';vertical-align:middle;" src="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/'.$index.'.gif" />', $message);
	}
	return $message;
}

/**
 * 序列化操作
 *
 * @param string $value
 */
function iserializer($value) {
	return serialize($value);
}
/**
 * 解序列化
 *
 * @param array $value
 */
function iunserializer($value) {
	if (empty($value)) {
		return '';
	}
	if (!is_string($value)) {
		return $value;
	}
	$result = unserialize($value);
	return $result;
}