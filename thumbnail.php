<?php
/**
  *
  * @desc   
  * @author Leo Brown
  * @date   May 24th 2010
  *
  */
Class Thumbnailer{

	var $download_prefix = '';
	var $admin_email     = '';

	function Thumbnailer(){

	}

	/**
	  *
	  * @desc   Get thumbnail of a given file
	  * @param  File path or URL (to be passed to file_get_contents())
	  * @return Binary PNG data or FALSE in case of error
	  * @author Leo Brown
	  * @date   May 24th 2010
	  *
	  */
	function getThumbnail($file,$h=175,$y=150){

		if($thumbnail = $this->cache($file,$h,$y)){
			return $thumbnail;
		}

		// get/set the original attachment file to/from cache
		if(!$attachment=$this->cache($file)){

			// store locally
			$attachment=file_get_contents($file);
			$this->cache($file, 0, 0, $attachment);
		}

		// local file name (required by imagemagick)
		$localfile=$this->cachedFileName($file);

		// page selection - default to page 0, or passed param 'page'
		if(stristr($file,'.pdf')){
			if(!@$_REQUEST['page']) $_REQUEST['page']=0;
			$page="[{$_REQUEST['page']}]";
		}
		else{
			$page='';
		}

		try{
			$thumbnail = new imagick("{$localfile}{$page}");
			if($thumbnail){
				$thumbnail->thumbnailImage($h,$y,true);
				$thumbnail->setImageFormat("png");
				$this->cache($file,$h,$y,$thumbnail);
				return $thumbnail;
			}
		}
		catch(Exception $e){

			// print/return the placeholder
		}

	}

	/**
	  *
	  * @desc   Output headers for displaying file
	  * @author Leo Brown
	  * @date   June 1st 2010
	  *
	  */
	function displayThumbnail($data){
		header('Content-type: image/png');
		print $data;
		die();
	}

	/**
	  *
	  * @desc   Get temp filename for file url and dimensions
	  * @author Leo Brown
	  * @date   May 24th 2010
	  *
	  */
	function cachedFileName($file,$h=0,$y=0){
		return sys_get_temp_dir().'/'.
			md5($file).
			"_{$h}_{$y}";
	}
	/**
	  *
	  * @desc   
	  * @author Leo Brown
	  * @date   May 24th 2010
	  *
	  */
	function cache($file,$h=0,$y=0,$data=null){

		if($data){
			file_put_contents($this->cachedFileName($file,$h,$y), $data);
		}
		elseif($data=@file_get_contents($this->cachedFileName($file,$h,$y))){
			return $data;
		}
		return false;

	}
}

if($file=@$_REQUEST['file']){

	// default and requested sizes
	$h=175;
	$w=150;

	if($size=@$_REQUEST['size']){
		$dims=explode('x',$size);
		if($width=reset($dims)) $w=$width;
		if($height=end($dims))  $h=$height;
	}

	$t=new Thumbnailer();
	$thumb = $t->getThumbnail($file,$w,$h);
	$t->displayThumbnail($thumb);
}
?>
