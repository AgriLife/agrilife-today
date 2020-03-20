<?php
/**
 * The file that defines the Tag taxonomy page custom fields
 *
 * @link       https://github.com/AgriLife/agrilife-today/blob/master/fields/tag.php
 * @since      1.3.0
 * @package    agrilife-today
 * @subpackage agrilife-today/fields
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_5e7512e7f2e09',
			'title'                 => 'Theme Settings',
			'fields'                => array(
				array(
					'key'               => 'field_5e7512f2032a7',
					'label'             => '',
					'name'              => 'agtoday_tag_group',
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
							'key'               => 'field_5e751317032a8',
							'label'             => 'Banner',
							'name'              => 'banner',
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
									'key'               => 'field_5e751acb4e1ec',
									'label'             => 'Image',
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
									'return_format'     => 'array',
									'preview_size'      => 'full',
									'library'           => 'all',
									'min_width'         => '',
									'min_height'        => '',
									'min_size'          => '',
									'max_width'         => '',
									'max_height'        => '',
									'max_size'          => '',
									'mime_types'        => '',
								),
								array(
									'key'               => 'field_5e751ae14e1ed',
									'label'             => 'Link',
									'name'              => 'link',
									'type'              => 'link',
									'instructions'      => '',
									'required'          => 0,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'return_format'     => 'array',
								),
								array(
									'key'               => 'field_5e751af24e1ee',
									'label'             => 'Alignment',
									'name'              => 'alignment',
									'type'              => 'select',
									'instructions'      => '',
									'required'          => 0,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'choices'           => array(
										'Left'   => 'Left',
										'Center' => 'Center',
										'Right'  => 'Right',
										'Wide'   => 'Wide',
										'Full'   => 'Full',
									),
									'default_value'     => array(
										0 => 'Center',
									),
									'allow_null'        => 0,
									'multiple'          => 0,
									'ui'                => 0,
									'return_format'     => 'value',
									'ajax'              => 0,
									'placeholder'       => '',
								),
								array(
									'key'               => 'field_5e751bba53bd1',
									'label'             => 'Background Color',
									'name'              => 'background_color',
									'type'              => 'color_picker',
									'instructions'      => '',
									'required'          => 0,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'default_value'     => 'transparent',
								),
							),
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'taxonomy',
						'operator' => '==',
						'value'    => 'post_tag',
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
