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
	if ($s['id']>0 && $s['statut']) $S[]=$s;
}
?>
<!doctype html>
<html>
<head>
	<title><?=BRAND?></title>
	<base href="<?=RACINE?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="UTF-8">
	<link rel="icon" href="<?=absolute("favicon.ico")?>" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
	<link href="lib/css/bootstrap.min.css" media="all" type="text/css" rel="stylesheet">
	<link href="css/audiostories.css" media="all" type="text/css" rel="stylesheet">
	<style>
	<?php
		for($i=0;$i<count($S);$i++){
		$gradient=str_replace('#','',"{$S[$i]['couleur'][0]}_{$S[$i]['couleur'][1]}_{$S[$i]['couleur'][2]}_{$S[$i]['couleur'][3]}.jpg");
		echo ".color-$i {background-image:url(".absolute("data/files/story/{$S[$i]['id']}/gradient/$gradient").");background-size:100% 100%;}\n";
		}
	?>
		
	</style>
</head>
<body>
<div id="main">
<?php
	for($i=0;$i<count($S);$i++){
		if ($i==0) $class=' opaque';
		else $class='';
		echo "<div class='mask$class color-$i'></div>\n";
	}
?>
<div class="col-xs-12 col-md-10 col-md-offset-1">
	<div class="col-xs-12 principal">
	<div class="col-xs-12 header-home">
		<a href="">
		<div class="col-xs-8 col-xs-offset-2 col-sm-4 col-sm-offset-0 col-md-4 col-lg-3">
			<img src="<?=absolute('data/files/brand/logo.png')?>" class="img-responsive"/>
		</div>
		</a>
        <ul class="pull-right hidden-xs hidden-sm">
		<?php
$Pc=new Pages();
$pages=$Pc->get_pages();
$P=array();
foreach($pages as $p) {
    if ($p['nom']!='' && $p['statut']) $P[]=$p;
}
foreach($P as $p){
    echo "<li><a href='p/".$p['id']."'>".$p['nom']."</a></li>";
}
?>
    </ul>
	</div>
	<div class="col-xs-12 col-sm-8 col-lg-6">
	<?php
		for($i=0;$i<min(2,count($S));$i++){
		$story=$S[$i]
	?>
		<div class="story-big">
			<a class="astory" href="story/<?=$story['id']?>">
			<?=html_image($story['id'],$story['photos']->une,'normal')?>
			<div class="img-mask"></div>
			</a>
			<div class="titres">
				<div class="col-xs-12 nom">
				<?= $story['nom']?> <span class="pull-right mini">#<?=$story['num']?></span>
				</div>
				<div class="col-xs-12 pitch">
				<?= $story['pitch']?>
				</div>
				<div class="clearfix"></div>
			</div>
 			<div class="clearfix"></div>
		</div>
	<?php
		}
	?>
	</div>
	<div class="col-xs-12 col-sm-4 col-lg-6 pad0">
	<?php
		for($i=2;$i<count($S);$i++){
		$story=$S[$i]
	?>
		<div class="col-lg-6">
			<div class="story-small">
				<a class="astory" href="story/<?=$story['id']?>">
				<?=html_image($story['id'],$story['photos']->une,'petit')?>
				<div class="img-mask-p"></div>
				</a>
				<div class="titres">
					<div class="col-xs-12 nom">
					<?= $story['nom']?> <span class="pull-right mini">#<?=$story['num']?></span>
					</div>
					<div class="col-xs-12 pitch">
					<?= $story['pitch']?>
					</div>
					<div class="clearfix"></div>
				</div>
	 			<div class="clearfix"></div>
			</div>
		</div>
	<?php
        if ($i%2==1) echo "<div class=\"clearfix\"></div>";
		}
	?>
	</div>
	</div>
</div>
<div class="col-xs-12 footer">
	<ul>
<?php
$Pc=new Pages();
$pages=$Pc->get_pages();
$P=array();
foreach($pages as $p) {
	if ($p['nom']!='' && $p['statut']) $P[]=$p;
}
foreach($P as $p){
	echo "<li><a href='p/".$p['id']."'>".$p['nom']."</a></li>";
}
?>
	</ul>
</div>
<div class="clearfix"></div>
</div>

<script src="lib/jquery-1.11.1.min.js"></script>
<script src="js/audiostories.js"></script>
</body>
</html>
