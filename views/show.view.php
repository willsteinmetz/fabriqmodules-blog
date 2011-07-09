<?php if (isset($notFound)): ?>
<h1>Not Found</h1>
<p>Blog entry could not be found</p>
<?php else: ?>
<h3><?php echo $blog->title; ?></h3>
<p style="font-size: 9pt; color: #999;">
	<strong><?php echo date('F j, D g:i a', strtotime($blog->created)); ?></strong> by <?php echo $blog->user->display; ?>
</p>
<?php
		echo $blog->body;
		if ($taxonomyEnabled && ($blog->terms->count() > 0)):
?>
<p style="font-size: 9pt; color: #666;"><strong>Tags:</strong> <?php
			for ($i = 0; $i < $blog->terms->count(); $i++) {
				echo $blog->terms[$i]->term;
				if ($i < ($blog->terms->count() - 1)) {
					echo ', ';
				}
			}
		endif;
		if ($isAdmin):
?></p>
<p>
	<button type="button" onclick="window.location = '<?php echo PathMap::build_path('blog', 'update', $blog->id); ?>';">Edit</button>
	<button type="button" onclick="window.location = '<?php echo PathMap::build_path('blog', 'destroy', $blog->id); ?>';">Delete</button>
</p>
	<?php endif; ?>
<p><a href="<?php echo PathMap::build_path('blog'); ?>">Return to blog</a></p>
<?php endif; ?>
