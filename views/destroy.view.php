<?php
/**
 * @file destroy.view.php
 * @author Will Steinmetz
 */

if ($notFound):
?>
<h1>Not Found</h1>
<p>Blog entry could not be found</p>
<?php
else:
	if ($submitted):
?>
<h1>Entry deleted</h1>
<p>Blog entry has been deleted.</p>
<p><a href="<?php echo PathMap::build_path('blog'); ?>">Return to blog entry list</a></p>
	<?php else: ?>
<h1>Delete blog entry?</h1>
<form method="post" action="<?php echo PathMap::build_path('blog', 'destroy', $blog->id); ?>">
	<p>Are you sure you want to delete the blog entry "<?php echo $blog->title; ?>?"</p>
	<p>
		<input type="submit" name="submit" value="Delete blog entry" />
		<a href="<?php echo PathMap::build_path('blog'); ?>">Cancel</a>
	</p>
</form>	
<?php
	endif;
endif;
?>
