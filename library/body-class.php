<?php
/**
 * Body class adding page-parent
 *
 */


//Page Slug Body Class

function add_slug_body_class( $classes ) {
    global $post;
    if ( isset( $post ) ) {
        $classes[] = $post->post_type . '-' . $post->post_name;
    }
    return $classes;
}

add_filter( 'body_class', 'add_slug_body_class' );


/* Add body-class for top-level parent Page or Category */
function topcatpg_body_class( $class ) {
	$prefix = 'parent-page-'; // Editable class name prefix.
	$top_cat_pg = 'home'; // Default.
	global $top_cat_pg;
	// Get class name from top-level Category or Page.
	global $wp_query;
	if ( is_single() ) {
		$wp_query->post = $wp_query->posts[0];
		setup_postdata( $wp_query->post );
		 /* Climb Posts category hierarchy, successively replacing
		 class name top_cat_pg with slug of higher level cat. */
		foreach( (array) get_the_category() as $cat ) {
			if ( !empty( $cat->slug ) )
				$top_cat_pg = sanitize_html_class( $cat->slug, $cat->cat_ID );
			while ( $cat->parent ) {
				$cat = &get_category( (int) $cat->parent);
				if ( !empty( $cat->slug ) )
					$top_cat_pg = sanitize_html_class( $cat->slug, $cat->cat_ID );
			}
		}
	} elseif ( is_archive() ) {
		if ( is_category() ) {
			$cat        = $wp_query->get_queried_object();
			$top_cat_pg = $cat->slug;
			/* Climb Category hierarchy, successively replacing
		 	class name with slug of higher level cat. */
			while ( $cat->parent ) {
				$cat = &get_category( (int) $cat->parent );
				if ( !empty( $cat->slug ) )
					$top_cat_pg = sanitize_html_class( $cat->slug, $cat->cat_ID );
			}
		}
	} elseif ( is_page() ) {
		global $post;
		if ( $post->post_parent )	{
			$ancestors  = get_post_ancestors( $post->ID );
			$root       = count( $ancestors ) - 1;
			$top_id     = $ancestors[$root];
			$top_pg     = get_page( $top_id );
			$top_cat_pg = $top_pg->post_name;
		} else {
			$top_cat_pg = $post->post_name;
		}
	}
	$class[] = $prefix . $top_cat_pg;
	return $class;
}
add_filter( 'body_class', 'topcatpg_body_class' );