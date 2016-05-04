<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */
//story
$story=$S[$id];
$desc=desc($story['desc']);
?>
<!doctype html>
<html>
<head>
	<title>#<?=$story['num']?> - <?=$story['nom']?></title>
	<base href="<?=RACINE?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="UTF-8">
	<meta property="og:title" content="<?=htmlspecialchars($story['nom'])?>" />
	<meta property="og:description" content="<?=htmlspecialchars($story['pitch'])?>" />
	<meta property="og:type" content="article" />
	<meta property="og:url" content="<?=absolute("story/".$story['id'])?>" />
	<meta property="og:image" content="<?=thumbnail($story['id'],$story['photos']->une,'normal')?>" />
<link rel="icon" href="<?=absolute("favicon.ico")?>" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
	<link href="lib/css/bootstrap.min.custom.css" media="all" type="text/css" rel="stylesheet">
	<link href="lib/css/jquery.modal.css" media="all" type="text/css" rel="stylesheet">
	<link href="css/audiostories.css" media="all" type="text/css" rel="stylesheet">
	<style>
	<?php
        $gradient=str_replace('#','',"{$story['couleur'][0]}_{$story['couleur'][1]}_{$story['couleur'][2]}_{$story['couleur'][3]}.jpg");
		echo ".color {opacity:1;background-image:url(".absolute("data/files/story/{$story['id']}/gradient/$gradient").");background-size:100% 100%;}\n";
		echo ".wrapperR2:after {padding-top:".(49.5*(1/$RATIO))."%;}\n";
	
	?>
		
	</style>
</head>
<body>
<div id="main">
<div class='mask color'></div>
<div class="col-xs-120 header">
	<a href="">
	<div class="col-xs-60 col-xs-offset-30 col-sm-30 col-sm-offset-0 col-md-25 col-lg-20">
		<img src="<?=absolute('data/files/brand/logo2.png')?>" class="img-responsive"/>
	</div>
	</a>
    <a class="pull-right info" href="#help" rel="modal:open"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a>
</div>
<div class="col-xs-120">
	<div class="diaporama wrapperR2">
		<div class="pgauche"><?=html_image_vide('normal')?></div>
		<div class="pdroite"><?=html_image_vide('normal')?></div>
        <div class="prev"></div>
		<div class="next"></div>
	</div>
	<div class="nav-diap">
		<?php
		$i=0;
		foreach($story['photos']->paires as $p) {
		?>
			<span class="nav-item cursor<?=($i==0 ? ' ok' : ' lazy')?>"></span>
		<?php
		$i++;
		}
		?>
	</div>
</div>
<div class="col-xs-120 col-lg-80 col-lg-offset-20 meta">
	<div class="col-xs-25 col-md-16 metaD pad0 carreparent">
		<div class="carre play playPause">
		</div>
		<div class="play-mask"></div>
		<div class="Ago"></div>
		<div class="Bgo ok"></div>
	</div>
	<div class="col-xs-95 col-md-104 pad0 waveContainer">
		<div class="Awaveform"></div>
		<div class="Aprogress"></div>
		<div class="Atime"></div>
		<div class="Bwaveform farwest"></div>
		<div class="Bprogress farwest"></div>
		<div class="Btime farwest"></div>
	</div>
	<table class="metabox pad0">
		<tr class="tdA">
			<td class="colgauche col-xs-25 col-md-16 faceA ok cursor">
				<span class="faceA-play"></span>Face A	
			</td>
			<td class="coldroite col-xs-95 col-md-104 faceA ok">
				<?= (isset($story['sons']->A->titre) ? $story['sons']->A->titre : '')?>
			</td>
		</tr>
		<tr class="tdB">
			<td class="colgauche faceB cursor">
				<span class="faceB-play"></span>Face B	
			</td>
			<td class="coldroite faceB">
				<?= (isset($story['sons']->B->titre) ? $story['sons']->B->titre : '')?>
			</td>
		</tr>
		<tr>
			<td class="colgauche last">
			</td>
			<td class="coldroite last text">
				<div class="colonnes"><?= hyphenation($desc['texte']) ?></div>
                <?php if ($desc['liens']!='') { ?> <div class="liens"><?= $desc['liens'] ?></div><?php } ?>
                <?php if ($desc['auteurs']!='') { ?> <div class="auteurs">Réalisation : <?= $desc['auteurs'] ?></div><?php } ?>
			</td>
		</tr>
	</table>
