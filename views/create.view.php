<h1>Create blog entry</h1>
<?php
if ($submitted && Messaging::has_messages()) {
	Messaging::display_messages();
}
?>
<form method="post" action="<?php echo PathMap::build_path('blog', 'create'); ?>">
<?php if ($submitted && is_numeric($_POST['id'])): ?>
	<input type="hidden" name="id" value="<?php echo $_POST['id']; ?>" />
<?php endif; ?>
	<div style="padding: 2px;">
		<label for="title">Title:</label>
		<input type="text" name="title" size="50" maxlength="100"<?php if ($submitted) { echo " value=\"{$_POST['title']}\""; } ?> />
	</div>
	<div style="padding: 2px;">
		<label for="body">Body:</label><br />
		<textarea name="body" style="width: 100%; height: 300px;"><?php if ($submitted) { echo $_POST['body']; } ?></textarea>
	</div>
	<div style="padding: 2px;">
		<input type="checkbox" name="locked" value="1"<?php if ($submitted && isset($_POST['locked']) && ($_POST['locked'] == 1)) { echo " checked=\"checked\""; } ?> />
		<label for="locked"> Locked</label>
		<div style="font-size: 9pt; color: #999;"><strong>NOTE: locked blog entries can only be seen by administrators.</strong></div>
	</div>
<?php
if ($pathmapEnabled) {
	echo FabriqModules::render_now('pathmap', 'create');
}
if ($taxonomyEnabled) {
	echo FabriqModules::render_now('taxonomy', 'termsList');
}
?>
	<div style="padding: 2px;">
		<input type="submit" name="submit" value="Create blog entry" />
	</div>
</form>
