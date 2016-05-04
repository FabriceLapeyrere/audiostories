<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */
function thumbnail($id,$f,$taille) {
	global $MINIATURES,$RATIO;
	$w=$MINIATURES[$taille];
	$h=floor($w/$RATIO);
	$path_parts = pathinfo($f);
	return absolute("data/files/story/$id/min/".$path_parts['filename']."_$w"."_$h.jpg");
}
function tw($taille) {
	global $MINIATURES,$RATIO;
	$w=$MINIATURES[$taille];
	return $w;
}
function th($taille) {
	global $MINIATURES,$RATIO;
	$w=$MINIATURES[$taille];
	$h=floor($w/$RATIO);
	return $h;
}
function html_image_vide($taille) {
	return "<img class='img-responsive' width='".tw($taille)."' height='".th($taille)."'>";
}
function html_image($id, $file, $taille) {
	return "<img class='img-responsive' width='".tw($taille)."' height='".th($taille)."' src='".thumbnail($id,$file,$taille)."'>";
}
function html_image_diap($id, $file, $taille) {
	return "<img class='img-responsive' width='".tw($taille)."' height='".th($taille)."' data-src='".thumbnail($id,$file,$taille)."'>";
}
function html_background_image($id, $file, $taille) {
	return "style='background:url(".thumbnail($id,$file,$taille).") no-repeat cover'";
}
function desc($desc){
    preg_match_all("|<p>@(.*)</p>|U",$desc,$out1, PREG_PATTERN_ORDER);
    foreach($out1[1] as $k=>$o){
        $auteurs[]=$o;
        $desc=str_replace($out1[0][$k],'',$desc);
    }
    preg_match_all("|<p>#(.*)</p>|U",$desc,$out2, PREG_PATTERN_ORDER);
    foreach($out2[1] as $k=>$o){
        $liens[]=$o;
        $desc=str_replace($out2[0][$k],'',$desc);
    }
    if (isset($auteurs)) $res['auteurs']=implode(', ',$auteurs);
	else $res['auteurs']='';
    if (isset($liens)) $res['liens']=implode('<br />',$liens);
    else $res['liens']='';
    $res['texte']=$desc;
    return $res;
}
function absolute($url) {
    return "http://".$_SERVER['SERVER_NAME'].RACINE.$url;
}