</div>
<div class="col-xs-120 col-lg-80 col-lg-offset-20 egalement">
<?php
	$i=0;
	shuffle($S);
	foreach($S as $id=>$s){
		if ($s['id']!=$story['id']) {
			$i++;
			if ($i%3==1) echo "<div class='hidden-xs clearfix'></div>";
			if ($i%2==1) echo "<div class='visible-xs clearfix'></div>";
		?>
			<div class="col-xs-120 col-md-40">
				<div class="story-small">
					<a class="astory" href="story/<?=$s['id']?>">
					<?=html_image($s['id'],$s['photos']->une,'petit')?>
					<div class="img-mask-p"></div>
					</a>
					<div class="titres">
						<div class="col-sm-120 nom">
						<?= $s['nom']?> <span class="pull-right mini">#<?=$s['num']?></span>
						</div>
						<div class="col-md-120 pitch hidden-xs hidden-sm">
						<?= $s['pitch']?>
						</div>
						<div class="clearfix"></div>
					</div>
		 			<div class="clearfix"></div>
				</div>
			</div>
		<?php
		}
	}
?>
</div>
<div class="col-xs-120 footer">
	<ul>
<?php
$Pc=new Pages();
$pages=$Pc->get_pages();
$P=array();
foreach($pages as $p) {
	if ($p['nom']!='' && $p['statut']) $P[]=$p;
}
foreach($pages as $p){
	echo "<li><a href='p/".$p['id']."'>".$p['nom']."</a></li>";
}
?>
	</ul> 
</div>
<div class="clearfix"></div>
</div>
<div id="help" class="jqModal">
<p>Pour faire défiler les photos cliquer sur celle de droite. Pour revenir en arrière cliquer sur celle de gauche.</p>

<p>Le player audio situé en dessous des photos contient deux bandes son : une face A et une face B. Cliquer sur l'une des deux faces pour la sélectionner.</p>
<p>Pour enclencher la lecture, ou la mettre en pause, il faut cliquer sur le bouton play <span class="glyphicon glyphicon-play" aria-hidden="true"></span> / pause <span class="glyphicon glyphicon-pause" aria-hidden="true"></span> cerclé de blanc.</p>

<p>Ne pas oublier d'allumer vos enceintes !</p>

<p>Bon visionnage.</p>

</div>


<?php
	echo "
	<script>
		var APeaks=0;
		var BPeaks=0;
		var faceA=0;
		var faceB=0;
	";
	if (isset($story['sons']->A->file['filename'])) {
		echo "
		faceA='".absolute("data/files/story/{$story['id']}/faceA/".$story['sons']->A->file['filename'])."';
		faceB='".absolute("data/files/story/{$story['id']}/faceB/".$story['sons']->B->file['filename'])."';
		APeaks=".trim(file_get_contents($story['sons']->A->file['path'].".json")).";
		BPeaks=".trim(file_get_contents($story['sons']->B->file['path'].".json")).";
	";
	}
	echo "
		var Paires=[];
		var PairesOk=[];
	";
	foreach ($story['photos']->paires as $p) {
		echo "
		Paires.push({gauche:'".thumbnail($story['id'],$p->gauche,'normal')."',droite:'".thumbnail($story['id'],$p->droite,'normal')."'});
		PairesOk.push({gauche:'',droite:''});
		";
	}
	echo "
	</script>";
?>
<script src="lib/jquery-1.11.1.min.js"></script>
<script src="lib/jquery.tools.scrollable.min.js"></script>
<script src="lib/jquery.modal.min.js"></script>
<script src="lib/Smooth-0.1.7.js"></script>
<script src="lib/wavesurfer.min.js"></script>
<script src="js/story.js"></script>
</body>
</html>
