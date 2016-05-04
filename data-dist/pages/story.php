<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$Sc=new Stories();
$stories=$Sc->get_stories();
$S=array();
foreach($stories as $s) {
	if ($s['id']>0 && $s['statut']) $S[$s['id']]=$s;
}
if ($id==0 || !isset($S[$id]) ) {
	include "data/pages/404.php";
} else {
    include "data/pages/inc/story.php";
}
?>
