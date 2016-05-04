<?php
/**
 *
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Fabrice Lapeyrere <fabrice@surlefil.org>
 */
	class Pages
	{
		// class object constructor
		// class object constructor
		function __construct()
		{
			// file location for the user database
			$dbfile = "data/db/pages.db";

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
		function get_page($id)
		{
			$query = "SELECT * FROM pages WHERE id=$id";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res[]=$row;
			}
			return $res[0];
		}
		function get_pages() {
			$query = "SELECT * FROM pages";
			$pages=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$pages[]=$row;
			}
			return $pages;
		}
	}
?>
