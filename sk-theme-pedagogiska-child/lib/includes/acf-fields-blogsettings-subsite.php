<?php
if( function_exists('register_field_group') ):

register_field_group(array (
	'key' => 'group_552d101245fcc',
	'title' => 'Blogginställningar',
	'fields' => array (
		array (
			'key' => 'field_552d101de6608',
			'label' => 'Bild',
			'name' => 'sk-blog-image',
			'prefix' => '',
			'type' => 'image',
			'instructions' => 'Optimal bildstorlek 285px x 285px.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'id',
			'preview_size' => 'thumbnail',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
		),
		array (
			'key' => 'field_5535fd2fa111a',
			'label' => 'Beskrivning',
			'name' => 'sk-blog-desc',
			'prefix' => '',
			'type' => 'wysiwyg',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'tabs' => 'visual',
			'toolbar' => 'basic',
			'media_upload' => 0,
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-blogginstallningar',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
));

endif;
?>