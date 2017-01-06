<?php
header('Content-Type: text/html; charset=utf-8');
$key=request('key');
$url = "http://my.njupt.edu.cn/ccs/main/searchUser.do?key=".$key;
$r= postData($url,null);
preg_match('|</td><td></td><td>(.+?)</td></tr>|is',$r,$ak);
$msg=$ak[1];
echo $msg."<br>";


function Request($key, $m = 'request') {
	$v = "";
	switch ($m) {
		case 'get':
			if (isset($_GET[$key])) $v = $_GET[$key];
			break;
		case 'post':
			if (isset($_POST[$key])) $v = $_POST[$key];
			break;
		default:
			if (isset($_REQUEST[$key])) $v = $_REQUEST[$key];
	} 
	return $v;
} 

function postData($url, $data) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url); 
	if($data){
		$data = http_build_query($data);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}else{
		curl_setopt($ch, CURLOPT_POST, 0);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
} 
?>