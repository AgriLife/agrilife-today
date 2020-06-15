<?php
/**
 * The file that defines the News Source taxonomy custom fields
 *
 * @link       https://github.com/AgriLife/agrilife-today/blob/master/fields/news-source.php
 * @since      1.4.0
 * @package    agrilife-today
 * @subpackage agrilife-today/fields
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_5ee27c5a94c41',
			'title'                 => 'Logo',
			'fields'                => array(
				array(
					'key'               => 'field_5ee27c64c4216',
					'label'             => '',
					'name'              => 'image',
					'type'              => 'image',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'return_format'     => 'id',
					'preview_size'      => 'medium',
					'library'           => 'all',
					'min_width'         => '',
					'min_height'        => '',
					'min_size'          => '',
					'max_width'         => '',
					'max_height'        => '',
					'max_size'          => '',
					'mime_types'        => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'taxonomy',
						'operator' => '==',
						'value'    => 'news-source',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		)
	);

endif;
