<?php

// -----------------------------------------
// semplice images for blocks
// -----------------------------------------

function semplice_get_external_image($id) {

	// get media json
	$media = file_get_contents(get_template_directory() . '/assets/images/admin/media.json');
	$media = json_decode($media, true);

	// get uri to assets
	$url = 'http://blocks.semplice.com/v4/images/';
	
	// return image
	if(strpos($id, 'svg') !== false) {
		return array(
			'url' => $url . $id,
			'type' => 'vector',
		);
	} else if(strpos($id, 'unsplash') !== false) {
		// set default image height
		$height = 1080;
		// get pos of ratio in url
		$pos = strpos($id, '#ratio');
		// check if ratio is set
		if (false !== $pos) {
			// get ratio
			$ratio = floatval(substr($id, $pos+strlen('#ratio:')));
			// set image height
			$height = round(1080 / $ratio, 0);
			// remove ratio from id
			$id = substr($id, 0, strpos($id, "#ratio"));
		}

		return array(
			'url' => $id,
			'type' => 'pixel',
			'width' => '1080',
			'height' => round(1080 / $ratio, 2),
		);
	} else {
		return array(
			'url' => $url . $id,
			'type' => 'pixel',
			'width' => $media['images'][$id]['width'],
			'height' => $media['images'][$id]['height']
		);
	}
}

// -----------------------------------------
// get image
// -----------------------------------------

function semplice_get_image($id, $size) {
	// get image url
	$image = wp_get_attachment_image_src($id, $size, false);
	// is still there?
	if($image) {
		return $image[0];
	} else {
		return 'notfound';
	}
}

// -----------------------------------------
// get background image
// -----------------------------------------

function semplice_get_background_image($styles) {
	// background type
	if(isset($styles['background_type']) && $styles['background_type'] == 'vid') {
		// check if bg image
		if(isset($styles['bg_video_fallback']) && is_numeric($styles['bg_video_fallback'])) {
			return $styles['bg_video_fallback'] . ',';
		}
	} else {
		// check if fall back image
		if(isset($styles['background-image']) && is_numeric($styles['background-image'])) {
			return $styles['background-image'] . ',';
		}
	}
}

// -----------------------------------------
// get images thats being used in a module
// -----------------------------------------

function semplice_get_used_images($content) {
	// image
	$image = false;
	// get image
	if($content['module'] == 'video') {
		if(isset($content['options']['poster'])) {
			$image = $content['options']['poster'];
		}		
	} else if(!empty($content['content']['xl'])) {
		$image = $content['content']['xl'];
	}

	// check if gallery or normal image
	if(is_numeric($image)) {
		return $image . ',';
	} else if(is_array($image)) {
		return implode(',', $content['content']['xl']) . ',';
	}
}

// ----------------------------------------
// get admin images
// ----------------------------------------

function semplice_get_admin_images($ids) {

	$images = array();

	// iterate through images
	if(is_array($ids) && !empty($ids)) {
		// check if broken or empty array
		if(array_key_exists (0, $ids) && null === $ids[0]) {
			$images = new stdClass();
		} else {
			// fetch all image urls in case they have chnaged (ex domain)
			foreach ($ids as $id => $url) {
				// get image
				$images[$id] = semplice_get_image($id, 'full');
			}
		}
		
	}
	return $images;
}