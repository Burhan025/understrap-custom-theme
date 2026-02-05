<?php
/**
 * Override WooCommerce template path to use our custom templates
 * This general function handles all templates in inc/site/woocommerce/ directory
 */
add_filter( 'woocommerce_locate_template', function ( $template, $template_name, $template_path ) {

	$template_new = '';
	if ( $template_name ) {
		$ext_php = ( strpos( $template_name, ".php" ) === false ) ? ".php" : '';

		$fallback     = get_stylesheet_directory() . '/inc/site/woocommerce/' . "{$template_name}{$ext_php}";
		$template_new = file_exists( $fallback ) ? $fallback : '';
	}

	if ( ! $template_new ) {
		$template_args = [];
		if ( strpos( $template, "plugins/woocommerce/templates/" ) !== false ) {
			$template_args = explode( "plugins/woocommerce/templates/", $template );
		} elseif ( strpos( $template, "themes/" . get_template() . "/woocommerce/" ) !== false ) {
			$template_args = explode( "themes/" . get_template() . "/woocommerce/", $template );
		} elseif ( strpos( $template, "themes/" . get_stylesheet() . "/woocommerce/" ) !== false ) {
			$template_args = explode( "themes/" . get_stylesheet() . "/woocommerce/", $template );
		}
		if ( ! empty( $template_args[1] ) ) {
			$fallback     = get_stylesheet_directory() . '/inc/site/woocommerce/' . $template_args[1];
			$template_new = file_exists( $fallback ) ? $fallback : '';
		}
	}

	if ( $template_new ) {
		$template = $template_new;
	}

	return $template;
}, 99, 3 );

add_filter( 'wc_get_template_part', function ( $template, $slug, $name ) {
	$template_new = '';
	if ( ! $template ) {
		$ext_php = ( strpos( $slug, ".php" ) === false ) ? ".php" : '';

		if ( $name ) {
			$fallback     = get_stylesheet_directory() . '/inc/site/woocommerce/' . "{$slug}-{$name}{$ext_php}";
			$template_new = file_exists( $fallback ) ? $fallback : '';
		}

		if ( ! $template_new ) {
			$fallback     = get_stylesheet_directory() . '/inc/site/woocommerce/' . "{$slug}{$ext_php}";
			$template_new = file_exists( $fallback ) ? $fallback : '';
		}
	}

	if ( ! $template_new ) {
		$template_args = [];
		if ( strpos( $template, "plugins/woocommerce/templates/" ) !== false ) {
			$template_args = explode( "plugins/woocommerce/templates/", $template );
		} elseif ( strpos( $template, "themes/" . get_template() . "/woocommerce/" ) !== false ) {
			$template_args = explode( "themes/" . get_template() . "/woocommerce/", $template );
		} elseif ( strpos( $template, "themes/" . get_stylesheet() . "/woocommerce/" ) !== false ) {
			$template_args = explode( "themes/" . get_stylesheet() . "/woocommerce/", $template );
		}
		if ( ! empty( $template_args[1] ) ) {
			$fallback     = get_stylesheet_directory() . '/inc/site/woocommerce/' . $template_args[1];
			$template_new = file_exists( $fallback ) ? $fallback : '';
		}
	}

	if ( $template_new ) {
		$template = $template_new;
	}

	return $template;
}, 99, 3 );

add_filter( 'template_include', function ( $template ) {
	if ( function_exists( 'is_woocommerce' ) && ( is_shop() || is_product_category() || is_product_tag() ) ) {
		$new_template = get_stylesheet_directory() . '/inc/site/woocommerce/archive-product.php';
		if ( file_exists( $new_template ) ) {
			return $new_template;
		}
	}

	if (
		is_home() && ! is_front_page() || // page blog (Posts page)
		is_category() ||                   // category archive
		is_tag() ||                        // tag archive
		is_author() ||                     // author archive
		is_date() ||                       // day, month, year archive
		is_post_type_archive( 'post' ) ||  // future-proof
		is_tax( 'post_format' )            // post format archive
	) {
		$new_template = get_stylesheet_directory() . '/inc/site/template-parts/blog.php';
		if ( file_exists( $new_template ) ) {
			return $new_template;
		}
	}

	if ( is_single() && get_post_type() === 'post' ) {
		$new_template = get_stylesheet_directory() . '/inc/site/template-parts/blog-detail.php';
		if ( file_exists( $new_template ) ) {
			return $new_template;
		}
	}

	if ( is_page() ) {
		$selected = get_page_template_slug( get_queried_object_id() );
		if ( $selected === 'page-center-title.php' ) {
			$new_template = get_stylesheet_directory() . '/inc/site/page-center-title.php';
			if ( file_exists( $new_template ) ) {
				return $new_template;
			}
		}
	}

	return $template;
}, 99 );

add_filter( 'theme_page_templates', function ( $templates ) {
	$templates['page-center-title.php'] = 'Page with Centered Title';

	return $templates;
} );

/**
 * Force template override for agegate, filters, and meta-table
 */
add_filter( 'bs_locate_template', function ( $template, $templateName, $templatePath ) {
	// Debug: Log all template requests to see what's being called
//	error_log("BS Template Override - templateName: {$templateName}, templatePath: {$templatePath}");

	if ( $templateName === 'agegate' ) {
		$childThemeTemplate = get_stylesheet_directory() . '/inc/site/template-parts/agegate.php';
		if ( file_exists( $childThemeTemplate ) ) {
			return $childThemeTemplate;
		}
	} else if ( $templateName === 'filters/select' ) {
		$filterSelect = get_stylesheet_directory() . '/inc/site/template-parts/filters/select.php';
		if ( file_exists( $filterSelect ) ) {
			return $filterSelect;
		}
	} else if ( $templateName === 'product-detail/meta-table' ) {
		$metaTable = get_stylesheet_directory() . '/inc/site/template-parts/product-detail/meta-table.php';
		if ( file_exists( $metaTable ) ) {
			return $metaTable;
		}
	}

	return $template;
}, 10, 3 );
