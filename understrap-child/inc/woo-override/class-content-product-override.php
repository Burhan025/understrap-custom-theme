<?php
/**
 * Content product override
 *
 * @package BurhanAftab\UnderstrapChild\WooOverride
 */

namespace BurhanAftab\UnderstrapChild\WooOverride;

/**
 * Content product override
 */
class Content_Product_Override {
	/**
	 * Constructor
	 */
	public function init() {
		add_filter( 'woocommerce_sale_flash', array( $this, 'sale_percentage_badge' ), 20, 3 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'new_product_badge' ), 9 );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'new_product_badge' ), 10 );

		add_action( 'woocommerce_before_main_content', array( $this, 'before_main_content' ), 10 );
		add_action( 'woocommerce_after_main_content', array( $this, 'after_main_content' ), 10 );

		add_action( 'woocommerce_before_shop_loop', array( $this, 'before_shop_loop_open' ), 19 );
		add_action( 'woocommerce_before_shop_loop', array( $this, 'before_shop_loop_close' ), 99 );

		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		add_action( 'woocommerce_before_shop_loop', array( $this, 'layout_switcher' ), 30 );

		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'item_content_open' ), 99 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'item_content_close' ), 99 );

		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
		add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 11 );

		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_to_cart_open' ), 8 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_to_cart_close' ), 11 );

		add_filter( 'paginate_links_output', array( $this, 'custom_pagination_numbers' ), 10 );

		add_action( 'woocommerce_shop_loop_item_title', array( $this, 'add_title_link' ), 9 );
		add_action( 'woocommerce_shop_loop_item_title', array( $this, 'close_title_link' ), 11 );

		add_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'loop_add_to_cart_args' ), 99 );

		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_breadcrumb', 4 );

		add_filter( 'woocommerce_related_products', array( $this, 'location_aware_related_products' ), 20, 3 );
	}

	/**
	 * Sale percentage instead of sale flash
	 *
	 * @param string $html HTML.
	 * @param object $post Post.
	 * @param object $product Product.
	 * @return string
	 */
	public function sale_percentage_badge( $html, $post, $product ) {
		global $bsCurrentLocationSlug;
		if ( empty( $bsCurrentLocationSlug ) ) {
			return '';
		}

		if ( $product->is_type( 'variable' ) ) {
			// Get max discount among variations.
			$max_percentage = 0;
			foreach ( $product->get_children() as $child_id ) {
				$variation = wc_get_product( $child_id );


				$variationAttributes = $variation->get_attributes();
				$variationLocation = isset( $variationAttributes['pa_locations'] ) ? $variationAttributes['pa_locations'] : '';
				if ( empty( $variationLocation ) || $variationLocation !== $bsCurrentLocationSlug ) {
					continue;
				}

				if ( $variation->is_on_sale() ) {
					$regular = $variation->get_regular_price();
					$sale    = $variation->get_sale_price();
					if ( $regular > 0 && $sale > 0 ) {
						$percentage = round( ( ( $regular - $sale ) / $regular ) * 100 );
						if ( $percentage > $max_percentage ) {
							$max_percentage = $percentage;
						}
					}
				}
			}
			if ( $max_percentage > 0 ) {
				return '<span class="onsale">-' . $max_percentage . '%</span>';
			}
		} elseif ( $product->is_on_sale() ) {
			$regular = $product->get_regular_price();
			$sale    = $product->get_sale_price();
			if ( $regular > 0 && $sale > 0 ) {
				$percentage = round( ( ( $regular - $sale ) / $regular ) * 100 );
				return '<span class="onsale">-' . $percentage . '%</span>';
			}
		}
		return '';
	}

	/**
	 * New product badge
	 */
	public function new_product_badge() {
		global $product;

		// Don't show "New" badge if product is on sale.
		if ( $product->is_on_sale() ) {
			return;
		}

		$post_date          = get_post_time( 'U', true, $product->get_id() );
		$now                = time();
		$days_since_created = ( $now - $post_date ) / DAY_IN_SECONDS;

		if ( $days_since_created <= 14 ) {
			echo '<span class="new-badge">New</span>';
		}
	}

	/**
	 * Add to cart open
	 */
	public function add_to_cart_open() {
		echo '<div class="bs-add-to-cart-wrapper">';
	}

	/**
	 * Add to cart close
	 */
	public function add_to_cart_close() {
		echo '</div>';
	}

	/**
	 * Custom pagination numbers
	 */
	public function custom_pagination_numbers( $link ) {
		return preg_replace_callback(
			'/>(\d+)</',
			static function ( $matches ) {
				return '>' . sprintf( '%02d', $matches[1] ) . '<';
			},
			$link
		);
	}

	/**
	 * Layout switcher
	 */
	public function layout_switcher() {
		?>
		<div class="bs-layout-switcher">
			<button class="bs-layout-switcher__button bs-layout-switcher__button--grid" data-layout="grid">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="Cart"><g id="Vector"><path d="M13.2222 23L23 23L23 13.2222L13.2222 13.2222L13.2222 23Z" fill="white"/><path d="M13.2222 10.7778L23 10.7778L23 1L13.2222 0.999999L13.2222 10.7778Z" fill="white"/><path d="M1 23L10.7778 23L10.7778 13.2222L1 13.2222L1 23Z" fill="white"/><path d="M1 10.7778L10.7778 10.7778L10.7778 0.999999L1 0.999998L1 10.7778Z" fill="white"/></g></g></svg>
			</button>
			<button class="bs-layout-switcher__button bs-layout-switcher__button--list" data-layout="list">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="Cart" clip-path="url(#clip0_17412_9408)"><path id="Vector" d="M30 2L-6 2L-6 5.33333L30 5.33333L30 2ZM30 10.3333L-6 10.3333L-6 13.6667L30 13.6667L30 10.3333ZM30 22L30 18.6667L-6 18.6667L-6 22L30 22Z" fill="white"/></g><defs><clipPath id="clip0_17412_9408"><rect width="24" height="24" fill="white" transform="translate(24 24) rotate(-180)"/></clipPath></defs></svg>
			</button>
		</div>
		<?php
	}

	/**
	 * Wrapper start
	 */
	public function before_main_content() {
		$container = get_theme_mod( 'understrap_container_type' );
		if ( false === $container ) {
			$container = '';
		}
		?>
		<div class="wrapper" id="woocommerce-wrapper">
			<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">
				<div class="row">
					<?php if ( is_shop() || is_product_category() || is_product_tag() ) { ?>
					<div class="col-lg-3 widget-area" id="left-sidebar">
						<?php echo do_shortcode( '[bs_product_filters]' ); ?>
						<?php
						if ( is_active_sidebar( 'sidebar-shop' ) ) {
							dynamic_sidebar( 'sidebar-shop' );
						}
						?>
					</div>
					<div class="col-lg-9 content-area" id="primary">
					<?php } else { ?>
					<div class="col-lg content-area" id="primary">
					<?php } ?>
						<main class="site-main" id="main">
						<?php
	}

	/**
	 * Wrapper end
	 */
	public function after_main_content() {
		?>
						</main>
					</div>
				</div><!-- .row -->
			</div><!-- .container(-fluid) -->
		</div><!-- #woocommerce-wrapper -->
		<?php
	}

	/**
	 * Before shop loop open
	 */
	public function before_shop_loop_open() {
		echo '<div class="bs-before-shop-loop">';
	}

	/**
	 * Before shop loop close
	 */
	public function before_shop_loop_close() {
		echo '</div>';
	}

	/**
	 * Item content open
	 */
	public function item_content_open() {
		echo '<div class="bs-loop-item-content">';
	}

	/**
	 * Item content close
	 */
	public function item_content_close() {
		echo '</div>';
	}

	/**
	 * Add link open to the product title
	 */
	public function add_title_link() {
		global $product;

		if ( ! ( $product instanceof \WC_Product ) ) {
			return;
		}

		$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );

		echo '<a href="' . esc_url( $link ) . '" class="bs-loop-item-title__link">';
	}

	/**
	 * Close link open to the product title
	 */
	public function close_title_link() {
		echo '</a>';
	}

	/**
	 * Add to cart button class
	 *
	 * @param array $args Arguments.
	 * @param object $product Product.
	 * @return array
	 */
	public function loop_add_to_cart_args( $args ) {
		if ( $args['class'] ) {
			$args['class'] .= ' bs-add-to-cart-button';
		} else {
			$args['class'] = 'bs-add-to-cart-button';
		}
		return $args;
	}

	/**
	 * Make related products location-aware using bs_get_products()
	 *
	 * @param array $related_ids Default related product IDs.
	 * @param int $product_id Current product ID.
	 * @param array $args Query args.
	 * @return array
	 */
	public function location_aware_related_products( $related_ids, $product_id, $args ) {

		if ( ! function_exists( 'bs_get_products' ) ) {
			return $related_ids;
		}

		// Get current product categories.
		$terms = get_the_terms( $product_id, 'product_cat' );
		$cat_ids = ( ! is_wp_error( $terms ) && ! empty( $terms ) )
			? wp_list_pluck( $terms, 'term_id' )
			: array();

		// BurhanAftab location-aware product list.
		$new_ids = bs_get_products( array(
			'categories' => $cat_ids,
			'in_stock'   => true,
			'orderby'    => 'rand',
			'order'      => 'ASC',
		) );

		// Remove the current product itself
		$new_ids = array_diff( $new_ids, array( $product_id ) );

		// Do NOT slice — return ALL
		return ! empty( $new_ids ) ? $new_ids : $related_ids;
	}
}
