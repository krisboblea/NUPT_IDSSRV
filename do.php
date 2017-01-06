<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(0);
$line_type="out";  //服务器在内网请填写in   外网填写out
$idssrv_host="idssrv01.njupt.edu.cn";
$p['IDToken0']=null;
$p['IDToken1']=request("no");
$p['IDToken2']=request("pwd");
$p['IDButton']='Submit';
$p['goto']='';
$p['encoded']='false';
$p['inputCode']='';
$p['gx_charset']='UTF-8';
if(!$p['IDToken1']||!$p['IDToken2']){
	$result['ids']['status']='0';
	$result['ids']['reason']='无效的用户名或密码';
	echo json_encode($result['ids']);
	die();
}
$cookie_file = dirname(__FILE__).'/cookies/'.$p['IDToken1'].'.txt';
$url = "http://".$idssrv_host."/amserver/UI/Login";
$i=0;
while($i<=0){
	$i+=1;
	$r= postData($url,$p);
	//echo $r;
	$result=null;
	if(!strstr($r,"AlrtMsgTxt")&&!strstr($r,"Error")){
		$result['ids']['status']='1';
		if($line_type=="in"){
			$info=iconv("GBK", "UTF-8",postData("http://xykadmin.njupt.edu.cn/web/main.jsp",null));
			preg_match("|var username='(.+?)';|is",$info,$ak['name']);
			preg_match("|var usercode='(.+?)';|is",$info,$ak['no']);
			preg_match("|var orgname='(.+?)';|is",$info,$ak['major']);
			preg_match("|var typename='(.+?)';|is",$info,$ak['type']);
		}elseif($line_type=="out"){
			$info=postData("http://my.njupt.edu.cn/ccs/ehome/index.do",null);
			preg_match('|<div id="welcome-msg">(.+?)个人服务门户|is',$info,$ak['name']);
			preg_match('|身份：(.+?)</div>|is',$info,$ak['type']);
			$ak["major"][1]="";
		}
		$result['ids']['no']=$p['IDToken1'];
		$result['ids']['name']=trim($ak["name"][1]);
		$result['ids']['type']=trim($ak["type"][1]);
		$result['ids']['major']=trim($ak["major"][1]);
		if(!$result['ids']['name']) $result['ids']['status']='0';
		break;
	}else{
		$result['ids']['status']='0';
		if(strstr($r,"AlrtMsgTxt")) preg_match('|<div class="AlrtMsgTxt">(.+?)<!-- warning message -->|is',$r,$ak);
		if(strstr($r,"Server Error")){
			$ak[1]="认证服务器错误,请重试！";
			$result['ids']['status']='-1';
		}
		$result['ids']['reason']=trim($ak[1]);
		//$result['ids']['return']=$r;
		continue;
	}
}
echo json_encode($result['ids']);
unlink($cookie_file);


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
	global $cookie_file;
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
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); //使用上面获取的cookies
	curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
} 
?>