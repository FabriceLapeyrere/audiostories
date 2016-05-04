<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
function initApp($params) {
	global $MINIATURES, $RATIO;
	$reponse=array();
	$stories=new Stories();
	$reponse['stories']=$stories->get_stories();
	$reponse['tags']=$stories->get_tags();
	$pages=new Pages();
	$reponse['pages']=$pages->get_pages();
	$user=new User();
	$reponse['users']=$user->get_users_list();
	$reponse['time']=millisecondes();
	$reponse['brand']=BRAND;
	$reponse['conf']=array('miniatures'=>$MINIATURES,'ratio'=>$RATIO);
	return $reponse;
}
function majApp($params) {
	$t=$params->time;
	$reponse=array();
	$stories=new Stories();
	$reponse['stories']=array();
	$reponse['stories']['mods']=$stories->get_mods($t);
	$reponse['stories']['adds']=$stories->get_adds($t);
	$reponse['stories']['dels']=$stories->get_dels($t);
	$reponse['tags']=array();
	$reponse['tags']['mods']=$stories->get_tags_mods($t);
	$reponse['tags']['adds']=$stories->get_tags_adds($t);
	$reponse['tags']['dels']=$stories->get_tags_dels($t);
	$reponse['tag_cas']['adds']=$stories->get_tag_story_adds($t);
	$reponse['tag_cas']['dels']=$stories->get_tag_story_dels($t);
	$pages=new Pages();
	$reponse['pages']=array();
	$reponse['pages']['mods']=$pages->get_mods($t);
	$reponse['pages']['adds']=$pages->get_adds($t);
	$reponse['pages']['dels']=$pages->get_dels($t);
	$reponse['time']=millisecondes();
	return $reponse;
}
