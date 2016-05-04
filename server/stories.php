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
			$dbfile = "data/db/stories.db";

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
			
			$this->database->commit();
		}

		function is_pub($id)
		{
			$query = "SELECT statut FROM stories WHERE id=$id";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res=$row;
			}
			return $res['statut'] ? true : false;
		}
		function get_story($id)
		{
			$query = "SELECT * FROM stories WHERE id=$id";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res=build_story($row,"");
			}
			return $res[0];
		}
		function get_stories() {
			$query = "SELECT * FROM stories";
			$query .= "
				ORDER BY date, rowid";
			$stories=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$stories[]=build_story($row,"");
			}
			foreach ($stories as $key => $row) {
				$dates[$key]  = $row['date'];
				$ids[$key] = $row['id'];
			}
			$bydate = array_orderby($stories, 'statut', SORT_DESC, 'date', SORT_ASC, 'id', SORT_ASC);
			$i=1;
			foreach($bydate as $k=>$s){
				$bydate[$k]['num']=$i;
				$i++;
			}
			$stories = array_orderby($bydate, 'num', SORT_DESC);
			return $stories;
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
	}
?>
