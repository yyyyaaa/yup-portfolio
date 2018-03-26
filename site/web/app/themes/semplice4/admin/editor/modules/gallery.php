<?php

// -----------------------------------------
// semplice
// admin/editor/modules/gallery/module.php
// -----------------------------------------

if(!class_exists('sm_gallery')) {
	class sm_gallery {

		public $output;

		// constructor
		public function __construct() {
			// define output
			$this->output = array(
				'html' => '',
				'css'  => '',
			);
		}

		// output editor
		public function output_editor($values, $id) {

			// get images
			$images = $values['content']['xl'];
			
			// get first image as preview
			if(is_array($images) && isset($images[0])) {
				$img = wp_get_attachment_image_src($images[0], 'full');
				$this->output['html'] = '<div class="gallery-preview"><img class="is-content" src="' . $img[0] . '" alt="gallery-placeholder"></div>';
			} else {
				// no images
				$this->output['html'] = '
					<div class="gallerygrid-error empty-gallery-icon">
						' . get_svg('backend', '/icons/module_gallery') . '
						<div class="content">
							<p>Your gallery has no images yet</p>
							<a class="semplice-button green-button">Add images</a>
						</div>
					</div>
				';
			}
			
			// return output
			return $this->output;
		}

		// output frtonend
		public function output_frontend($values, $id) {

			// output
			$output = '';
			$cover = array(
				'css' 		 => '',
				'class' 	 => '',
				'object-fit' => '',
			);
			$gallery_size = 'true';

			// attributes
			extract( shortcode_atts(
				array(
					'images'				=> '',
					'width'					=> 'grid-width',
					'cover_mode'			=> 'disabled',
					'autoplay'				=> false,
					'adaptive_height'		=> 'true',
					'animation_status'		=> 'enabled',
					'animation'				=> 'sgs-crossfade',
					'timeout' 				=> 4000,
					'arrows_visibility'		=> 'true',
					'pagination_visibility'	=> 'false',
					'arrows_color'			=> '#ffffff',
					'pagination_color'		=> '#000000',
					'pagination_position'	=> 'below',
					'infinite'				=> 'false',
				), $values['options'] )
			);
			
			// autoplay?
			if($autoplay == 'true' && is_numeric($timeout)) {
				$autoplay = $timeout;
			} else {
				$autoplay = 'false';
			}

			// animation status
			if($animation_status == 'disabled') {
				$animation = 'sgs-nofade';
			}

			$images = $values['content']['xl'];
			
			if(is_array($images)) {

				// cover class and css
				if($cover_mode == 'enabled') {
					// change min-height to vh if section is fullscreen. set min height to 100% if section height is custom and therefore defined with a fixed value for .container
					$min_height_unit = 'vh';
					$content_height = '100vh';
					if($values['section_height']['mode'] == 'custom') {
						$min_height_unit = '%';
						$content_height = $values['section_height']['height'];
					}

					// set up cover
					$cover = array(
						'css' 		 => $values['section_element'] . ' .row, ' . $values['section_element'] . ' .row .column { min-height: 100' . $min_height_unit . ' !important; } ' . $values['section_element'] . ' .column-content { height: ' . $content_height . '; }',
						'class' 	 => ' sgs-cover',
						'object-fit' => ' data-object-fit="cover"',
					);
					// set gallery sizing to false
					$gallery_size = 'false';
				}

				$output .= '<div id="gallery-' . $id . '" class="is-content semplice-gallery-slider ' . $animation . ' pagination-' . $pagination_position . ' sgs-pagination-' . $pagination_visibility . $cover['class'] . '">';

				foreach($images as $image) {
				
					$img = wp_get_attachment_image_src($image, 'full');
					
					$output .= '<div class="sgs-slide ' . $width . '">';
					$output .= '<img src="' . $img[0] . '" alt="gallery-image"' . $cover['object-fit'] . ' />';
					$output .= '</div>';
				}
				
				$output .= '</div>';

				// custom css for nav and pagination
				$this->output['css'] = '#gallery-' . $id . ' .flickity-prev-next-button .arrow { fill: ' . $arrows_color . ' !important; }#gallery-' . $id . ' .flickity-page-dots .dot { background: ' . $pagination_color . ' !important; }' . $cover['css'];
				
				$output .='
					<script>
						(function($) {
							$(document).ready(function () {
								$("#gallery-' . $id . '").flickity({
									autoPlay: ' . $autoplay . ',
									adaptiveHeight: ' . $adaptive_height . ',
									prevNextButtons: ' . $arrows_visibility . ',
									pageDots: ' . $pagination_visibility . ',
									wrapAround: ' . $infinite . ',
									setGallerySize: ' . $gallery_size . ',
									percentPosition: true,
									imagesLoaded: true,
									arrowShape: { 
										x0: 10,
										x1: 60, y1: 50,
										x2: 65, y2: 45,
										x3: 20
									},
									pauseAutoPlayOnHover: false,
								});
							});
						})(jQuery);
					</script>
				';
			} else {
				$output .= '<div class="empty-gallery">Your gallery has no images yet.</div>';
			}

			// save output
			$this->output['html'] = $output;

			return $this->output;
		}
	}
	// instance
	$this->module['gallery'] = new sm_gallery;
}