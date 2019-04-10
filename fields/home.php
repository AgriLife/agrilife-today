<?php
/**
 * The file that defines the Home Page custom fields
 *
 * @link       https://github.com/AgriLife/agrilife-today/blob/master/fields/home.php
 * @since      0.1.7
 * @package    agrilife-today
 * @subpackage agrilife-today/fields
 */

if ( function_exists( 'acf_add_local_field_group' ) ) :

	acf_add_local_field_group(
		array(
			'key'                   => 'group_5cae401b57ab6',
			'title'                 => 'Home',
			'fields'                => array(
				array(
					'key'               => 'field_5cae4024c97a2',
					'label'             => 'Top Story',
					'name'              => 'top_group',
					'type'              => 'group',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'layout'            => 'row',
					'sub_fields'        => array(
						array(
							'key'               => 'field_5cae4201c97a3',
							'label'             => 'Story',
							'name'              => 'story',
							'type'              => 'post_object',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'post_type'         => array(
								0 => 'post',
							),
							'taxonomy'          => '',
							'allow_null'        => 0,
							'multiple'          => 0,
							'return_format'     => 'object',
							'ui'                => 1,
						),
					),
				),
				array(
					'key'               => 'field_5cae426fc97a4',
					'label'             => 'Stories',
					'name'              => 'stories_group',
					'type'              => 'flexible_content',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'layouts'           => array(
						'layout_5cae4470ad4f9' => array(
							'key'        => 'layout_5cae4470ad4f9',
							'name'       => 'post',
							'label'      => 'Latest Post',
							'display'    => 'block',
							'sub_fields' => array(
								array(
									'key'               => 'field_5cae4675c97a6',
									'label'             => 'Category',
									'name'              => 'category',
									'type'              => 'taxonomy',
									'instructions'      => '',
									'required'          => 0,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'taxonomy'          => 'category',
									'field_type'        => 'checkbox',
									'add_term'          => 0,
									'save_terms'        => 0,
									'load_terms'        => 0,
									'return_format'     => 'object',
									'multiple'          => 0,
									'allow_null'        => 0,
								),
							),
							'min'        => '',
							'max'        => '',
						),
						'layout_5cae475dc97a7' => array(
							'key'        => 'layout_5cae475dc97a7',
							'name'       => 'quote',
							'label'      => 'Quote',
							'display'    => 'block',
							'sub_fields' => array(
								array(
									'key'               => 'field_5cae485cc97a8',
									'label'             => 'Quote',
									'name'              => 'quote',
									'type'              => 'wysiwyg',
									'instructions'      => '',
									'required'          => 0,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'default_value'     => '',
									'tabs'              => 'all',
									'toolbar'           => 'basic',
									'media_upload'      => 0,
									'delay'             => 0,
								),
								array(
									'key'               => 'field_5cae498cc97a9',
									'label'             => 'Category',
									'name'              => 'category',
									'type'              => 'taxonomy',
									'instructions'      => '',
									'required'          => 0,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'taxonomy'          => 'category',
									'field_type'        => 'select',
									'allow_null'        => 1,
									'add_term'          => 0,
									'save_terms'        => 0,
									'load_terms'        => 0,
									'return_format'     => 'object',
									'multiple'          => 0,
								),
							),
							'min'        => '',
							'max'        => '',
						),
						'layout_5cae4c02c97aa' => array(
							'key'        => 'layout_5cae4c02c97aa',
							'name'       => 'podcast',
							'label'      => 'Podcast',
							'display'    => 'block',
							'sub_fields' => array(
								array(
									'key'               => 'field_5cae4c10c97ab',
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
									'preview_size'      => 'thumbnail',
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
									'key'               => 'field_5cae4df1c97ac',
									'label'             => 'Page',
									'name'              => 'page',
									'type'              => 'page_link',
									'instructions'      => '',
									'required'          => 0,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'post_type'         => array(
										0 => 'page',
									),
									'taxonomy'          => '',
									'allow_null'        => 0,
									'allow_archives'    => 0,
									'multiple'          => 0,
								),
							),
							'min'        => '0',
							'max'        => '1',
						),
					),
					'button_label'      => 'Add Item',
					'min'               => '',
					'max'               => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/home.php',
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
