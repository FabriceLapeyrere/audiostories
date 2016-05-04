<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */
ini_set('max_execution_time', 0);
include 'shared/utils.php';
include 'shared/init.php';
include 'data/conf/main.php';
include 'server/always.php';
include 'server/stories.php';
include 'server/pages.php';
include 'lib/cesures/hyphenation.php';
$page=isset($_GET['page']) ? $_GET['page'] : 'index';
$id=isset($_GET['id']) ? $_GET['id'] : 0;

if (file_exists("data/pages/$page.php")) {
	include "data/pages/$page.php";
} else {
	include "data/pages/404.php";
}
