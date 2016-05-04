<?php
	class User
	{
		// class object constructor
		function __construct()
		{
			// file location for the user database
			$dbfile = "../data/db/user.db";

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
			$create = "CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, login TEXT, name TEXT, password TEXT, prefs TEXT);";
			$this->database->exec($create);
			$password=md5('adminadmin');
			$prefs=array();
			$prefs['panier']=array();
			$select = $this->database->prepare("INSERT INTO users (login, name, password, prefs) VALUES (?,?,?,?)");
			$select->execute(array('admin', 'Admin', $password, json_encode($prefs)));
			$this->database->commit();
		}

		// check login/password
		function check($login,$password)
		{
			$password=md5($login.$password);
			$query = "SELECT id, login, name FROM users WHERE login='$login' and password='$password'";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res=$row;
			}
			return $res;
		}
		// create new user
		function create($login,$name,$password)
		{
			$password=md5($login.$password);
			$prefs=array();
			$prefs['panier']=array();
			$insert = $this->database->prepare('INSERT INTO users (login, name, password, prefs) VALUES (?,?,?,?) ');
			$insert->execute(array($login,$name,$password,json_encode($prefs)));
			$id=$this->database->lastInsertId();
			return $id;
		}
		function update($id,$login,$name,$password)
		{
			if ($password=='') {
				$update = $this->database->prepare('UPDATE users set name=? WHERE id=?');
				$update->execute(array($name,$id));
			} else {
				$password=md5($login.$password);
				$update = $this->database->prepare('UPDATE users set name=?, password=? WHERE id=?');
				$update->execute(array($name,$password,$id));
			} 
			return $this->get_user($id);
		}
		function mod_prefs($params)
		{
			$query = "SELECT prefs FROM users WHERE id=".$_SESSION['user']['id'];
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res=$row['prefs'];
			}
			$prefs=json_decode($res);
			if (isset($params->panier)) {
				$prefs->panier=$params->panier;
			}
			$update = $this->database->prepare('UPDATE users set prefs=? WHERE id=?');
			$update->execute(array(json_encode($prefs),$_SESSION['user']['id']));
			return 1;
		}
		function add_panier($params)
		{
			$query = "SELECT prefs FROM users WHERE id=".$_SESSION['user']['id'];
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res=$row['prefs'];
			}
			$prefs=json_decode($res);
			if (isset($params->nouveaux)) {
				$prefs->panier=array_merge($prefs->panier, $params->nouveaux);
			}
			$update = $this->database->prepare('UPDATE users set prefs=? WHERE id=?');
			$update->execute(array(json_encode($prefs),$_SESSION['user']['id']));
			return 1;
		}
		function panier_all($params)
		{
			$query = "SELECT prefs FROM users WHERE id=".$_SESSION['user']['id'];
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res=$row['prefs'];
			}
			$prefs=json_decode($res);
			$contacts=new Contacts();
			$tab=$contacts->get_id_cass_filtre($params->filtre);
			if (count($tab)>0) {
				$prefs->panier=array_values(array_unique(array_merge($prefs->panier, $tab)));
			}
			$update = $this->database->prepare('UPDATE users set prefs=? WHERE id=?');
			$update->execute(array(json_encode($prefs),$_SESSION['user']['id']));
			return $prefs->panier;
		}
		function del_panier($params)
		{
			$query = "SELECT prefs FROM users WHERE id=".$_SESSION['user']['id'];
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res=$row['prefs'];
			}
			$prefs=json_decode($res);
			if (isset($params->nouveaux)) {
				$prefs->panier=array_values(array_diff($prefs->panier, $params->nouveaux));
			}
			$update = $this->database->prepare('UPDATE users set prefs=? WHERE id=?');
			$update->execute(array(json_encode($prefs),$_SESSION['user']['id']));
			return 1;
		}
		function del($id)
		{
			if ($id!=1){
				$del = $this->database->prepare('DELETE FROM users WHERE id=?');
				$del->execute(array($id));
			}
			return $id;
		}
		function get_users()
		{
			$query = "SELECT id, login, name FROM users";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res[]=$row;
			}
			return $res;
		}
		function get_panier()
		{
			$query = "SELECT prefs FROM users WHERE id=".$_SESSION['user']['id'];
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res=$row['prefs'];
			}
			$prefs=json_decode($res);
			return $prefs->panier;
		}
		function get_users_list()
		{
			$query = "SELECT id, name FROM users";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res[]=$row;
			}
			return $res;
		}
		function get_user($id)
		{
			$query = "SELECT id, login, name FROM users WHERE id=$id";
			$res=array();
			foreach($this->database->query($query, PDO::FETCH_ASSOC) as $row){
				$res=$row;
			}
			return $res;
		}
	}
?>
