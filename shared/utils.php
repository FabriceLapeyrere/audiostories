<?php
function miniature($w,$h,$file){
	$path_parts = pathinfo($file);
	$path=$path_parts['dirname'];
	if (!file_exists($path. DIRECTORY_SEPARATOR . "min")) mkdir($path. DIRECTORY_SEPARATOR . "min");
	$pre=$path. DIRECTORY_SEPARATOR . "min" . DIRECTORY_SEPARATOR . $path_parts['filename'];
	$prew=$path. DIRECTORY_SEPARATOR . "min" . DIRECTORY_SEPARATOR . $path_parts['filename'] . "_$w"."_";
	$dest=$path. DIRECTORY_SEPARATOR . "min" . DIRECTORY_SEPARATOR . $path_parts['filename'] . "_$w"."_$h.jpg";
	if (!file_exists($dest)){
		list($largeur, $hauteur) = getimagesize($file);
		if ($w/$h>$largeur/$hauteur) {
			$width=$w;
			$height=$width*$h/$w;
			$r=$width/$largeur;
		}else{
			$height=$h;
			$width=$height*$w/$h;
			$r=$height/$hauteur;
		}
		//création de la destination
		$destination = imagecreatetruecolor(min($width,$w), min($height,$h));
		$back = imagecolorallocate($destination, 255, 255, 255);

		//on detecte le type
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime=finfo_file($finfo, $file);
		//on ouvre la source
		switch ($mime) {
			case "image/png":
				$source = imagecreatefrompng($file);
				break;
			case "image/jpeg":
				$source = imagecreatefromjpeg($file);
				break;
			case "image/gif":
				$source = imagecreatefromgif($file);
				break;
		}
		imagealphablending($source, false);
		imagesavealpha($source, true);
		imagealphablending($destination, false);
		imagesavealpha($destination, true);
		// Redimensionnement
		imagecopyresampled($destination, $source, 0, 0, max(0,($largeur-$w/$r)/2), max(0,($hauteur-$h/$r)/2), min($width,$w), min($height,$h),min($width,$w)/$r, min($height,$h)/$r);

		imagejpeg($destination,$dest);
		imagedestroy($destination);
		imagedestroy($source);
	}
	foreach(glob($prew."*") as $thumb) {
		if ($thumb != $dest) unlink($thumb);
	}
	return $dest;
}


function check_waveform($id,$prefixe="../") {
	$audiowaveform=exec('which audiowaveform');
	if ($audiowaveform!='') {
		foreach(glob($prefixe."data/files/story/$id/face*/*.{mp3,wav,WAV,MP3}",GLOB_BRACE) as $f){
            if (!file_exists("$f.sox")) {
                rename($f,"$f.orig");
				exec("sox $f.orig $f");
				touch("$f.sox");
			}
			if (!file_exists("$f.json")) {
				exec("audiowaveform -i $f -o $f.json -z 50000 -b 8");
			}
		}			
	}
}
function check_miniatures($id,$prefixe="../") {
	global $MINIATURES, $RATIO;
	foreach(glob($prefixe."data/files/story/$id/*.{jpg,JPG,jpeg,JPEG}",GLOB_BRACE) as $f){
		if (is_file($f)) {
			foreach($MINIATURES as $nom=>$w){
				$h=floor($w/$RATIO);
				miniature($w,$h,$f);
			}
		}
	}	
}
function check_gradient($id,$couleurs,$prefixe="../") {
    $couleurs=json_decode($couleurs);
    $dest=str_replace('#','',$prefixe."data/files/story/$id/gradient/{$couleurs[0]}_{$couleurs[1]}_{$couleurs[2]}_{$couleurs[3]}.jpg");
    if (!file_exists($prefixe."data/files/story/$id/gradient/")){
        mkdir($prefixe."data/files/story/$id/gradient/");
    }
    if (!file_exists($dest)){
        $image=gradient(1000, 1000, $couleurs);
        imagejpeg($image,$dest);
        imagedestroy($image);
    }
}
function check_data($id,$statut,$prefixe="../") {
    if (!file_exists($prefixe."data/files/story/$id")) {
		mkdir($prefixe."data/files/story/$id");
	}
}
function build_story($row,$prefixe="../") {
	check_data($row['id'],$row['statut'],$prefixe);
	check_waveform($row['id'],$prefixe);
	check_miniatures($row['id'],$prefixe);
	check_gradient($row['id'],$row['couleur'],$prefixe);
	$row['files']=array();
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	foreach(glob($prefixe."data/files/story/".$row['id']."/*.{jpg,JPG,jpeg,JPEG}",GLOB_BRACE) as $f){
		if (is_file($f)) {
			$row['files'][]=array(
				"filename"=>basename($f),
				"mime"=>finfo_file($finfo, $f),
				"date"=>filemtime($f)
			);
		}
	}
	$row['sons']=json_decode($row['sons']);
	if (!isset($row['sons']->A)) $row['sons']->A=(object) null;
	if (!isset($row['sons']->B)) $row['sons']->B=(object) null;
	foreach(glob($prefixe."data/files/story/".$row['id']."/faceA/*.{mp3,wav,WAV,MP3}",GLOB_BRACE) as $f){
		if (is_file($f)) {
			$row['sons']->A->file=array(
				"path"=>$f,
				"filename"=>basename($f),
				"mime"=>finfo_file($finfo, $f),
				"date"=>filemtime($f)
			);
		}
	}
	foreach(glob($prefixe."data/files/story/".$row['id']."/faceB/*.{mp3,wav,WAV,MP3}",GLOB_BRACE) as $f){
		if (is_file($f)) {
			$row['sons']->B->file=array(
				"path"=>$f,
				"filename"=>basename($f),
				"mime"=>finfo_file($finfo, $f),
				"date"=>filemtime($f)
			);
		}
	}
	$row['statut']=$row['statut'] ? true : false;
	$row['date']=0+$row['date'];
	$row['photos']=json_decode($row['photos']);
	$row['couleur']=json_decode($row['couleur']);
	return $row;
}
function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
            }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}
