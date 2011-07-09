<?php
/**
 * @file blog.module.php
 * @author Will Steinmetz
 */

class blog_module extends FabriqModule {
	function __construct() {
		parent::__construct();
	}
	
	public function index() {
		if (FabriqModules::module('roles')->requiresPermission('view blog posts', 'blog')) {
			Fabriq::title('Blog');
			$blog = FabriqModules::new_model('blog', 'Blogs');
			$blog->getAll((PathMap::arg(1)) ? PathMap::arg(1) : 0);
			FabriqModules::set_var('blog', 'blog', $blog);
			$isAdmin = FabriqModules::module('roles')->userHasPermission('update blog posts', 'blog');
			FabriqModules::set_var('blog', 'isAdmin', $isAdmin);
			$taxonomyEnabled = FabriqModules::enabled('taxonomy');
			FabriqModules::set_var('blog', 'taxonomyEnabled', $taxonomyEnabled);
		}
	}
	
	public function create() {
		if (FabriqModules::module('roles')->requiresPermission('create blog posts', 'blog')) {
			Fabriq::title('Create blog entry');
			
			if (file_exists('libs/javascript/tiny_mce/tiny_mce.js')) {
				FabriqLibs::js_lib('tiny_mce', 'tiny_mce');
				FabriqLibs::js_lib('jquery.tinymce', 'tiny_mce');
				FabriqModules::add_js('blog', 'blog');
			}
			
			// determine if extension modules are enabled
			$pathmapEnabled = FabriqModules::enabled('pathmap');
			FabriqModules::set_var('blog', 'pathmapEnabled', $pathmapEnabled);
			if ($pathmapEnabled) {
				FabriqModules::set_var('pathmap', 'pathmap_controller', 'blog');
				FabriqModules::set_var('pathmap', 'pathmap_action', 'show');
				FabriqModules::set_var('pathmap', 'pathmap_modpage', 'module');
			}
			$taxonomyEnabled = FabriqModules::enabled('taxonomy');
			FabriqModules::set_var('blog', 'taxonomyEnabled', $taxonomyEnabled);
			if ($taxonomyEnabled && !isset($_POST['submit'])) {
				FabriqModules::module('taxonomy')->termsList('blog', $blog->db_table, $blog->id);
			}
			
			if (isset($_POST['submit'])) {
				$blog = FabriqModules::new_model('blog', 'Blogs');
				if (isset($_POST['id']) && is_numeric($_POST['id'])) {
					$blog->find($_POST['id']);
				}
				$blog->title = trim($_POST['title']);
				$blog->body = trim($_POST['body']);
				$blog->locked = (isset($_POST['locked']) && ($_POST['locked'] == 1)) ? 1 : 0;
				$blog->user = $_SESSION['FABMOD_USERS_userid'];
				
				if (strlen($blog->title) == '') {
					Messaging::message('Blog title is required');
				}
				if (strlen($blog->body) == '') {
					Messaging::message('Blog body is required');
				}
				
				if (!Messaging::has_messages()) {
					if (isset($_POST['id'])) {
						$blog->update();
					} else {
						$blog->id = $blog->create();
						FabriqModules::trigger_event($this->name, 'create', 'blog entry created', $blog);
					}
					
					// create map if needed
					if ($pathmapEnabled) {
						$_POST['id'] = $blog->id;
						$_POST['pathmap_extra'] = $blog->id;
						$_POST['pathmap_wildcard'] = NULL;
						FabriqModules::module('pathmap')->create();
					}
					// add taxonomy if available
					if ($taxonomyEnabled) {
						FabriqModules::module('taxonomy')->termsList('blog', $blog->db_table, $blog->id);
					}
					if (!Messaging::has_messages()) {
						header('Location: ' . PathMap::build_path('blog'));
						exit();
					}
				}
				
				FabriqModules::set_var('blog', 'submitted', true);
			}
		}
	}
	
