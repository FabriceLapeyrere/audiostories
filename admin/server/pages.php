<?php
	class Pages
	{
		// class object constructor
		function __construct()
		{
			// file location for the user database
			$dbfile = "../data/db/pages.db";

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
			$create = "CREATE TABLE pages (id INTEGER PRIMARY KEY AUTOINCREMENT, nom TEXT, html TEXT, statut INTEGER, creationdate INTEGER, createdby INTEGER, modificationdate INTEGER, modifiedby INTEGER)";
			$this->database->exec($create);
			$create = "CREATE TABLE trash (id INTEGER PRIMARY KEY AUTOINCREMENT, id_item INTEGER, type TEXT, json TEXT, date INTEGER, by INTEGER)";
			$this->database->exec($create);
			//seed
			
			//include 'seed.php';

			$this->database->commit();
		}

		//contacts	// get item by id
		function get_page($id)
		{
			$query = "SELECT * FROM pages WHERE id=$id";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$row['statut']=$row['statut'] ? true : false;
				$res[]=$row;	
			}
			return $res[0];
		}
		function get_mods($t) {
			return $this->get_pages('modificationdate', $t);
		}
		function get_adds($t) {
			return $this->get_pages('creationdate', $t);
		}
		function get_dels($t) {
			$query = "SELECT id_item, json FROM trash WHERE type='page' AND date>=$t";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res[]=json_decode($row['json']);
			}
			return $res;
		}
		function get_pages($typedate='',$t=0) {
			$query = "SELECT * FROM pages";
			if ($typedate!='') {
				$query .= "
				WHERE $typedate>$t";
			}
			$pages=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$row['statut']=$row['statut'] ? true : false;
				$pages[]=$row;
			}
			if ($typedate=='') {
				$P=array();
				$i=0;
				foreach($pages as $p){
					while($i<$p['id']){
						$P[]=array('id'=>0);
						$i++;
					}
					$P[]=$p;
					$i++;
				}
				return $P;
			} else {
				return $pages;
			}
		}
		function touch_page($id_page) {
			$update = $this->database->prepare('UPDATE pages SET modificationdate=?, modifiedby=? WHERE id=?');
			$update->execute(array(millisecondes(), $_SESSION['user']['id'], $id_page));
			return 1;
		}
		function mod_page($params) {
			$id=isset($params->id) ? $params->id : '';
			$nom=isset($params->nom) ? $params->nom : '';
			$html=isset($params->html) ? $params->html : '';
			$statut=isset($params->statut) ? $params->statut : 0;
			$update = $this->database->prepare('UPDATE pages SET nom=?, html=?, statut=?, modificationdate=?, modifiedby=? WHERE id=?');
			$update->execute(array($nom,$html,$statut,millisecondes(),$_SESSION['user']['id'],$id));
			return 1;
		}
		function del_page($params) {
			$id=$params->page->id;
			$page=$params->page;
			$insert = $this->database->prepare('INSERT INTO trash (id_item, type, json, date , by) VALUES (?,?,?,?,?) ');
			$insert->execute(array($id,'page',json_encode($page),millisecondes(),$_SESSION['user']['id']));
			$delete = $this->database->prepare('DELETE FROM pages WHERE id=? ');
			$delete->execute(array($id));
			return 1;
		}
		function add_page($params) {
			$nom=$params->page->nom;
			$insert = $this->database->prepare('INSERT INTO pages (nom, html, statut, creationdate, createdby, modificationdate, modifiedby) VALUES (?,?,?,?,?,?,?)');
			$insert->execute(array($nom, '', 0, millisecondes(), $_SESSION['user']['id'], millisecondes(), $_SESSION['user']['id']));
			$id = $this->database->lastInsertId();
			return $id;
		}
	}
?>
