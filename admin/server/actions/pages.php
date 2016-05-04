<?php
function addPage($params){
	$pages= new Pages();
	return $pages->add_page($params);
}
function getPage($params){
	$pages= new Pages();
	return $pages->get_page($params);
}
function modPage($params){
	$pages= new Pages();
	return $pages->mod_page($params);
}
function delPage($params){
	$pages= new Pages();
	return $pages->del_page($params);
}
