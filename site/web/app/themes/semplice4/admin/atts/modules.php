<?php

// -----------------------------------------
// semplice
// admin/atts/modules.php
// -----------------------------------------

// paragraph
require get_template_directory() . '/admin/atts/modules/paragraph.php';

// button
require get_template_directory() . '/admin/atts/modules/button.php';

// image
require get_template_directory() . '/admin/atts/modules/image.php';

// gallery
require get_template_directory() . '/admin/atts/modules/gallery.php';

// oembed
require get_template_directory() . '/admin/atts/modules/oembed.php';

// spacer
require get_template_directory() . '/admin/atts/modules/spacer.php';

// video
require get_template_directory() . '/admin/atts/modules/video.php';

// portfolio grid
require get_template_directory() . '/admin/atts/modules/portfoliogrid.php';

// dribbble
require get_template_directory() . '/admin/atts/modules/dribbble.php';

// code
require get_template_directory() . '/admin/atts/modules/code.php';

// modules array
$module_options = array(

	// paragraph
	'paragraph' => $paragraph,

	// button module
	'button' => $button,

	// image module
	'image' => $image,

	// gallery
	'gallery' => $gallery,

	// oembed module
	'oembed' => $oembed,

	// spacer module
	'spacer' => $spacer,

	// video module
	'video' => $video,

	// portfolio grid
	'portfoliogrid' => $portfoliogrid,

	// dribbble
	'dribbble' => $dribbble,

	// code module
	'code' => $code,

	// share module
	'share' => array(
		'options' => get_share_module_options($fonts, false),
	),
);

?>