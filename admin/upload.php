<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
include '../shared/utils.php';
include '../shared/init_admin.php';
include '../data/conf/main.php';
include 'server/auth.php';
include 'server/stories.php';
include 'server/always.php';

if ( !empty( $_FILES ) ) {

	$id = $_POST[ 'id' ];
	$type = $_POST[ 'type' ];
	$tempPath = $_FILES[ 'file' ][ 'tmp_name' ];
	if ($type=='story') {
		$uploadDir = "../data/files/$type/$id";
		$path_parts = pathinfo($_FILES[ 'file' ][ 'name' ]);
		$new_file_name=filter($path_parts['filename']) . ".".$path_parts['extension'];
		$uploadPath = "$uploadDir/$new_file_name";
		if (!file_exists($uploadDir)){
			mkdir($uploadDir);
		}
		$i=1;
		$path=$uploadPath;
		while (file_exists($path)){
			$new_file_name=filter($path_parts['filename']) . "-copie-$i.".$path_parts['extension'];
			$path=$uploadDir . DIRECTORY_SEPARATOR . $new_file_name;
			$i++;
		}
		if (move_uploaded_file( $tempPath, $path )) {
			$stories=new Stories();
			$stories->touch_story($id);
			check_miniatures($id);
			$answer = array(
				'answer' => 'File transfer completed'
			);
			//on crée les miniatures
		} else {
			$answer = array(
				'answer' => 'Erreur...',
				'path'=>$uploadPath
			);
		}
	}
	if ($type=='faceA' || $type=="faceB") {
		$story_dir ="../data/files/story/$id";
		$upload_dir = "../data/files/story/$id/$type";
		$path_parts = pathinfo($_FILES[ 'file' ][ 'name' ]);
		$new_file_name=filter($path_parts['filename']) . ".".$path_parts['extension'];
		$uploadPath = "$upload_dir/$new_file_name";
		if (!file_exists($story_dir)){
			mkdir($story_dir);
		}
		if (!file_exists($upload_dir)){
			mkdir($upload_dir);
		}
		foreach(glob($upload_dir. DIRECTORY_SEPARATOR ."*") as $f) {
			unlink($f);
		}
		$path=$upload_dir . DIRECTORY_SEPARATOR . $new_file_name;
		if (move_uploaded_file( $tempPath, $path )) {
			$stories=new Stories();
			$stories->touch_story($id);
			check_waveform($id);
			$answer = array(
				'answer' => 'File transfer completed'
			);
		} else {
			$answer = array(
				'answer' => 'Erreur...',
				'path'=>$uploadPath
			);
		}
		
	}
	$json = json_encode( $answer );

	echo $json;

} else {

    echo 'No files';

}

?>
