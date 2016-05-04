<?php
$noexit=1;
include "admin/server/auth.php";
if (isset($_SESSION['user'])){
    $Sc=new Stories();
    $stories=$Sc->get_stories();
    $S=array();
    foreach($stories as $s) {
        if ($s['id']>0 && $s['statut'] || $s['id']==$id) $S[$s['id']]=$s;
    }
    include "data/pages/inc/story.php";
} else {
    include "data/pages/404.php";
}
?>
