<?php
/**
  * @desc   Extracts metadata from standard PDF format
  * @author Leo Brown
  * @date   May 24th 2010
  * @notes  The format we're extracting from is as so (these will appear on one line in the PDF):
  * /Info 1274 [...]
  * 1274 0 obj <<
  *	/Subject(This document is about something or other.\n\nLine breaks and other things are supported.)
  *	/Author(The author is unknown at this time)
  *	/Creator(Adobe InDesign CS2 \(4.0.4\))
  *	/Keywords(Some keywords go here)
  *	/Producer(Adobe PDF Library 7.0)
  *	/ModDate(D:20100524143435+01'00')
  *	/Title(The title is to do with translating)
  * >>
  * endobj
  */
Class PDFMetaData{

	private $keys=array(
		'Title',
		'Author',
		'Subject',
		'Keywords'
//		'Creator',
//		'Producer',
//		'ModDate',
	);

	// regex for parsing PDF meta
	var $objects='/<<(.*)>>/';
	var $items='/\/(?P<key>[A-Z][A-Za-z]*)\((?P<value>[^)\/]*)\)/';

	/**
	  * @desc   Take a file path or URL, and send it for processing
	  * @return Results as array from extractMeta
	  * @param  File URL or local path
	  * @author Leo Brown
	  *
	  */
	function getMeta($file){
		if($data = @file_get_contents($file)){
			return $this->extractMeta($data);
		}
	}

	/**
	  * @desc   Take a PDF or PDF dictionary as a binary string and return the meta elements
	  * @param  $data Binary string of PDF or PDF dictionary
	  * @return Array PDF Metadata
	  * @author Leo Brown
	  *
	  */
	function extractMeta($data){

		$meta = array();

		// objects
		$objects=array();
		preg_match_all($this->objects,$data,$objects);
		$objects=end($objects);

		// iterate matches
		foreach($objects as $object){

			$items=array();
			preg_match_all($this->items,$data,$items);

			foreach($this->keys as $key){

				if($index = array_search($key, $items['key'])){
					$meta[$key]=$items['value'][$index];
				}

				// if we have all keys, we can stop parsing
				if(count($this->keys)==count($meta)) break 2;

			}			
		}

		return $meta;

	}
}
?>
