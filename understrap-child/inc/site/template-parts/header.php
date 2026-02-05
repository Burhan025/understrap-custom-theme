<?php
/**
 * The header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$bootstrap_version = get_theme_mod( 'understrap_bootstrap_version', 'bootstrap4' );
$navbar_type       = get_theme_mod( 'understrap_navbar_type', 'collapse' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
	<script>var $ = jQuery.noConflict();</script>

</head>

<body <?php body_class(); ?> <?php understrap_body_attributes(); ?>>
<?php do_action( 'wp_body_open' ); ?>
<div class="site" id="page">

	<!-- ******************* The Navbar Area ******************* -->
	<?php
	$is_sticky = get_field( 'sticky_header', 'option' );
	$is_home   = is_front_page();
	?>
	<header id="wrapper-navbar"  class="header <?php echo $is_sticky ? 'sticky-top' : ''; ?> <?php echo $is_home ? 'transparent-header' : ''; ?>">

		<a class="skip-link <?php echo understrap_get_screen_reader_class( true ); ?>" href="#content">
			<?php esc_html_e( 'Skip to content', 'understrap' ); ?>
		</a>
		<div class="header-top container pt-3 py-lg-3">
			<div class="header-left d-none d-lg-flex p-3">
				<div class="menu-wrapper">
					<button
						class="navbar-toggler menu-toggle"
						type="button"
						aria-label="<?php esc_attr_e( 'Toggle navigation', 'understrap' ); ?>"
					>
						<span class="navbar-toggler-icon"></span>
						<span class="menu-text text-light">Menu</span>
					</button>
					<!-- Dropdown menu -->
					<nav class="menu-dropdown">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'container'      => false,
								'menu_id'        => 'main-menu',
								'menu_class'     => 'menu-list nav nav-bar text-dark',
								'fallback_cb'    => false,
							)
						);
						?>
					</nav>

				</div>

			</div>

			<div class="logo">
				<a href="<?php echo esc_url( home_url() ); ?>">
					<?php
					$logo_group = get_field('home_front_page_logo', 'option');
					$logo_light = $logo_group['homepage_logo_light'] ?? '';
					$logo_dark  = $logo_group['homepage_logo_dark'] ?? '';
					$default_logo = bs_get_logo_url();
					?>

					<?php if ( is_front_page() && !empty( $logo_light['url'] ) && !empty( $logo_dark['url'] ) ) : ?>
						<img class="logo-light" src="<?php echo esc_url( $logo_light['url'] ); ?>" alt="Logo Light">
						<img class="logo-dark" src="<?php echo esc_url( $logo_dark['url'] ); ?>" alt="Logo Dark">
					<?php else : ?>
						<img class="logo-default" src="<?php echo esc_url( $default_logo ); ?>" alt="Logo">
					<?php endif; ?>
				</a>
			</div>
			<div class="header-right align-items-center">
				<div class="become-member d-none d-lg-none">
					<?php
					// Example button block (Primary)
					echo '<a href="/loyalty-program/" class=" btn">Become a Member</a>';
					?>
				</div>
				<!-- add location button here -->
				<div class="location-button d-lg-flex">
					<?php echo do_shortcode( '[bs_location_selector]' ); ?>
				</div>
				<!-- Search icon-->
				<div class="search-icon d-none d-lg-flex align-items-lg-center">
					<a class="search-button" href="#search-form">
						<svg class="icon-search"  xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<path d="M15.7549 14.255H14.9649L14.6849 13.985C15.6649 12.845 16.2549 11.365 16.2549 9.755C16.2549 6.165 13.3449 3.255 9.75488 3.255C6.16488 3.255 3.25488 6.165 3.25488 9.755C3.25488 13.345 6.16488 16.255 9.75488 16.255C11.3649 16.255 12.8449 15.665 13.9849 14.685L14.2549 14.965V15.755L19.2549 20.745L20.7449 19.255L15.7549 14.255ZM9.75488 14.255C7.26488 14.255 5.25488 12.245 5.25488 9.755C5.25488 7.26501 7.26488 5.255 9.75488 5.255C12.2449 5.255 14.2549 7.26501 14.2549 9.755C14.2549 12.245 12.2449 14.255 9.75488 14.255Z" fill="currentColor"/>
						</svg>
					</a>
				</div>
				<!-- add dashbozard link here -->
				<div class="dashboard-link d-lg-flex align-self-center align-items-lg-center">
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM7.35 18.5C8.66 17.56 10.26 17 12 17C13.74 17 15.34 17.56 16.65 18.5C15.34 19.44 13.74 20 12 20C10.26 20 8.66 19.44 7.35 18.5ZM18.14 17.12C16.45 15.8 14.32 15 12 15C9.68 15 7.55 15.8 5.86 17.12C4.7 15.73 4 13.95 4 12C4 7.58 7.58 4 12 4C16.42 4 20 7.58 20 12C20 13.95 19.3 15.73 18.14 17.12Z" fill="currentColor"/>
							<path d="M12 6C10.07 6 8.5 7.57 8.5 9.5C8.5 11.43 10.07 13 12 13C13.93 13 15.5 11.43 15.5 9.5C15.5 7.57 13.93 6 12 6ZM12 11C11.17 11 10.5 10.33 10.5 9.5C10.5 8.67 11.17 8 12 8C12.83 8 13.5 8.67 13.5 9.5C13.5 10.33 12.83 11 12 11Z" fill="currentColor"/>
						</svg>
					</a>
				</div>
				<!-- add cart icon here -->
				<?php echo do_shortcode( '[bs_cart_icon]' ); ?>

			</div>
		</div>
		<!-- add search form here -->
		<div class="header-search-form d-none d-lg-block">
			<div id="search-form" class="container">

				<?php get_search_form(); ?>
			</div>
		</div>
		<div class="header-mid-mb d-flex justify-content-between align-items-center d-lg-none p-3">
			<div class="mobile-menu d-flex d-lg-none justify-content-between align-items-center">
				<button
					class="navbar-toggler"
					type="button"
					data-bs-toggle="collapse"
					data-bs-target="#navbarNavDropdown"
					aria-controls="navbarNavDropdown"
					aria-expanded="false"
					aria-label="<?php esc_attr_e( 'Toggle navigation', 'understrap' ); ?>"
				>
					<span class="navbar-toggler-icon"></span>
				</button>

				<!-- The WordPress Menu goes here -->
				<div id="navbarNavDropdown" class="navbar-collapse collapse px-4">
					<!-- Optional Close Button inside menu (custom addition) -->
					<div class="mobile-menu-top d-flex justify-content-end my-3" style="text-align: right;">
<!--						<h5 class="menu-title">-->
<!--						</h5>-->
						<button class="btn btn-close" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"></button>
					</div>
					<div class="become-member d-none d-lg-none">
						<div class="content  mb-3 ">
							<?php echo esc_attr__( 'Become part of our growing community and unlock special perks.', 'woocommerce' ); ?>
						</div>
						<div class="button btn w-100 mb-3">
							<?php
							// Example button block (Primary)
							echo '<a href="/loyalty-program/" class=" btn">Become a Member</a>';
							?>
						</div>

					</div>
					<?php
					wp_nav_menu(
						array(
							'container'      => '',
							'theme_location' => 'primary',
							'menu_class'     => 'nav nav-dropdown',
							'fallback_cb'    => '',
						)
					);

					wp_nav_menu(
						array(
							'container'      => '',
							'theme_location' => 'shop',
							'menu_class'     => 'nav nav-dropdown',
							'fallback_cb'    => '',
						)
					);
					?>
				</div>

			</div>
			<div class="w-100 mobile-search-wrapper d-lg-none">
				<?php get_search_form(); ?>
			</div>
		</div>

		<!-- add menu here -->
		<div class="header-bottom">
			<div class="container">
				<div class="location-button d-lg-none d-flex justify-content-center">
					<?php echo do_shortcode( '[bs_location_selector]' ); ?>
				</div>
				<?php
				wp_nav_menu(
					array(
						'theme_location'  => 'shop',
						'container_class' => 'desktop-menu',
						'menu_id'         => 'shop-menu',
						'menu_class'      => 'nav nav-bar',
						'fallback_cb'     => '',
					)
				);
				?>
			</div>
		</div>

	</header><!-- #wrapper-navbar -->
