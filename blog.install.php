<?php

class blog_install {
	public function install() {
		$mod = new Modules();
		$mod->getModuleByName('blog');
		$perms = array(
			'view blog posts',
			'create blog posts',
			'update blog posts',
			'delete blog posts'
		);
		
		$perm_ids = FabriqModules::register_perms($mod->id, $perms);
		
		global $db;
		$sql = "CREATE TABLE IF NOT EXISTS `fabmod_blog_blogs` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`title` VARCHAR(100) NOT NULL,
			`body` TEXT NOT NULL,
			`locked` TINYINT(1) NOT NULL DEFAULT 0,
			`user` INT(11) NOT NULL,
			`created` DATETIME NOT NULL,
			`updated` DATETIME NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=INNODB;";
		$db->query($sql);
		$sql = "ALTER TABLE `fabmod_blog_blogs`
			ADD CONSTRAINT `fk_blog_user`
			FOREIGN KEY (`user`)
			REFERENCES `fabmod_users_users`(`id`)
			ON DELETE CASCADE";
		$db->query($sql);
		
		// map paths
		$pathmap = &FabriqModules::module('pathmap');
		$pathmap->register_path('blog/!#', 'blog', 'index', 'module', null, 1);
		$pathmap->register_path('blog/create', 'blog', 'create', 'module');
		$pathmap->register_path('blog/update/!#', 'blog', 'update', 'module', null, 2);
		$pathmap->register_path('blog/destroy/!#', 'blog', 'destroy', 'module', null, 2);
		$pathmap->register_path('blog/show/!#', 'blog', 'show', 'module', null, 2);
		
		// set module as installed
		$mod->installed = 1;
		$mod->update();
	}
	
	public function update_1_3() {
		$mod = new Modules();
		$mod->getModuleByName('blog');
		
		$mod->versioninstalled = '1.3';
		$mod->update();
	}
	
	public function update_1_5() {
		$mod = new Modules();
		$mod->getModuleByName('blog');
		
		$mod->versioninstalled = '1.5';
		$mod->update();
	}
	
	public function update_1_5_1() {
		$mod = new Modules();
		$mod->getModuleByName('blog');
		
		$mod->versioninstalled = '1.5.1';
		$mod->update();
	}
	
	public function uninstall() {
		$mod = new Modules();
		$mod->getModuleByName('blog');
		
		// remove perms
		FabriqModules::remove_perms($mod->id);
		
		// remove paths
		$pathmap = &FabriqModules::module('pathmap');
		$pathmap->remove_path('blog');
		$pathmap->remove_path('blog/create');
		$pathmap->remove_path('blog/update/!#');
		$pathmap->remove_path('blog/destroy/!#');
		$pathmap->remove_path('blog/show/!#');
		
		// delete database table
		global $db;
		$sql = "DROP TABLE `fabmod_blog_blogs`;";
		$db->query($sql);
		
		// uninstall any terms
		if (FabriqModules::enabled('taxonomy')) {
			FabriqModules::module('taxonomy')->uninstallMaps('fabmod_blog_blogs');
		}
		
		// set module as not installed
		$mod->installed = 0;
		$mod->update();
	}
}
	