function gradient($w=1000, $h=1000, $c=array('#FFFFFF','#FF0000','#00FF00','#0000FF'), $hex=true) {

    /*
    Generates a gradient image

    Author: Christopher Kramer

    Parameters:
    w: width in px
    h: height in px
    c: color-array with 4 elements:
        $c[0]:   top left color
        $c[1]:   top right color
        $c[2]:   bottom left color
        $c[3]:   bottom right color
       
    if $hex is true (default), colors are hex-strings like '#FFFFFF' (NOT '#FFF')
    if $hex is false, a color is an array of 3 elements which are the rgb-values, e.g.:
    $c[0]=array(0,255,255);

    */

    $im=imagecreatetruecolor($w,$h);

    if($hex) {  // convert hex-values to rgb
      for($i=0;$i<=3;$i++) {
       $c[$i]=hex2rgb($c[$i]);
      }
    }

    $rgb=$c[0]; // start with top left color
    for($x=0;$x<=$w;$x++) { // loop columns
      for($y=0;$y<=$h;$y++) { // loop rows
       // set pixel color
       $col=imagecolorallocate($im,$rgb[0],$rgb[1],$rgb[2]);
       imagesetpixel($im,$x-1,$y-1,$col);
       // calculate new color 
       for($i=0;$i<=2;$i++) {
        $rgb[$i]=
          $c[0][$i]*(($w-$x)*($h-$y)/($w*$h)) +
          $c[1][$i]*($x     *($h-$y)/($w*$h)) +
          $c[2][$i]*(($w-$x)*$y     /($w*$h)) +
          $c[3][$i]*($x     *$y     /($w*$h));
       }
      }
    }
    return $im;
}

function hex2rgb($hex)
{
    $rgb[0]=hexdec(substr($hex,1,2));
    $rgb[1]=hexdec(substr($hex,3,2));
    $rgb[2]=hexdec(substr($hex,5,2));
    return($rgb);
}
/**
 * Copy file or folder from source to destination, it can do
 * recursive copy as well and is very smart
 * It recursively creates the dest file or directory path if there weren't exists
 * Situtaions :
 * - Src:/home/test/file.txt ,Dst:/home/test/b ,Result:/home/test/b -> If source was file copy file.txt name with b as name to destination
 * - Src:/home/test/file.txt ,Dst:/home/test/b/ ,Result:/home/test/b/file.txt -> If source was file Creates b directory if does not exsits and copy file.txt into it
 * - Src:/home/test ,Dst:/home/ ,Result:/home/test/** -> If source was directory copy test directory and all of its content into dest     
 * - Src:/home/test/ ,Dst:/home/ ,Result:/home/**-> if source was direcotry copy its content to dest
 * - Src:/home/test ,Dst:/home/test2 ,Result:/home/test2/** -> if source was directoy copy it and its content to dest with test2 as name
 * - Src:/home/test/ ,Dst:/home/test2 ,Result:->/home/test2/** if source was directoy copy it and its content to dest with test2 as name
 * @todo
 *     - Should have rollback technique so it can undo the copy when it wasn't successful
 *  - Auto destination technique should be possible to turn off
 *  - Supporting callback function
 *  - May prevent some issues on shared enviroments : http://us3.php.net/umask
 * @param $source //file or folder
 * @param $dest ///file or folder
 * @param $options //folderPermission,filePermission
 * @return boolean
 */
function smartCopy($source, $dest, $options=array('folderPermission'=>0755,'filePermission'=>0755))
{
    $result=false;
   
    if (is_file($source)) {
        if ($dest[strlen($dest)-1]=='/') {
            if (!file_exists($dest)) {
                cmfcDirectory::makeAll($dest,$options['folderPermission'],true);
            }
            $__dest=$dest."/".basename($source);
        } else {
            $__dest=$dest;
        }
        $result=copy($source, $__dest);
        chmod($__dest,$options['filePermission']);
       
    } elseif(is_dir($source)) {
        if ($dest[strlen($dest)-1]=='/') {
            if ($source[strlen($source)-1]=='/') {
                //Copy only contents
            } else {
                //Change parent itself and its contents
                $dest=$dest.basename($source);
                @mkdir($dest);
                chmod($dest,$options['filePermission']);
            }
        } else {
            if ($source[strlen($source)-1]=='/') {
                //Copy parent directory with new name and all its content
                @mkdir($dest,$options['folderPermission']);
                chmod($dest,$options['filePermission']);
            } else {
                //Copy parent directory with new name and all its content
                @mkdir($dest,$options['folderPermission']);
                chmod($dest,$options['filePermission']);
            }
        }

        $dirHandle=opendir($source);
        while($file=readdir($dirHandle))
        {
            if($file!="." && $file!="..")
            {
                 if(!is_dir($source."/".$file)) {
                    $__dest=$dest."/".$file;
                } else {
                    $__dest=$dest."/".$file;
                }
                //echo "$source/$file ||| $__dest<br />";
                $result=smartCopy($source."/".$file, $__dest, $options);
            }
        }
        closedir($dirHandle);
       
    } else {
        $result=false;
    }
    return $result;
}


