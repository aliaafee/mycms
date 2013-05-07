<?php
/*
	mycms
	dbConnection
*/

class dbConnection {
	protected $setting;
	public $db;
	
	public function __construct($settings) {
		$this->setting = $settings;
	}
	
	public function __destruct() {
	
	}
	
	public function connect() {
		if ($this->db = new SQLiteDatabase($this->setting['database']['file'])) {
			return true;
		} else {
			return false;
		}
	}
	
	public function disconnect() {
		return true;
	}
	
	public function getSubMenu($parent,$includeDisabled=false) {
		if (!is_numeric($parent)) { return false;}
		
		if ($includeDisabled) {
			$query = "SELECT id,title,parent,sort,enabled,type FROM content WHERE (parent=$parent AND type=0) ORDER BY sort";
		} else {
			$query = "SELECT id,title,parent,sort,enabled,type FROM content WHERE (parent=$parent AND type=0) AND enabled=1 ORDER BY sort";
		}
		
		$q = @$this->db->query($query);
		if ($q === false) {
			$this->createContentTable();
			$q = $this->db->query($query);
		}
		return $q->fetchAll();
	}
	
	public function getContent($id,$includeDisabled=false) {
		if (!is_numeric($id)) { return false;}
		
		if ($includeDisabled) {
			$query = 'SELECT title,parent,content,enabled,type,date FROM content WHERE id='.$id.' AND type=0';	
		} else {
			$query = "SELECT title,parent,content,enabled,type,date FROM content WHERE (id=$id AND type=0) AND enabled=1";
		}
		
		$q = @$this->db->query($query);
		if ($q === false) {
			$this->createContentTable();
			$q = $this->db->query($query);
		}
		return $q->fetch();
	}
	
	public function getInfo($id) {
		if (!is_numeric($id)) { return false;}
		$q = @$this->db->query('SELECT id,parent,title FROM content WHERE id='.$id);
		if ($q === false) {
			$this->createContentTable();
			$q = $this->db->query('SELECT id,parent,title FROM content WHERE id='.$id);
		}
		return $q->fetch();
	}
	
	public function search($query) {
		$query = sqlite_escape_string($query);
		$q = $this->db->query("SELECT id,title,content,enabled FROM content WHERE enabled=1 AND (content LIKE '%$query%' OR title LIKE '%$query%')");
		return $q->fetchAll();
	}
	
	public function insertContent($parent,$sort,$enabled,$title,$content,$date) {
		$parent = sqlite_escape_string($parent);
		$sort = sqlite_escape_string($sort);
		$enabled = sqlite_escape_string($enabled);
		$type = 0;
		$date = sqlite_escape_string($date);
		$title = sqlite_escape_string($title);
		$content = sqlite_escape_string($content);
		$q = @$this->db->query("SELECT max(sort) FROM content WHERE parent='$parent'");
		$max = $q->fetchSingle();
		if ($max == '') { $max = -1; }
		$sort = $max + 1;
		
		return $this->db->queryExec("INSERT INTO content VALUES (NULL,'$parent','$sort','$enabled','$type','$date','$title','$content');");
	}
	
	public function removeContent($id,$recursive=false) {
		if (!is_numeric($id)) { return false;}
		if ($recursive) {
			$menu = $this->getSubMenu($id,$includeDisabled=false);
			foreach ($menu as $key => $menuItem) {
				$this->removeContent($menuItem['id'],$recursive);
			}
		}
		return $this->db->queryExec("DELETE FROM content WHERE id=$id");
	}
	
	public function updateContent($id,$title,$content,$enabled) {
		if (!is_numeric($id)) { return false;}
		$title = sqlite_escape_string($title);
		$content = sqlite_escape_string($content);
		return $this->db->queryExec("UPDATE content SET title='$title', content='$content', enabled='$enabled' WHERE id=$id");
	}
	
	public function orderUp($id) {
		if (!is_numeric($id)) { return false;}
		$query = "SELECT id,parent,sort FROM content WHERE parent=(SELECT parent FROM content WHERE id=$id LIMIT 1) AND (sort<=((SELECT sort FROM content WHERE id=$id LIMIT 1))) ORDER BY sort DESC LIMIT 2";
		$q = $this->db->query($query);
		
		$items = $q->fetchAll();
		if (count($items)==2) {
			return $this->db->queryExec(
				"UPDATE content SET sort='".$items[1]['sort']."' WHERE id=".$items[0]['id'].";".
				"UPDATE content SET sort='".$items[0]['sort']."' WHERE id=".$items[1]['id'].";"
			);
		}
		return false;
	}
	
	public function orderDown($id) {
		if (!is_numeric($id)) { return false;}
		$query = "SELECT id,parent,sort FROM content WHERE parent=(SELECT parent FROM content WHERE id=$id LIMIT 1) AND (sort>=((SELECT sort FROM content WHERE id=$id LIMIT 1))) ORDER BY sort LIMIT 2";
		$q = $this->db->query($query);
		
		$items = $q->fetchAll();
		if (count($items)==2) {
			return $this->db->queryExec(
				"UPDATE content SET sort='".$items[1]['sort']."' WHERE id=".$items[0]['id'].";".
				"UPDATE content SET sort='".$items[0]['sort']."' WHERE id=".$items[1]['id'].";"
			);
		}
		return false;
	}
	
	private function createContentTable() {
		//type	0=Site Page
		//	1=News Article
		return $this->db->queryExec(
			'CREATE TABLE content (
				id INTEGER AUTOINCREMENT PRIMARY KEY ,
				parent INTEGER,
				sort INTEGER,
				enabled INTEGER,
				type INTEGER,
				date TEXT,
				title TEXT,
				content BLOB
			);'
		);
	}
	
}
