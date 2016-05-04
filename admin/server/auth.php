<?php
session_start();
if (isset($params->verb) && $params->verb=='login'){
	$login=$params->login;
	$password=$params->password;
	$user= new User();
	$u=$user->check($login,$password);
	if (count($u)>0){
		$_SESSION['user']=array(
			'login'=>$u['login'],
			'name'=>$u['name'],
			'id'=>$u['id']
		);
	}
}
if (isset($params->verb) && $params->verb=='logout'){
	unset($_SESSION['user']);
}
if (!isset($noexit) && !isset($_SESSION['user'])){
	$reponse['auth']=false;
	echo json_encode($reponse);
	exit;
}
$reponse['auth']=true;
$reponse['user']=$_SESSION['user'];

