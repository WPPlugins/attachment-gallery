=== Attachment Gallery ===
Contributors: acumensystems
Donate link: http://www.acumendevelopment.net
Tags: attachments, pdfs, gallery
Requires at least: 2.5
Tested up to: 3.0
Stable tag: trunk

Shortcode providing thumbnail gallery of items attached to the current post (i.e. PDFs).

== Description ==

The Attachment Gallery plugin was developed by [Acumen](http://www.acumendevelopment.net/ "Acumen Development")
in order to provide an easy way for users to view and download attachments you've added to your page.

Once the plugin is installed, you will be able to include the attachment gallery in your posts
and pages using the `[attachmentgallery]` shortcode. Any page containing attachments (which Wordpress typically inserts as
HTML links) will have these attachments removed, and a structured HTML gallery containing those attachments inserted
where the shortcode was placed.

Note - requires the free ImageMagick software for thumbnailing - see install section for more details.

TODO: if rel="attachment then find the file type, check if it's mime matches our file type search
Get the GUID of the actual file, and return it

== Installation ==

This section describes how to install the plugin and get it working.

1. Check that ImageMagic is installed - for instance, on Linux you may use `yum -y install ImageMagick-devel` and `pecl install imagick`
1. Upload the plugin to it's own directory within `/wp-content/plugins/`
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Attach some items (i.e. PDFs to a page)
1. Shortcodes can now be added as `[attachmentgallery]` to any of your pages/posts with attachments
1. Contact us with your feedback!

== Frequently Asked Questions ==

= What attachment types are supported for thumbnailing? =

Currently only PDFs are supported, but we are able to expand this easily on request. Immediate upcoming considerations would be
document types such as Word, Excel, Powerpoint and their open source equivalents, along with bitmap image formats such as JPG, PNG, TIFF, and BMP, and vector 
image formats such as PSD and SVG.

= How can I style the Galleries? =

You can style them with the CSS classes `attachment_gallery`, `attachment_gallery_item`, `attachment_gallery_item_title` etc, as so:

	.attachment_gallery{
		background-color:#eee;
		border:solid 1px #888;
		padding:10px;
	}
	.attachment_gallery_item{
		background-color:#fff;
		border:solid 1px #888;
		padding:10px;
	}
	.attachment_gallery_item_download{
		text-align:right;
		display:block;
		width:100%;
		margin:5px;
	}
	.attachment_gallery_item_thumbnail{
		border:solid 1px #ccc;
		float:left;
		margin-right:10px;
		width:100px;
	}

= Can I change the order of the HTML gallery elements? =
Not yet, though please get in touch if you'd find this useful.

= What does the actual output HTML look like? =
Like this:

`
    <div class="attachment_gallery">
    	<div class="attachment_gallery_item">
    		<div class="attachment_gallery_item_thumbnail">
    			<a href="http://example.com/my.pdf">
    				<img src="http://example.com/my.pdf/attachment-gallery/thumbnail.php?size=100&file=http://example.com/my.pdf" /> 
    			</a>
    		</div>
    		<a href="http://example.com/my.pdf">
    			PDF Title
    		</a>
    		</div>
    		<dl class="attachment_gallery_meta attachment_gallery_meta_title"> 
    			<dt>Title</dt>
    			<dd><div class="nl">Title extracted from PDF</div></dd> 
    		</dl>
    		<dl class="attachment_gallery_meta attachment_gallery_meta_subject"> 
    			<dt>Subject</dt> <dd><div class="nl">Subject extracted from PDF</div></dd>
    		</dl>
    		<div class="attachment_gallery_item_download">
    			<a href="http://example.com/my.pdf">Download Now</a> 
    		</div> 
    	</div>
    </div>
`
== Changelog ==

= 0.1 =
* Initial release

= 0.2 =
* Showing thumbnails and metadata

= 0.3 =
* Now extracting either WP-gallery based meta OR PDF-extracted meta

= 0.4 =
* Code refactoring
* Accepts download="yes" in shortcode to disable Download links
