<?php
/**
 * @file update.view.php
 * @author Will Steinmetz
 */

if ($notFound):
?>
<h1>Not Found</h1>
<p>Blog entry could not be found</p>
<?php else: ?>
<h1>Update blog entry</h1>
<?php
if ($submitted && Messaging::has_messages()) {
	Messaging::display_messages();
}
?>
<form method="post" action="<?php echo PathMap::build_path('blog', 'update', $blog->id); ?>">
	<div style="padding: 2px;">
		<label for="title">Title:</label>
		<input type="text" name="title" size="50" maxlength="100" value="<?php echo $blog->title; ?>" />
	</div>
	<div style="padding: 2px;">
		<label for="body">Body:</label><br />
		<textarea name="body" style="width: 100%; height: 300px;"><?php echo $blog->body; ?></textarea>
	</div>
	<div style="padding: 2px;">
		<input type="checkbox" name="locked" value="1"<?php if ($blog->locked == 1) { echo " checked=\"checked\""; } ?> />
		<label for="locked"> Locked</label>
		<div style="font-size: 9pt; color: #999;"><strong>NOTE: locked blog entries can only be seen by administrators.</strong></div>
	</div>
<?php
if ($pathmapEnabled) {
	echo FabriqModules::render_now('pathmap', 'update');
}
if ($taxonomyEnabled) {
	echo FabriqModules::render_now('taxonomy', 'termsList');
}
?>
	<div style="padding: 2px;">
		<input type="submit" name="submit" value="Update blog entry" />
	</div>
</form>
<?php endif; ?>
