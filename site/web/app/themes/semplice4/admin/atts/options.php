<?php

// -----------------------------------------
// semplice
// admin/atts/options.php
// -----------------------------------------

$options = array(

	// section options
	'section' => array(
		'mobile-options' => '',
		'options' => array(
			'title'  => 'Options',
			'break'  => '2,3,1,1',
			'data-hide-mobile' => true,
			'layout' => array(
				'title'		 => 'Layout',
				'size'		 => 'span2',
				'data-input-type' => 'switch',
				'switch-type'=> 'twoway',
				'class' 	 => 'editor-listen',
				'data-handler' => 'layout',
				'default' 	 => 'grid',
				'switch-values' => array(
					'grid'	=> 'Grid',
					'fluid' => 'Fluid',
				),
			),
			'gutter' => array(
				'title'		 => 'Gutter',
				'data-input-type' => 'switch',
				'switch-type'=> 'twoway',
				'size'		 => 'span2',
				'class' 	 => 'editor-listen',
				'data-handler' => 'layout',
				'default' 	 => 'yes',
				'switch-values' => array(
					'yes'  => 'Keep',
					'no'   => 'Remove',
				),
			),
			'height' => array(
				'title'				=> 'height',
				'size'		 		=> 'span2',
				'data-input-type' 		=> 'select-box',
				'class' 	 => 'editor-listen',
				'data-handler' => 'layout',
				'default'		 	=> 'dynamic',
				'help'				=> '<b>Fullscreen</b> means that your section has a minimum height of 100% from your browser viewport. In order to keep the 100% height try not to add content that will exceed the height.',
				'data-visibility-switch' 	=> true,
				'data-visibility-values' 	=> 'dynamic,fullscreen,custom',
				'data-visibility-prefix'	=> 'ov-section-height',
				'select-box-values' => array(
					'dynamic'    => 'Dynamic',
					'fullscreen' => 'Fullscreen',
					'custom'	 => 'Custom'
				),
			),

			// height
			'custom-height' => array(
				'title'				=> 'Custom',
				'size'		 		=> 'span1',
				'data-input-type' 		=> 'range-slider',
				'data-target'		=> '.container',
				'min'				=> 0,
				'max'				=> 9999,
				'default'			=> 0,
				'class' 	 		=> 'editor-listen',
				'data-handler' 		=> 'default',
				'responsive'		=> true,
				'data-has-unit'		=> true,
				'style-class'		=> 'ov-section-height-custom',
				'data-range-slider' => 'customHeight',
			),
			// height
			'height-unit' => array(
				'title'				=> 'Unit',
				'size'		 		=> 'span1',
				'data-input-type' 		=> 'select-box',
				'class' 	 		=> 'editor-listen',
				'data-handler' 		=> 'save',
				'default'		 	=> 'px',
				'style-class'		=> 'ov-section-height-custom',
				'help'				=> 'After changing the unit please click into input field where you defined your \'Custom\' height to apply the unit change.',
				'select-box-values' => array(
					'px' => 'px',
					'vh' => 'vh',
				),
			),
			'valign' => array(
				'title'		 => 'Vertical Align',
				'size'		 => 'span4',
				'data-input-type' => 'switch',
				'switch-type'=> 'fourway',
				'class' 	 		=> 'editor-listen',
				'data-handler' 		=> 'layout',
				'default'	 => 'stretch',
				'help'		 => 'To align content vertically please set the height of your section to \'Fullscreen\' or define a \'Custom\' height which exceeds the height of your content.',
				'tooltips'	 => array(
					'stretch' => 'Stretch',
					'top'     => 'Align top',
					'bottom'  => 'Align bottom',
					'center'  => 'Align middle',
				),
				'switch-values' => array(
					'stretch' => 'Stretch',
					'top'     => 'Top',
					'bottom'  => 'Bottom',
					'center'  => 'Center',
				),
			),
			'justify' => array(
				'title'		 => 'Justify',
				'help'		 => 'First three options only take effect if you have free space in your section. (that means your column sizes are combined < 12) For the last two options you need at least 2 columns and the size of your columns is < 12',
				'size'		 => 'span4',
				'data-input-type' => 'switch',
				'switch-type'=> 'fiveway',
				'class' 	 		=> 'editor-listen',
				'data-handler' 		=> 'layout',
				'default'	 => 'left',
				'tooltips'	 => array(
					'left'     		 => 'Align left',
					'right'  		 => 'Align right',
					'center' 		 => 'Align center',
					'space-between'  => 'Space Between',
					'space-around' 	 => 'Space Around'
				),
				'switch-values' => array(
					'left'     		 => 'Left',
					'right'  		 => 'Right',
					'center' 		 => 'center',
					'space-between'  => 'Space Between',
					'space-around' 	 => 'Space Around'
				),
			),
		),
	),

	// column options
	'column' => array(
		'options' => array(
			'title'  => 'Options',
			'hide-title' => true,
			'valign' => array(
				'title'		 => 'Vertical Align',
				'help'		 => 'This option only takes effect if you set the section height to either \'Fullscreen\' or enter a \'custom height\' thats taller than your content and set the section vertical align to stretch. (first option)',
				'size'		 => 'span4',
				'data-input-type' => 'switch',
				'switch-type'=> 'fourway',
				'class' 	 		=> 'editor-listen',
				'data-handler' 		=> 'layout',
				'default'	 => 'stretch',
				'tooltips'	 => array(
					'stretch' => 'Stretch',
					'top'     => 'Align top',
					'bottom'  => 'Align bottom',
					'center'  => 'Align middle',
				),
				'switch-values' => array(
					'stretch' => 'Stretch',
					'top'     => 'Top',
					'bottom'  => 'Bottom',
					'center'  => 'Center',
				),
			),
		),
	),

	// content options, empty for the module options
	'content' => array(),

	// section options
	'cover' => array(
		'options' => array(
			'title'  => 'Options',
			'break'  => '2,1,1',
			'data-hide-mobile' => true,
			'layout' => array(
				'title'		 => 'Layout',
				'size'		 => 'span2',
				'data-input-type' => 'switch',
				'switch-type'=> 'twoway',
				'class' 	 		=> 'editor-listen',
				'data-handler' 		=> 'layout',
				'default' 	 => 'grid',
				'switch-values' => array(
					'grid'	=> 'Grid',
					'fluid' => 'Fluid',
				),
			),
			'gutter' => array(
				'title'		 => 'Gutter',
				'data-input-type' => 'switch',
				'switch-type'=> 'twoway',
				'size'		 => 'span2',
				'class' 	 		=> 'editor-listen',
				'data-handler' 		=> 'layout',
				'default' 	 => 'yes',
				'switch-values' => array(
					'yes'  => 'Keep',
					'no'   => 'Remove',
				),
			),
			'valign' => array(
				'title'		 => 'Vertical Align',
				'size'		 => 'span4',
				'data-input-type' => 'switch',
				'switch-type'=> 'fourway',
				'class' 	 		=> 'editor-listen',
				'data-handler' 		=> 'layout',
				'default'	 => 'stretch',
				'switch-values' => array(
					'stretch' => 'Stretch',
					'top'     => 'Top',
					'bottom'  => 'Bottom',
					'center'  => 'Center',
				),
			),
			'justify' => array(
				'title'		 => 'Justify',
				'help'		 => 'First three options only take effect if you have free space in your section. (that means your column sizes are combined < 12) For the last two options you need at least 2 columns and the size of your columns is < 12',
				'size'		 => 'span4',
				'data-input-type' => 'switch',
				'switch-type'=> 'fiveway',
				'class' 	 		=> 'editor-listen',
				'data-handler' 		=> 'layout',
				'default'	 => 'left',
				'switch-values' => array(
					'left'     		 => 'Left',
					'right'  		 => 'Right',
					'center' 		 => 'center',
					'space-between'  => 'Space Between',
					'space-around' 	 => 'Space Around'
				),
			),
		),
	),

	// mobile settings
	'responsive-lg' => get_responsive_options('lg', 'Desktop' ),
	'responsive-md' => get_responsive_options('md', 'Tablet Wide'),
	'responsive-sm' => get_responsive_options('sm', 'Tablet Portrait'),
	'responsive-xs' => get_responsive_options('xs', 'Phone'),
);

?>