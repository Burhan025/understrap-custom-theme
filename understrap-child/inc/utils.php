<?php
/**
 * Utils
 *
 * @package BurhanAftab\UnderstrapChild
 */

if ( ! function_exists( 'bs_get_logo_url' ) ) {
	/**
	 * Get logo URL
	 *
	 * @return string
	 */
	function bs_get_logo_url() {
		$custom_logo_id = get_theme_mod( 'custom_logo' );

		if ( $custom_logo_id ) {
			// Use customizer logo.
			$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
		} else {
			// Fallback: check for default-logo.png in child theme first, then parent.
			$child_logo_path  = get_stylesheet_directory() . '/assets/images/logo.png';
			$parent_logo_path = get_template_directory() . '/assets/images/logo.png';

			if ( file_exists( $child_logo_path ) ) {
				$logo_url = get_stylesheet_directory_uri() . '/assets/images/logo.png';
			} elseif ( file_exists( $parent_logo_path ) ) {
				$logo_url = get_template_directory_uri() . '/assets/images/logo.png';
			} else {
				// Final fallback: no logo found.
				$logo_url = '';
			}
		}

		return $logo_url;
	}
}

/**
 * Custom pagination
 *
 * @param array $args Pagination arguments.
 * @return void
 */
function bs_pagination( $args = array() ) {
	global $wp_query, $wp_rewrite;

	// Setting up default values based on the current URL.
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$url_parts    = explode( '?', $pagenum_link );

	// Get max pages and current page out of the current query, if available.
	$total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
	$current = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;

	// Append the format placeholder to the base URL.
	$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

	// URL base depends on permalink settings.
	$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

	$defaults = array(
		'base'               => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below).
		'format'             => $format, // ?page=%#% : %#% is replaced by the page number.
		'total'              => $total,
		'current'            => $current,
		'aria_current'       => 'page',
		'show_all'           => false,
		'prev_next'          => true,
		'end_size'           => 1,
		'mid_size'           => 2,
		'type'               => 'plain',
		'add_args'           => array(), // Array of query args to add.
		'add_fragment'       => '',
		'before_page_number' => '',
		'after_page_number'  => '',
		'class'              => '',
		'label'              => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$prev_link = str_replace( '%_%', 2 === $args['current'] ? '' : $args['format'], $args['base'] );
	$prev_link = str_replace( '%#%', $args['current'] - 1, $prev_link );
	$next_link = str_replace( '%_%', $args['format'], $args['base'] );
	$next_link = str_replace( '%#%', $args['current'] + 1, $next_link );

	$prev_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
	<path d="M20 11H7.83L13.42 5.41L12 4L4 12L12 20L13.41 18.59L7.83 13H20V11Z" fill="#0D0D0B"/>
	</svg>';

	$next_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
	<path d="M12 4L10.59 5.41L16.17 11H4V13H16.17L10.59 18.59L12 20L20 12L12 4Z" fill="#0D0D0B"/>
	</svg>';
	?>
	<nav class="bs-pagination <?php echo esc_attr( $args['class'] ); ?>" aria-label="<?php echo esc_attr( $args['label'] ); ?>">

		<?php if ( $args['prev_next'] ) : ?>
		<div class="page-numbers page-numbers__prev">
			<?php if ( 1 === $args['current'] ) : ?>
				<span class="prev page-numbers">
					<?php echo $prev_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			<?php else : ?>
				<a class="prev page-numbers" href="<?php echo esc_url( apply_filters( 'paginate_links', $prev_link ) ); ?>">
					<?php echo $prev_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<div class="page-numbers__wrapper">
			<?php echo paginate_links( array_replace( $args, array( 'prev_next' => false ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

		<?php if ( $args['prev_next'] ) : ?>
		<div class="page-numbers page-numbers__next">
			<?php if ( $args['current'] === $args['total'] ) : ?>
				<span class="next page-numbers">
					<?php echo $next_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			<?php else : ?>
				<a class="next page-numbers" href="<?php echo esc_url( apply_filters( 'paginate_links', $next_link ) ); ?>">
					<?php echo $next_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</nav>

	<?php
}

/**
 * Validate phone number
 *
 * @param string $phone Phone number.
 * @return bool
 */
function bs_validate_phone( $phone ) {
	if ( is_string( $phone ) ) {

		$number_count = 0;
		$allow_chars  = array( '(', ')', ' ', '-', '/' );

		// Allow +1 before phone number.
		$position = strpos( $phone, '+1' );
		if ( $position === 0 ) {
			$phone = str_replace( '+1', '', $phone );
		}
		$chars = str_split( $phone );

		foreach ( $chars as $key => $item ) {
			if ( is_numeric( $item ) ) {
				++$number_count;
				continue;
			}

			if ( ! in_array( $item, $allow_chars ) ) {
				return false;
			}
		}

		if ( $number_count != 10 ) {
			return false;
		}

		return true;
	}

	return false;
}

/**
 * Print continue shopping link
 *
 * @return void
 */
function bp_print_continue_shopping_link() {
	?>
	<a class="continue-shopping" href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">
		<i class="fa fa-arrow-left"></i>
		<?php esc_html_e( 'CONTINUE SHOPPING', 'woocommerce' ); ?>
	</a>
	<?php
}

/**
 * Get products
 *
 * @param array $args Arguments.
 * @return array
 */
function bs_get_products( $args = array() ) {

	global $wpdb;
	global $bsCurrentLocation;

	$args = wp_parse_args(
		$args,
		array(
			'categories' => array(),
			'tags'       => array(),
			'is_new'     => false,
			'in_stock'   => true,
			'on_sale'    => false,
			'orderby'    => 'rand',
			'order'      => 'ASC',
			'limit'      => 8,
			'location'   => $bsCurrentLocation ? $bsCurrentLocation->slug() : '',
		)
	);

	$tbl_posts  = $wpdb->prefix . 'posts';
	$tbl_tr     = $wpdb->prefix . 'term_relationships';
	$tbl_wc_pml = $wpdb->prefix . 'wc_product_meta_lookup';

	$select  = 'SELECT ' . $tbl_posts . '.ID FROM ' . $tbl_posts . ' ';
	$join    = 'INNER JOIN ' . $tbl_wc_pml . ' AS pml ON (' . $tbl_posts . '.ID = pml.parent_id)';
	$where   = $tbl_posts . '.post_type = "product" AND ' . $tbl_posts . '.post_status = "publish"';
	$limit   = 'LIMIT 0, ' . esc_sql( $args['limit'] );
	$groupby = 'GROUP BY ' . $tbl_posts . '.ID';

	if ( 'rand' === $args['orderby'] ) {
		$orderby = 'ORDER BY RAND()';
	} else {
		$pair = array(
			'date'       => $tbl_posts . '.post_date',
			'title'      => $tbl_posts . '.post_title',
			'menu_order' => $tbl_posts . '.menu_order',
		);
		if ( ! isset( $pair[ $args['orderby'] ] ) ) {
			$args['orderby'] = 'date';
		}

		$orderby = 'ORDER BY ' . esc_sql( $pair[ $args['orderby'] ] ) . ' ' . esc_sql( $args['order'] );
	}

	if ( ! empty( $args['categories'] ) ) {
		$categories = array();
		foreach ( $args['categories'] as $categoryId ) {
			$categories   = array_merge( $categories, get_term_children( $categoryId, 'product_cat' ) );
			$categories[] = (int) $categoryId;
		}

		$categories = array_unique( $categories );
		if ( ! empty( $categories ) ) {
			$join  .= ' INNER JOIN ' . $tbl_tr . ' AS tr1 ON (' . $tbl_posts . '.ID = tr1.object_id)';
			$where .= ' AND tr1.term_taxonomy_id IN (' . implode( ',', $categories ) . ')';
		}
	}

	if ( ! empty( $args['tags'] ) ) {
		$tags = $args['tags'];
		if ( ! empty( $tags ) ) {
			$join  .= ' INNER JOIN ' . $tbl_tr . ' AS tr2 ON (' . $tbl_posts . '.ID = tr2.object_id)';
			$where .= ' AND tr2.term_taxonomy_id IN (' . implode( ',', $tags ) . ')';
		}
	}

	if ( ! empty( $args['is_new'] ) ) {
		$where .= " AND wp_posts.post_date > '" . gmdate( 'Y-m-d H:i:s', strtotime( '2 weeks ago' ) ) . "'";
	}

	if ( ! empty( $args['in_stock'] ) ) {
		$where .= ' AND pml.stock_status = "instock"';
	}

	if ( ! empty( $args['on_sale'] ) ) {
		$where .= ' AND pml.onsale = 1';
	}

	if ( ! empty( $args['location'] ) && 'all' !== $args['location'] ) {
		$where .= ' AND pml.location = "' . esc_sql( $args['location'] ) . '"';
	}

	$sql = $select . $join . ' WHERE ' . $where . ' ' . $groupby . ' ' . $orderby . ' ' . $limit;

	return $wpdb->get_col( $sql ); // phpcs:ignore WordPress.DB
}