<?php

// -----------------------------------------
// semplice
// admin/editor/modules/portfoliogrid/module.php
// -----------------------------------------

if(!class_exists('sm_portfoliogrid')) {

	class sm_portfoliogrid {

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
					'hor_gutter'				=> 30,
					'ver_gutter'				=> 30,
					'categories'				=> '',
					'title_visibility'			=> 'both',
					'title_position'			=> 'below',
					'title_padding'				=> '1rem',
					'title_color'				=> '#000000',
					'title_fontsize'			=> '16px',
					'title_font'				=> 'regular',
					'title_text_transform'		=> 'none',
					'category_color'			=> '#999999',
					'category_fontsize'			=> '14px',
					'category_font'				=> 'regular',
					'category_text_transform'	=> 'none',
					'category_padding_top'		=> '0.4444444444444444rem',
				), $values['options'] )
			);

			// get portfolio order
			$portfolio_order = json_decode(get_option('semplice_portfolio_order'));

			// get projects
			$projects = semplice_get_projects($portfolio_order, $categories);

			// get thumb hover options
			$global_hover_options = json_decode(get_option('semplice_customize_thumbhover'), true);

			// change title position to below if visibility is hidden
			if($title_visibility == 'hidden') {
				$title_position = 'below';
			}

			// title padding
			if(strpos($title_position, 'below') === false) {
				$title_css = '';
				// paddings
				$paddings = array('top', 'left', 'bottom', 'right');
				foreach ($paddings as $padding) {
					if(strpos($title_position, $padding) !== false) {
						$title_css .= 'padding-' . $padding . ': ' . $title_padding . ';';
					}
				}
			} else {
				$title_css = 'padding-top: ' . $title_padding . ';';
			}
			
			// are there any published projects
			if(!empty($projects)) {

				// gutter numeric?
				if(!is_numeric($hor_gutter)) { $hor_gutter = 30; }
				if(!is_numeric($ver_gutter)) { $ver_gutter = 39; }

				// generate items
				$masonry_items = '';

				// add to css
				$add_to_css = semplice_thumb_hover_css(false, $global_hover_options, true, '#content-holder', false);

				foreach ($projects as $key => $project) {		

					if(empty($project['image']['width'])) {
						$project['image']['width'] = 6;
					}

					// thumb hover css if custom thumb hover is set
					if(isset($project['thumb_hover'])) {
						$add_to_css .= semplice_thumb_hover_css('project-' . $project['post_id'], $project['thumb_hover'], false, '#content-holder', false);
					}

					// title and category
					$title = '';
					if($title_visibility == 'both') {
						$title = '
							<p class="post-title">' . $project['post_title'] . '<span class="' . $category_font . '">' . $project['project_type'] . '</span></p>
						'; 
					} else if($title_visibility == 'title') {
						$title = '
							<p class="post-title">' . $project['post_title'] . '</p>
						'; 
					} else if($title_visibility == 'category') {
						$title = '
							<p class="post-title"><span class="' . $category_font . '">' . $project['project_type'] . '</span></p>
						'; 
					}

					// link title if below
					if(false !== strpos($title_position, 'below')) {
						$title = '<a class="' . $title_font . '" href="' . $project['permalink'] . '" title="' . $project['post_title'] . '">' . $title . '</a>';
					}

					// show post settings link on admin
					if(false === $this->is_editor) {
						$thumb_inner = '<a href="' . $project['permalink'] . '">' . $this->get_thumb_inner($id, $global_hover_options, $project, true, $title, $title_position);
					} else {
						$thumb_inner = $this->get_thumb_inner($id, $global_hover_options, $project, false, $title, $title_position);
					}

					// masonry items open
					$masonry_items .= '<div id="project-' . $project['post_id'] . '" class="masonry-item thumb masonry-' . $id . '-item ' . $title_position . '" data-xl-width="' . $project['image']['grid_width'] . '" data-sm-width="6" data-xs-width="12">';

					// add thumb inner
					$masonry_items .= $thumb_inner;

					// masonry items close
					$masonry_items .= '</div>';
				}

				// add to css
				$add_to_css .= '#content-holder #' . $id . ' .thumb .post-title { ' . $title_css . ' } #' . $id . ' .thumb .post-title, #' . $id . ' .thumb .post-title a { color: ' . $title_color . '; font-size: ' . $title_fontsize . '; text-transform: ' . $title_text_transform . '; } #' . $id . ' .thumb .post-title span, #' . $id . ' .thumb .post-title a span { color: ' . $category_color . '; font-size: ' . $category_fontsize . '; text-transform: ' . $category_text_transform . '; padding-top: ' . $category_padding_top . '; }';

				// get masonry
				$this->output['html'] = semplice_masonry($id, $values['options'], $masonry_items, $hor_gutter, $ver_gutter, $add_to_css, $this->is_editor);
			} else {
				$this->output['html'] = '<div class="empty-portfolio"><img src="' . get_template_directory_uri() . '/assets/images/admin/noposts.svg" alt="no-posts"><h3>Looks like you have an empty Portfolio. Please note that only<br />published projects are visible in the portfolio grid.</h3></div>';
			}

			// output
			return $this->output;
		}

		public function get_thumb_inner($id, $global_hover_options, $project, $is_frontend, $title, $title_position) {

			// vars
			$post_settings = '';

			if(false === $is_frontend) {
				$post_settings = '<a class="admin-click-handler grid-post-settings" data-content-id="' . $id . '" data-handler="execute" data-action-type="postSettings" data-action="getPostSettings" data-post-id="' . $project['post_id'] . '" data-ps-mode="grid" data-post-type="project" data-thumbnail-src="' . $project['image']['src'] . '">' . get_svg('backend', '/icons/post_settings') . '</a>';
			}

			// define output
			$output = '
				<div class="thumb-inner">
					' . semplice_thumb_hover_html($global_hover_options, $project, $is_frontend) . '
					' . $post_settings . '
					<img src="' . $project['image']['src'] . '" width="' . $project['image']['width'] . '" height="' . $project['image']['height'] . '">
			';

			// if title is below close the thumb inner link before the title. if title is above, include the title within thumb inner a tag. Note: the title only contains a tags if below
			if(false !== strpos($title_position, 'below')) {
				$output .= '</div></a>' . $title;
			} else {
				$output .= $title . '</div></a>';
			}

			// output
			return $output;
		}

		// output frontend
		public function output_frontend($values, $id) {

			// same as editor
			$this->is_editor = false;
			return $this->output_editor($values, $id);
		}
	}

	// instance
	$this->module['portfoliogrid'] = new sm_portfoliogrid;
}