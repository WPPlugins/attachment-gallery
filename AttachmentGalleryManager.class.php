<?php

/**
  * @desc   Attachment Gallery Display Manager
  * @author Leo Brown <leo@acumendevelopment.net>
  * @date   May 24th 2010
  *
  */
Class AttachmentGalleryManager{

	var $path;
	var $options=array();
	var $marker='\[attachmentgallery.*\]';
	var $downloadText='Download Now';

	/**
	  * @desc   
	  * @author Leo Brown
	  *
	  */
	function AttachmentGalleryManager(){
		$this->path = dirname(__FILE__).'/';
	}

	/**
	  * @desc   
	  * @author Leo Brown
	  *
	  */
	function detect_gallery_options($content){

        global $shortcode_tags;
        $tagnames = array_keys($shortcode_tags);

		// get shortcode
		$shortcode=array();
		if(preg_match("/{$this->marker}/", $content, $shortcode)){

			// extract and use shortcode_parse_atts() to get the array
			$shortcode=reset($shortcode);
			$options=array();
			preg_match('/\[[^ ]* (.*)\]/', $shortcode, $options);
			$options=shortcode_parse_atts(end($options));

			return $options;
		}
		else return false;
	}

	/**
	  * @desc   Returns gallery HTML on the basis of options
	  * @author Leo Brown
	  * @param  $options Array Options array
	  * @return string HTML of Gallery
	  */
	function get_attachment_gallery(&$contents,$options){

		$attachments = $this->find_attachment_links($contents, $options['filetypes']);

		// get metadata reader
		@include_once 'PDFMetaData.class.php';
		if(class_exists('PDFMetaData')) $metaReader=new PDFMetaData();

		// generate HTML for gallery
		$html='';
		if($attachments){
			$html .= '<div class="attachment_gallery">';
			foreach($attachments as $file){

				$meta = $this->cache_get('attachment_gallery_'.md5($file['url']));
				if(!is_array($meta)){
					if(@$metaReader){
						$meta = $metaReader->getMeta($file['url']);
						$this->cache_set('attachment_gallery_'.md5($file['url']),$meta);
					}
				}

				// output the base HTML for this attachment
				$html .= "<div class=\"attachment_gallery_item\">";
				if(class_exists('imagick')){
					$html .= '<div class="attachment_gallery_item_thumbnail">
						<a href="'.$file['url'].'">
							<img src="'.WP_PLUGIN_URL.'/attachment-gallery/thumbnail.php?size=100x150&file='.$file['url'].'" />
						</a>
					</div>';
				}
				$html .="<div class=\"attachment_gallery_item_title\">
						<a href=\"{$file['url']}\">{$file['title']}</a>
					</div>";

				// overwrite PDF-style Meta (Author, Subject, Keywords etc)
				// with any passed in the attachment array (from WP Media library)
				// specifically Subject (description)
				if($capt = @$file['caption'])     $meta['Author'] =$capt;
				if($desc = @$file['description']) $meta['Subject']=$desc;

				foreach(@$meta as $key=>$value){
					// split multiple lines onto DIVs so the user can style them if required
					$valueLines = explode("\n",str_replace(array('\r\n','\n'),"\n",$value));
					$value = '<div class="nl">'.implode('</div><div class="nl">',$valueLines).'&nbsp;</div>';

					$html .="<dl class=\"attachment_gallery_meta attachment_gallery_meta_".strtolower($key)."\">
						<dt>{$key}</dt> <dd>{$value}</dd>
						</dl>";
				}

				if($options['download'] && ('no'!==$options['download']))
				$html .= "<div class=\"attachment_gallery_item_download\"
						<a href=\"{$file['url']}\">{$this->downloadText}</a>
					</div";

				$html .= "</div>";
			}
			$html .= '</div>';

		}
		return $html;
	}
	
	/**
	  * @desc   Places items the cache
	  * @author Leo Brown
	  *
	  */
	function cache_set($key, $data){
		return @file_put_contents(
			sys_get_temp_dir().'/attachment_cache_'.
			md5($key),serialize($data)
		);
	}

	/**
	  * @desc   Accesses the cache
	  * @author Leo Brown
	  *
	  */
	function cache_get($key){

		return @unserialize(@file_get_contents(
			sys_get_temp_dir().
			'/attachment_cache_'.md5($key)
		));
	}

	/**
	  * @desc   Splices gallery content back into page content
	  * @author Leo Brown
	  * @todo   Move this function to the shortcode library, probably
	  *
	  */
	function insert_gallery($page, $gallery){
		return preg_replace("/{$this->marker}/",$gallery,$page);
	}
	
	/**
	  * @desc   
	  * @author Leo Brown
	  *
	  */
	function find_attachment_links(&$input, $types){


// not yet used
//		if(!$types) $types=array('.pdf');
//		$types=implode($types,'|');

		// our list of files
		$results=array();

		// pattern for any link - hand written / may need to be modified
		$regexp = "<a\s[^>]*href=(['\"]??)([^\" >]*)\\1[^>]*>(.*)<\/a>";

		// process our matches
		if(preg_match_all("/$regexp/siU", $input, $matches, PREG_SET_ORDER)) {

			foreach($matches as $match){

				// storage for this result
				$result=array();

				// known values
				$result['title']=$match[3];
				$result['url']  =$match[2];

				// test if is rel=attachment (regex from _fix_attachment_links in post.php)
				$search = "#[\s]+rel=(\"|')(.*?)wp-att-(?P<id>\d+)\\1#i";
				if(preg_match($search, $match[0], $attachment)){

					// get the attachment that this link refers to
					if($data = get_post($attachment['id'], 'ARRAY_A')){
						$result['title']       = $data['post_title'];
						$result['url']         = $data['guid'];
						$result['description'] = $data['post_content'];
						$result['caption']     = $data['post_excerpt'];

						// get author etc too?
					}
				}

				// only store if we actually have a URL from the anchor tag
				if($result['url']){
					// remove the link
					$input=str_replace($match[0],'',$input);

					// store result
					$results[]=$result;
				}
			}
		}

		return $results;
	}
}

/**
  * @desc   Hooks the_content and absorbs attachment links, replaces them with a gallery
  * @author Leo Brown
  *
  */
function attachmentGalleryHook($content){
	$manager=new AttachmentGalleryManager();
	$options = $manager->detect_gallery_options($content);

	$gallery = $manager->get_attachment_gallery($content,$options);
	if($gallery){
		return $manager->insert_gallery($content, $gallery);
	}
	else return $content;
}
?>