	public function update() {
		if (FabriqModules::module('roles')->requiresPermission('update blog posts', 'blog')) {
			$blog = FabriqModules::new_model('blog', 'Blogs');
			$blog->find(PathMap::arg(2));
			
			if (($blog->title != '') && (($blog->locked == 0) || FabriqModules::module('roles')->requiresPermission('update blog posts', 'blog'))) {
				Fabriq::title('Update blog entry');
			
				if (file_exists('libs/javascript/tiny_mce/tiny_mce.js')) {
					FabriqLibs::js_lib('tiny_mce', 'tiny_mce');
					FabriqLibs::js_lib('jquery.tinymce', 'tiny_mce');
					FabriqModules::add_js('blog', 'blog');
				}
				
				// determine if extension modules are enabled
				$pathmapEnabled = FabriqModules::enabled('pathmap');
				FabriqModules::set_var('blog', 'pathmapEnabled', $pathmapEnabled);
				if ($pathmapEnabled) {
					FabriqModules::module('pathmap')->start_update('blog', 'show', $blog->id);
					FabriqModules::set_var('pathmap', 'pathmap_controller', 'blog');
					FabriqModules::set_var('pathmap', 'pathmap_action', 'show');
					FabriqModules::set_var('pathmap', 'pathmap_modpage', 'module');
				}
				$taxonomyEnabled = FabriqModules::enabled('taxonomy');
				FabriqModules::set_var('blog', 'taxonomyEnabled', $taxonomyEnabled);
				if ($taxonomyEnabled) {
					FabriqModules::module('taxonomy')->termsList('blog', $blog->db_table, $blog->id);
				}
				
				if (isset($_POST['submit'])) {
					$blog->title = trim($_POST['title']);
					$blog->body = trim($_POST['body']);
					$blog->locked = (isset($_POST['locked']) && ($_POST['locked'] == 1)) ? 1 : 0;
					$blog->user = $_SESSION['FABMOD_USERS_userid'];
					
					if (strlen($blog->title) == '') {
						Messaging::message('Blog title is required');
					}
					if (strlen($blog->body) == '') {
						Messaging::message('Blog body is required');
					}
					
					if (!Messaging::has_messages()) {
						$blog->update();
						FabriqModules::trigger_event($this->name, 'create', 'blog entry updated', $blog);
						
						// create map if needed
						if ($pathmapEnabled) {
							$_POST['pathmap_extra'] = $blog->id;
							$_POST['pathmap_wildcard'] = NULL;
							FabriqModules::module('pathmap')->update('blog', 'show', $blog->id);
						}
						// add taxonomy if available
						if ($taxonomyEnabled) {
							FabriqModules::module('taxonomy')->termsList('blog', $blog->db_table, $blog->id);
						}
						if (!Messaging::has_messages()) {
							header('Location: ' . PathMap::build_path('blog'));
							exit();
						}
					}
					
					FabriqModules::set_var('blog', 'submitted', true);
				}
				FabriqModules::set_var('blog', 'blog', $blog);
			} else {
				FabriqModules::set_var('blog', 'notFound', true);
				Fabriq::title('Blog entry not found');
			}
		}
	}
	
	public function destroy() {
		if (FabriqModules::module('roles')->requiresPermission('delete blog posts', 'blog')) {
			$blog = FabriqModules::new_model('blog', 'Blogs');
			$blog->find(PathMap::arg(2));
			
			if ($blog->title != '') {
				Fabriq::title('Delete entry?');
				FabriqModules::set_var('blog', 'blog', $blog);
				
				if (isset($_POST['submit'])) {
					$blog->destroy();
					FabriqModules::trigger_event($this->name, 'destroy', 'blog entry deleted', $blog);
					FabriqModules::set_var('blog', 'submitted', true);
				}
			} else {
				FabriqModules::set_var('blog', 'notFound', true);
				Fabriq::title('Blog entry not found');
			}
		}
	}
	
	public function show($entry) {
		if (FabriqModules::module('roles')->requiresPermission('view blog posts', 'blog')) {
			$blog = FabriqModules::new_model('blog', 'Blogs');
			if (!$entry || !is_numeric($entry)) {
				$blog->find(PathMap::arg(2));
			} else {
				$blog->find($entry);
			}
			
			if (($blog->title != '') && (($blog->locked == 0) || FabriqModules::module('roles')->requiresPermission('update blog posts', 'blog'))) {
				Fabriq::title('Blog - ' . $blog->title);
				$user = FabriqModules::new_model('users', 'Users');
				$user->find($blog->user);
				$blog->user = $user;
				$taxonomyEnabled = FabriqModules::enabled('taxonomy');
				FabriqModules::set_var('blog', 'taxonomyEnabled', $taxonomyEnabled);
				FabriqModules::set_var('blog', 'blog', $blog);
				$isAdmin = FabriqModules::module('roles')->userHasPermission('update blog posts', 'blog');
				FabriqModules::set_var('blog', 'isAdmin', $isAdmin);
			} else {
				FabriqModules::set_var('blog', 'notFound', true);
				Fabriq::title('Blog entry not found');
			}
		}
	}
}
