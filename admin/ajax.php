<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
$params=json_decode(file_get_contents('php://input'));
include '../shared/utils.php';
include '../shared/init_admin.php';
include '../data/conf/main.php';
include 'server/user.php';
include 'server/stories.php';
include 'server/pages.php';
include 'server/auth.php';
include 'server/always.php';
include 'server/actions/app.php';
include 'server/actions/stories.php';
include 'server/actions/pages.php';
include 'server/actions/admin.php';
if (isset($params->verb) && $params->verb!='login'  && $params->verb!='logout'){
	if ($params->verb!='up') {
		$func=$params->verb;
		$reponse['action']=$func($params);
		if (strpos($func,'add')===0 || strpos($func,'mod')===0 || strpos($func,'del')===0 || strpos($func,'mov')===0 || strpos($func,'pub')===0) {
			$log=array(
				'user'=>$_SESSION['user'],
				'date'=>millisecondes(),
				'params'=>$params
			);
			error_log(json_encode($log)."\n", 3, "../data/log.txt");
		}
	}
	if ($params->verb!='initApp' && !isset($params->simple)) $reponse['maj']=majApp($params);
}
if (isset($_SESSION['user'])) $reponse['user']=$_SESSION['user'];
echo json_encode($reponse);
