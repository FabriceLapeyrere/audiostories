<?php
function getLog(){
	if (isset($_SESSION['user']) && $_SESSION['user']['id']==1) {
		$i=0;
		$tab=array();
		$lines = file('../data/log.txt');
		foreach (array_reverse($lines) as $line) {
			$tab[]=json_decode($line);
			$i++;
			if ($i>=100) break;
		}
		return $tab;
	} else {
		return array();
	}
}
function getUsers(){
	if (isset($_SESSION['user']) && $_SESSION['user']['id']==1) {
		$user= new User();
		return $user->get_users();
	} else {
		return array();
	}
}
function getUsersList(){
	$user= new User();
	return $user->get_users_list();
}
function addUser($params){
	if (isset($_SESSION['user']) && $_SESSION['user']['id']==1) {
		$user= new User();
		return $user->create($params->login,$params->name,$params->pwd);
	} else {
		return 0;
	}
}
function modUser($params){
	if (isset($_SESSION['user']) && ($_SESSION['user']['id']==1 || $_SESSION['user']['id']=$params->id)) {
		$user= new User();
		$pwd=isset($params->pwd) ? $params->pwd : '';
		$u=$user->update($params->id,$params->login,$params->name,$pwd);
		if ($_SESSION['user']['id']==$params->id) {
			$_SESSION['user']=$u;
		}
		return $u;
	} else {
		return 0;
	}
}
function delUser($params){
	if (isset($_SESSION['user']) && $_SESSION['user']['id']==1) {
		$user= new User();
		$user->del($params->id);
		return $user->get_users();
	} else {
		return 0;
	}
}
function modPanier($params){
	$user= new User();
	return $user->mod_prefs($params);
}
function addPanier($params){
	$user= new User();
	return $user->add_panier($params);
}
function panierAll($params){
	$user= new User();
	return $user->panier_all($params);
}
function delPanier($params){
	$user= new User();
	return $user->del_panier($params);
}
