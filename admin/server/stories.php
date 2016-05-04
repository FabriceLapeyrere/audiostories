<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice.lapeyrere@surlefil.org>
 */
	class Stories
	{
		// class object constructor
		function __construct()
		{
			// file location for the user database
			$dbfile = "../data/db/stories.db";

			// do we need to build a new database?
			$rebuild = false;
			if(!file_exists($dbfile)) { $rebuild = true; }

			// bind the database handler
			$this->database = new PDO("sqlite:" . $dbfile);

			// If we need to rebuild, the file will have been automatically made by the PDO call,
			// but we'll still need to define the user table before we can use the database.
			if($rebuild) { $this->rebuild_database($dbfile); }

		}

		// this function rebuilds the database if there is no database to work with yet
		function rebuild_database($dbfile)
		{
			$this->database->beginTransaction();
			$create = "CREATE TABLE stories (id INTEGER PRIMARY KEY AUTOINCREMENT, nom TEXT, desc TEXT, pitch TEXT, couleur TEXT, sons TEXT, photos TEXT, statut INTEGER, date INTEGER, creationdate INTEGER, createdby INTEGER, modificationdate INTEGER, modifiedby INTEGER)";
			$this->database->exec($create);
			$create = "CREATE TABLE tags (id INTEGER PRIMARY KEY AUTOINCREMENT, nom INTEGER, color TEXT, id_parent INTEGER, creationdate INTEGER, createdby INTEGER, modificationdate INTEGER, modifiedby INTEGER)";
			$this->database->exec($create);
			$create = "CREATE TABLE tag_story (id TEXT PRIMARY KEY, id_tag INTEGER, id_story INTEGER, date INTEGER)";
			$this->database->exec($create);
			$create = "CREATE TABLE trash (id INTEGER PRIMARY KEY AUTOINCREMENT, id_item INTEGER, type TEXT, json TEXT, date INTEGER, by INTEGER)";
			$this->database->exec($create);
			//seed
			
			//include 'seed.php';

			$this->database->commit();
		}

//contacts	// get item by id
		function get_story($id)
		{
			$query = "SELECT * FROM stories WHERE id=$id";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res[]=build_story($row,"../");
			}
			return $res[0];
		}
		function get_mods($t) {
			return $this->get_stories('modificationdate', $t);
		}
		function get_adds($t) {
			return $this->get_stories('creationdate', $t);
		}
		function get_dels($t) {
			$query = "SELECT id_item, json FROM trash WHERE type='story' AND date>=$t";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res[]=json_decode($row['json']);
			}
			return $res;
		}
		function get_tags_mods($t) {
			return $this->get_tags('modificationdate', $t);
		}
		function get_tag_story_adds($t) {
			$query = "SELECT id_tag, id_story FROM tag_story WHERE date>=$t";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res[]=$row;
			}
			return $res;
		}
		function get_tag_story_dels($t) {
			$query = "SELECT id_item, json FROM trash WHERE type='tag_story' AND date>=$t";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res[]=json_decode($row['json']);
			}
			return $res;
		}
		function get_tags_adds($t) {
			return $this->get_tags('creationdate', $t);
		}
		function get_tags_dels($t) {
			$query = "SELECT id_item, json FROM trash WHERE type='tag' AND date>=$t";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res[]=json_decode($row['json']);
			}
			return $res;
		}
		function get_stories($typedate='',$t=0) {
			$query = "SELECT * FROM stories";
			if ($typedate!='') {
				$query .= "
				WHERE $typedate>$t";
			}
			$stories=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$stories[]=build_story($row,"../");
			}
			if ($typedate=='') {
				$S=array();
				$i=0;
				foreach($stories as $s){
					while($i<$s['id']){
						$S[]=array('id'=>0);
						$i++;
					}
					$S[]=$s;
					$i++;
				}
				return $S;
			} else {
				return $stories;
			}
		}
		function touch_story($id_story) {
			$update = $this->database->prepare('UPDATE stories SET modificationdate=?, modifiedby=? WHERE id=?');
			$update->execute(array(millisecondes(), $_SESSION['user']['id'], $id_story));
			return 1;
		}
		function del_file($params)
		{
			$id=$params->id;
			$file=$params->file;
			$path_parts = pathinfo($file->filename);
			unlink("../data/files/story/$id/".$file->filename);
			foreach(glob("../data/files/story/$id/min/".$path_parts['filename']."*") as $f) {
				unlink($f);
			}
			$this->touch_story($id);
			return 1;
		}
		function get_tags($typedate='',$t=0) {
			$query = "SELECT * FROM tags";
			if ($typedate!='') {
				$query .= "
				WHERE $typedate>$t";
			}
			$tags=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				if (strlen($row['color'])==0) $row['color']='#333333';
				$tags[]=$row;
			}
			if ($typedate=='') {
				$T=array();
				$i=0;
				foreach($tags as $t){
					while($i<$t['id']){
						$T[]=array('id'=>0);
						$i++;
					}
					$T[]=$t;
					$i++;
				}
				return $T;
			} else {
				return $tags;
			}
		}
		function get_tag($id) {
			$query = "SELECT * FROM tags WHERE id=$id";
			$tags=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				if (strlen($row['color'])==0) $row['color']='#333333';
				$tags[]=$row;
			}
			return $tags[0];
		}
		function get_tags_story($id_story) {
			$query = "SELECT id_tag FROM tag_story as t1 inner join tags as t2 on t1.id_tag=t2.id WHERE id_story=$id_story ORDER BY t2.nom";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res[]=$row['id_tag'];
			}
			return $res;
		}
		function get_stories_tag($id_tag) {
			$query = "SELECT id_story FROM tag_story WHERE id_tag=$id_tag ";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res[]=$row['id_story'];
			}
			return $res;
		}
		function mod_story($params) {
			$nom=isset($params->nom) ? $params->nom : '';
			$desc=isset($params->desc) ? $params->desc : '';
			$pitch=isset($params->pitch) ? $params->pitch : '';
			$couleur=isset($params->couleur) ? $params->couleur : ['#ffffff','#ffffff','#ffffff','#ffffff'];
			$id=isset($params->id) ? $params->id : '';
			$sons=isset($params->sons) ? $params->sons : array();
			$photos=isset($params->photos) ? $params->photos : array();
			$statut=isset($params->statut) ? $params->statut : 0;
			$date=isset($params->date) ? $params->date : 0;
			$update = $this->database->prepare('UPDATE stories SET nom=?, desc=?, pitch=?, couleur=?, sons=?, photos=?, statut=?, date=?, modificationdate=?, modifiedby=? WHERE id=?');
			$update->execute(array($nom,$desc,$pitch,json_encode($couleur),json_encode($sons),json_encode($photos),$statut,$date, millisecondes(),$_SESSION['user']['id'],$id));
			check_data($id,$statut,"../");
			return 1;
		}
		function del_story($params) {
			$id=$params->story->id;
			$story=$params->story;
			$insert = $this->database->prepare('INSERT INTO trash (id_item, type, json, date , by) VALUES (?,?,?,?,?) ');
			$insert->execute(array($id,'story',json_encode($story),millisecondes(),$_SESSION['user']['id']));
			$delete = $this->database->prepare('DELETE FROM stories WHERE id=? ');
			$delete->execute(array($id));
			return 1;
		}
		function add_story($params) {
			$nom=$params->story->nom;
			$insert = $this->database->prepare('INSERT INTO stories (nom, desc, pitch, couleur, sons, photos, statut, date,  creationdate, createdby, modificationdate, modifiedby) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
			$insert->execute(array($nom, '', '', json_encode(array('#FFFFFF','#FF0000','#00FF00','#0000FF')), '{}', json_encode(array('une'=>'','paires'=>array())), 0, millisecondes(), millisecondes(), $_SESSION['user']['id'], millisecondes(), $_SESSION['user']['id']));
			$id = $this->database->lastInsertId();
			return $id;
		}
		function move_tag($params) {
			$delete = $this->database->prepare('UPDATE tags SET id_parent=?, modificationdate=?, modifiedby=? WHERE id=? ');
			$delete->execute(array($params->parent->id,millisecondes(),$_SESSION['user']['id'],$params->tag->id));
			return 1;
		}
		function mod_tag($params) {
			$delete = $this->database->prepare('UPDATE tags SET nom=?, color=?, modificationdate=?, modifiedby=? WHERE id=? ');
			$delete->execute(array($params->tag->nom,$params->tag->color,millisecondes(),$_SESSION['user']['id'],$params->tag->id));
			return 1;
		}
		function add_tag($params) {
			$delete = $this->database->prepare('INSERT INTO tags (nom, color, id_parent, creationdate, createdby, modificationdate, modifiedby) VALUES (?,?,?,?,?,?,?) ');
			$delete->execute(array($params->tag->nom,$params->tag->color,0,millisecondes(),$_SESSION['user']['id'],millisecondes(),$_SESSION['user']['id']));
			return 1;
		}
		function add_story_tag($params) {
			$insert = $this->database->prepare('INSERT INTO tag_story (id,id_tag,id_story,date) VALUES (?,?,?,?)');
			$insert->execute(array($params->tag->id."|".$params->story->id,$params->tag->id,$params->story->id,millisecondes()));
			return 1;
		}
		function del_story_tag($params) {
			$insert = $this->database->prepare('INSERT INTO trash (id_item, type, json, date , by) VALUES (?,?,?,?,?) ');
			$insert->execute(array($params->tag->id."|".$params->story->id,'tag_story',json_encode(array('id_tag'=>$params->tag->id,'id_story'=>$params->story->id)),millisecondes(),$_SESSION['user']['id']));
			$delete = $this->database->prepare('DELETE FROM tag_story WHERE id_tag=? AND id_story=? ');
			$delete->execute(array($params->tag->id,$params->story->id));
			return 1;
		}
	}
?>
