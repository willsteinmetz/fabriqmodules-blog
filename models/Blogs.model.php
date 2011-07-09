<?php
/**
 * @file Blogs.model.php
 * @author Will Steinmetz
 * Blog entry model
 */

class blog_Blogs extends ModuleModel {
	function __construct() {
		parent::__construct('blog', array('title', 'body', 'locked', 'user'), 'blogs');
	}
	
	public function find($query = 'all') {
		parent::find($query);
		// get taxonomy if enabled
		if (FabriqModules::enabled('taxonomy')) {
			$this[0]->terms = FabriqModules::new_model('taxonomy', 'Terms');
			$this->terms->getMappedTerms($this->db_table, $this->id);
		}
		// look for a custom url
		if (FabriqModules::enabled('pathmap')) {
			$this[0]->customPath = FabriqModules::new_model('pathmap', 'Paths');
			$this->customPath->get_by_details('blog', 'show', $this->id);
		}
	}
	
	public function getAll($page = 0, $limit = 10) {
		global $db;
		
		$go = $page * $limit;
		if (FabriqModules::module('roles')->userHasPermission('update blog posts', 'roles')) {
			$query = "SELECT * FROM {$this->db_table} ORDER BY created DESC LIMIT ?, ?";
			$this->fill($db->prepare_select($query, $this->fields(), array($go, $limit)));
		} else {
			$query = "SELECT * FROM {$this->db_table} WHERE locked = ? ORDER BY created DESC LIMIT ?, ?";
			$this->fill($db->prepare_select($query, $this->fields(), array(0, $go, $limit)));
		}
		$found = array();
		for ($i = 0; $i < $this->count(); $i++) {
			// get user details
			if (!array_key_exists($this[$i]->user, $found)) {
				$user = FabriqModules::new_model('users', 'Users');
				$user->find($this[$i]->user);
				$found[$this[$i]->user] = $user;
				$this[$i]->user = $user;
			}
			// look for a custom url
			$this[$i]->customPath = FabriqModules::new_model('pathmap', 'Paths');
			$this[$i]->customPath->get_by_details('blog', 'show', $this[$i]->id);
		}
		
		// get terms if the module is enabled
		if (FabriqModules::enabled('taxonomy')) {
			for ($i = 0; $i < $this->count(); $i++) {
				$this[$i]->terms = FabriqModules::new_model('taxonomy', 'Terms');
				$this[$i]->terms->getMappedTerms($this->db_table, $this[$i]->id);
			}
		}
	}
}
