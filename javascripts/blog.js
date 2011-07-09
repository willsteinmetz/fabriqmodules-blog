/**
 * @file blog.js
 * @author Will Steinmetz
 * Blog JavaScript functionality
 */

$(function() {
	// add tinyMCE
	$('textarea[name="body"]').tinymce({
		mode: 'textareas',
		theme: 'advanced',
		skin: 'o2k7',
		skin_variant: 'black',
		theme_advanced_toolbar_location: 'top',
		theme_advanced_toolbar_align: 'left',
		theme_advanced_resizing: true,
		theme_advanced_statusbar_location: 'bottom',
		remove_linebreaks: false
	});
});
