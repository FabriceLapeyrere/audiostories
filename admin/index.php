<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
include '../shared/utils.php';
include '../shared/init_admin.php';
include '../data/conf/main.php';
include 'server/always.php';
?>
<html ng-app="audiostories" ng-controller="mainCtl">
<head>
<title><?=BRAND?></title>
<base href="<?=RACINE?>admin/">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
<link rel="icon" href="<?=absolute("favicon.ico")?>" />
<link href="lib/css/bootstrap.min.css" media="all" type="text/css" rel="stylesheet">
<link href="lib/css/bootstrap-theme.min.css" media="all" type="text/css" rel="stylesheet">
<link href="lib/css/angular-motion.min.css" media="all" type="text/css" rel="stylesheet">
<link href="lib/css/bootstrap-switch.css" media="all" type="text/css" rel="stylesheet">
<link href="lib/css/hotkeys.css" media="all" type="text/css" rel="stylesheet">
<link href="css/audiostories.css" media="all" type="text/css" rel="stylesheet">
<link href="lib/css/cssreset-context-min.css" rel="stylesheet" type="text/css">
<link href="lib/css/cssbase-context-min.css" rel="stylesheet" type="text/css">
<style>
	.wrapperR:after {
		padding-top:<?=100*(1/$RATIO)?>%; /*ratio*/
	}
</style>
</head>
<body>
<div ng-if="logged.id>0" ng-class="{'navbar':logged.id>0, 'navbar-default':logged.id>0}" role="navigation" bs-navbar ng-include="'partials/menu.html'">
</div>
<div ng-view></div>

<script src="lib/angular.min.js"></script>
<script src="lib/angular-route.min.js"></script>
<script src="lib/angular-resource.min.js"></script>
<script src="lib/ui-bootstrap-tpls-0.10.0.min.js"></script>
<script src="lib/angular-sanitize.min.js"></script>
<script src="lib/angular-file-upload.min.js"></script>
<script src="lib/draganddrop.js"></script>
<script src="lib/autofill-event.js"></script>
<script src="lib/angular-toggle-switch.min.js"></script>
<script src="lib/moment-with-langs.js"></script>
<script src="lib/hotkeys.js"></script>
<script src="lib/ckeditor/ckeditor.js"></script>
<script src="lib/ng-ckeditor.js"></script>
<script src="lib/mousewheel.js"></script>
<script src="lib/hamster.js"></script>
<script src="lib/load-image.js"></script>
<script src="lib/color-thief.js"></script>
<script src="lib/keysort.js"></script>
<script src="js/audiostories.js"></script>
</body>
</html>
