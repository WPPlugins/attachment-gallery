<?php
$path =  dirname(__FILE__).'/';
require_once $path.'AttachmentGalleryManager.class.php';

// load the filter first, it will only actually affect output
// if it finds [attachmentgallery] In the output
add_filter('the_content','attachmentGalleryHook',10);

// shortcode setup
function attachmentgallery_shortcode($atts){

	// this isn't being used, the shortcode is being post-parsed as
	// our output depends on page content being preloaded

}
add_shortcode('attachmentgallery', 'attachmentgallery_shortcode');
?>
