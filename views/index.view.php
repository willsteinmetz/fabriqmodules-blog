<?php if ($isAdmin): ?>
<p>
	<button type="button" onclick="window.location = '<?php echo PathMap::build_path('blog', 'create'); ?>';">Add blog entry</button>
</p>
<?php
endif;
if ($blog->count() > 0):
	foreach ($blog as $entry):
?>
<h3><a href="<?php echo ($entry->customPath->count() > 0) ? $entry->customPath->path : PathMap::build_path('blog', 'show', $entry->id); ?>"><?php echo $entry->title; ?></a></h3>
<p style="font-size: 9pt; color: #999;">
	<strong><?php echo date('F j, D g:i a', strtotime($entry->created)); ?></strong> by <?php echo $entry->user->display; ?>
</p>
<?php
		echo $entry->body;
		if ($taxonomyEnabled && ($entry->terms->count() > 0)):
?>
<p style="font-size: 9pt; color: #666;"><strong>Tags:</strong> <?php
			for ($i = 0; $i < $entry->terms->count(); $i++) {
				echo $entry->terms[$i]->term;
				if ($i < ($entry->terms->count() - 1)) {
					echo ', ';
				}
			}
		endif;
		if ($isAdmin):
?></p>
<p>
	<button type="button" onclick="window.location = '<?php echo PathMap::build_path('blog', 'update', $entry->id); ?>';">Edit</button>
	<button type="button" onclick="window.location = '<?php echo PathMap::build_path('blog', 'destroy', $entry->id); ?>';">Delete</button>
</p>
<?php
		endif;
	endforeach;
else:
?>
<p><strong>There are currently no blog entries available</strong></p>
<?php endif; ?>
