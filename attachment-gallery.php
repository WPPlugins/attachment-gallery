<?php
/*
Plugin Name: Attachment Galleries
Plugin URI: http://www.acumendevelopment.net
Description: Attachment Galleries for WP3.0
Author: Leo Brown
Version: 0.1
Author URI: http://www.acumendevelopment.net
*/

// load dependencies
$path = dirname(__FILE__).'/';
require_once $path.'AttachmentGalleryManager.class.php';// Core attachmentgallery features such as displaying attachmentgallerys
require_once $path.'AttachmentGalleryShortcode.php';	// Shortcode support
//require_once $path.'AttachmentGalleryWidget.class.php';	// Widget admin options

?>
