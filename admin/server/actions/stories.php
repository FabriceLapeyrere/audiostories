<?php
function addStory($params){
	$stories= new Stories();
	return $stories->add_story($params);
}
function getStory($params){
	$stories= new Stories();
	return $stories->get_story($params);
}
function modStory($params){
	$stories= new Stories();
	return $stories->mod_story($params);
}
function delStory($params){
	$stories= new Stories();
	return $stories->del_story($params);
}
function addStoryTag($params){
	$stories= new Stories();
	return $stories->add_story_tag($params);	
}
function delStoryTag($params){
	$stories= new Stories();
	return $stories->del_story_tag($params);	
}
function movTag($params){
	$stories= new Stories();
	return $stories->move_tag($params);	
}
function modTag($params){
	$stories= new Stories();
	return $stories->mod_tag($params);	
}
function addTag($params){
	$stories= new Stories();
	return $stories->add_tag($params);	
}
function delFile($params){
	$stories= new Stories();
	return $stories->del_file($params);	
}
