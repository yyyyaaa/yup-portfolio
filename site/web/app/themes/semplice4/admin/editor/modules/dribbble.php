<?php

// -----------------------------------------
// semplice
// admin/editor/modules/dribbble/module.php
// -----------------------------------------

if(!class_exists('sm_dribbble')) {

	class sm_dribbble {

		public $output;
		public $is_editor;

		// constructor
		public function __construct() {
			// define output
			$this->output = array(
				'html' => '',
				'css'  => '',
			);
			// set is editor
			$this->is_editor = true;
		}

		// output frontend
		public function output_editor($values, $id) {

			// extract options
			extract( shortcode_atts(
				array(
					'dribbble_id'	    => 'vanschneider',
					'count'				=> 15,
					'col'				=> 4,
					'target'			=> 'lightbox',
					'hor_gutter'		=> 30,
					'ver_gutter'		=> 30,
				), $values['options'] )
			);

			// get dribbble token
			$dribbble_token_dep = get_option('semplice_dribbble_token');
			$dribbble_token = get_option('semplice_dribbble_token_v2');

			// empty dribbble id?
			if(!$dribbble_id) {
				$dribbble_id = 'vanschneider';
			}

			// generate items
			$masonry_items = '';

			// get dribbble json
			if(!function_exists('_isCurl')) {
				function _isCurl(){
				    return function_exists('curl_version');
				}
			}

			// if curl is installed get media
			if(_isCurl()) {
				$media = json_decode($this->exec_curl($dribbble_id, $dribbble_token, $count), true);
			} else {
				$media = 'curl';
			}

			// index
			$index = 0;

			// get shots
			if($media == 'curl') {
				$this->output['html'] = '
					<div class="dribbble-error">
						<img src="' . get_svg('backend', '/icons/module_dribbble') . '">
						<div class="content">
							<p>cURL Extension not installed. Please advise your host to install / activate the cURL Extension for you.</p>
						</div>
					</div>
				';
			} else if(!$dribbble_token && $media != 'error') {
				// vars
				$connect_button = '';
				$connect_message = 'You are not connected yet.</p>';
				// chnage connect message if old api is active
				if($dribbble_token_dep) {
					$connect_message = 'Please update your token for the Dribbble Api v2';
				}
				// only show connect button in the backend
				if(true === $this->is_editor) {
					$connect_button = '<a class="semplice-button green-button" href="https://dribbble.com/oauth/authorize?client_id=1a12455a58abacba654732e07477faf1fe78d2990049fb9feabe2fd63550e93c&redirect_uri=http://redirect.semplice.com/?uri=' . admin_url('admin.php?page=semplice-admin-dribbble-auth') . '" target="_blank">Connect Dribbble</a>';
				}
				$this->output['html'] = '
					<div class="dribbble-error">
						' . get_svg('backend', '/icons/module_dribbble') . '
						<div class="content">
							<p>' . $connect_message . '</p>
							' . $connect_button . '
						</div>
					</div>
				';
			} else if(is_array($media) && !empty($media)) {

				foreach ($media as $shots => $shot) {

					// make image
					$image = array(
						'src' 	 => '',
						'width'  => $shot['width'],
						'height' => $shot['height'],
					);

					// image url
					if(!empty($shot['images']['hidpi'])) {
						$image['src'] = $shot['images']['hidpi'];
					} else {
						$image['src'] = $shot['images']['normal'];
					}

					// lightbox vs link to dribbble
					if($target == 'lightbox') {
						$href = $image['src'];
						$lightbox = true;
					} else {
						$href = $shot['html_url'];
						$lightbox = false;
					}


					// add thumb to holder
					$masonry_items .= '
						<div class="masonry-item thumb masonry-' . $id . '-item" data-xl-width="' . $col . '" data-sm-width="6" data-xs-width="12">
							' . $this->wrap_hyperlink($image, $href, $lightbox) . '
						</div>
					';
					
					// increment index
					$index ++;

				}

				// get masonry
				$this->output['html'] = semplice_masonry($id, $values['options'], $masonry_items, $hor_gutter, $ver_gutter, '', $this->is_editor);
				
			} else {
				// only show connect button in the backend
				$connect_button = '';
				if(true === $this->is_editor) {
					$connect_button = '<a class="semplice-button green-button" href="https://dribbble.com/oauth/authorize?client_id=1a12455a58abacba654732e07477faf1fe78d2990049fb9feabe2fd63550e93c&redirect_uri=http://redirect.semplice.com/?uri=' . admin_url('admin.php?page=semplice-admin-dribbble-auth') . '" target="_blank">Connect Dribbble</a>';
				}
				$this->output['html'] = '
					<div class="dribbble-error">
						' . get_svg('backend', '/icons/module_dribbble') . '
						<div class="content">
							<p>Wrong or no access token.</p>
							' . $connect_button . '
						</div>
					</div>
				';
			}

			// output
			return $this->output;			
		}

		// output frontend
		public function output_frontend($values, $id) {
			// same as editor
			$this->is_editor = false;
			return $this->output_editor($values, $id);
		}

		// wrap hyperlink
		public function wrap_hyperlink($image, $link, $lightbox) {
			if(true === $lightbox) {
				return '<a class="dribbble-image semplice-lightbox"><img class="lightbox-item" src="' . $image['src'] . '" width="800" height="600" alt="dribbble-image"></a>';
			} else {
				return '<a class="dribbble-image" href="' . $link . '" target="_blank"><img src="' . $image['src'] . '" width="800" height="600" alt="dribbble-image"></a>';
			}
		}

		// curl
		function exec_curl($dribbble_id, $token, $shots) {

			// curl init
			$ch = curl_init();

			// authorization
			$authorization = 'Authorization: Bearer ' . $token;

			// header
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));

			// url
			curl_setopt($ch, CURLOPT_URL, 'https://api.dribbble.com/v2/user/shots?per_page=' . $shots);
			
			// disable ssl
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
			// return content
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$response = curl_exec($ch);

			// get html code
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			curl_close($ch);

			//print_r($response);

			if($code === 200) {
				return $response;
			} else {
				return "error";
			}
		}
	}

	// instance
	$this->module['dribbble'] = new sm_dribbble;
}