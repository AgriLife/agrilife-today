<?php
/**
 * The file that defines the In The News post type custom fields
 *
 * @link       https://github.com/AgriLife/agrilife-today/blob/master/fields/in-the-news.php
 * @since      1.4.0
 * @package    agrilife-today
 * @subpackage agrilife-today/fields
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_5ee132b4af540',
			'title'                 => 'Details',
			'fields'                => array(
				array(
					'key'               => 'field_5ee132cefa5c8',
					'label'             => '',
					'name'              => 'in_the_news_details',
					'type'              => 'group',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'layout'            => 'block',
					'sub_fields'        => array(
						array(
							'key'               => 'field_5ee133e2fa5c9',
							'label'             => 'Link',
							'name'              => 'link',
							'type'              => 'url',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
						),
						array(
							'key'               => 'field_5ee24eb504a73',
							'label'             => 'Source',
							'name'              => 'source',
							'type'              => 'taxonomy',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'taxonomy'          => 'news-source',
							'field_type'        => 'select',
							'allow_null'        => 0,
							'add_term'          => 1,
							'save_terms'        => 1,
							'load_terms'        => 1,
							'return_format'     => 'id',
							'multiple'          => 0,
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'in-the-news',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		)
	);

endif;
