<?php
// ------------------------------------------------
//  CUSTOM POST TYPES FOR IWTMEDICAL PRODUCTS 
// ------------------------------------------------


// IWTMedical Product CPT
function cptui_register_my_cpts_itw_medical_product() {

	/**
	 * Post Type: ITW Medical Products.
	 */

	$labels = [
		"name" => esc_html__( "ITW Medical Products", "twentytwentythree" ),
		"singular_name" => esc_html__( "ITW Medical Product", "twentytwentythree" ),
		"add_new" => esc_html__( "Add New", "twentytwentythree" ),
		"add_new_item" => esc_html__( "Add New Product", "twentytwentythree" ),
		"edit_item" => esc_html__( "Edit Product", "twentytwentythree" ),
		"new_item" => esc_html__( "New Product", "twentytwentythree" ),
		"view_item" => esc_html__( "View Product", "twentytwentythree" ),
	];

	$args = [
		"label" => esc_html__( "ITW Medical Products", "twentytwentythree" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "itw-medical-product", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "itw-medical-product", $args );
}
//add_action( 'init', 'cptui_register_my_cpts_itw_medical_product' );
cptui_register_my_cpts_itw_medical_product();


// IWTMedical Product Category
function cptui_register_my_taxes() {

	/**
	 * Taxonomy: Categories.
	 */

	$labels = [
		"name" => esc_html__( "Categories", "twentytwentythree" ),
		"singular_name" => esc_html__( "Category", "twentytwentythree" ),
	];

	
	$args = [
		"label" => esc_html__( "Categories", "twentytwentythree" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'itw-medical-product-category', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "itw-medical-product-category",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "itw-medical-product-category", [ "itw-medical-product" ], $args );
}
//add_action( 'init', 'cptui_register_my_taxes' );
cptui_register_my_taxes();