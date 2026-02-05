<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Deepknead_ACF_Autoload_Blocks {

	protected static $instance;
	public $file = __FILE__;
	public $directory = '';
	public $prefix = 'DK Blocks';

	public static function get_instance() {
		if ( empty( self::$instance ) && ! ( self::$instance instanceof Deepknead_ACF_Autoload_Blocks ) ) {
			self::$instance = new Deepknead_ACF_Autoload_Blocks();
		}

		return self::$instance;
	}

	public function __construct() {

		add_action( 'acf/init', [ $this, 'acf_init' ], 999 );
		add_action( 'acf/include_fields', [ $this, 'add_preview_image_to_blocks' ], 20 );
		add_filter( 'acf/settings/load_json', [ $this, 'acf_load_json' ], 999 );
		add_filter( 'acf/settings/save_json', [ $this, 'acf_save_json' ], 999 );
		add_filter( 'acf/location/rule_operators', array( $this, 'add_starts_with_operator' ) );
		add_filter( 'acf/location/rule_match/block', array( $this, 'match_starts_with_operator' ), 10, 4 );
		add_filter( 'block_categories_all', [ $this, 'register_category' ], 999, 2 );

		// add_action( 'save_post', [ $this, 'set_post_block_meta' ] );
	}

	// ACF Init
	public function acf_init() {
		$this->directory = Deepknead_ACF_Autoload_Blocks::get_directory();
		$this->register_blocks();
	}

	// ACF Load JSON
	public function acf_load_json( $paths ) {
		// Block Support
		$autoload_blocks = Deepknead_ACF_Autoload_Blocks::get_autoload_blocks();

		foreach ( $autoload_blocks as $autoload_block ) {
			$paths[] = $autoload_block['dir'];
		}

		return $paths;
	}

	// ACF Save JSON
	public function acf_save_json( $path ) {
		if ( ! empty( $_POST['acf_field_group']['key'] ) ) {
			$slug = $_POST['acf_field_group']['key'];

			if ( strpos( $slug, 'group_block_' ) > - 1 ) {
				$block = $this->directory . '/' . str_ireplace( [ 'group_block_', '_' ], [ '', '-' ], $slug );

				if ( is_dir( $block ) ) {
					$path = $block;
				}
			}
		}

		return $path;
	}

	// Register category
	public function register_category( $categories, $post ) {
		$categories = array_merge( [
			[
				'slug'  => 'deepknead_blocks',
				'title' => $this->prefix,
				'icon'  => '',
			],
		], $categories );

		return $categories;
	}

	// Register blocks
	public function register_blocks() {
		if ( function_exists( 'register_block_type' ) ) {
			$autoload_blocks = Deepknead_ACF_Autoload_Blocks::get_autoload_blocks();

			foreach ( $autoload_blocks as $autoload_block ) {
				$options = [
					'render_callback' => [ $this, 'render_block' ],
				];

				$options = apply_filters( 'acf/autoload_blocks/parse_block_options', $options );

				register_block_type( $autoload_block['dir'], $options );
			}
		}
	}

	// Render block
	public function render_block( $block ) {
		$slug     = str_replace( 'acf/block-', '', $block['name'] );
		$slug     = str_replace( 'acf/', '', $slug );
		$file     = apply_filters( 'acf/autoload_blocks/cli/template_file', 'template', $slug );
		$template = $slug . '/' . $file;

		$this->do_render_block( $block, $slug, $template );
	}

	// Render block
	public function do_render_block( $block, $slug, $template ) {
		ob_start();

		if ( is_admin() ) {
			$this->render_admin_preview( $block, $slug );
		} else {
			$this->render_frontend_template( $block, $template );
		}

		$content = ob_get_clean();

		if ( is_admin() ) {
			$content = apply_filters( 'acf/autoload_blocks/render_block', $content, $block );
		}

		echo $content;
	}

	private function render_admin_preview( $block, $slug ) {
		$preview_image    = get_field( 'preview_image' );
		$dir              = Deepknead_ACF_Autoload_Blocks::get_directory() . '/';
		$has_preview_file = file_exists( $dir . $slug . '/preview.jpg' );

		if ( $preview_image ) {
			$preview = $this->create_preview_image_html( $preview_image );
		} elseif ( ! empty( $block['preview'] ) || $has_preview_file ) {
			$preview = $this->create_default_preview_html( $slug );
		} else {
			$preview = $this->create_fallback_preview_html( $block );
		}

		echo apply_filters( 'acf/autoload_blocks/block_preview', $preview, $block );
	}

	private function render_frontend_template( $block, $template ) {
		$data = apply_filters( 'acf/autoload_blocks/block_data', get_fields(), $block );
		$this->template_part( $template, [
			'is_admin' => false,
			'block'    => $block,
			'data'     => $data,
		] );
	}

	private function create_preview_image_html( $preview_image ) {
		return '<img src="' . $preview_image . '" alt="" class="autoload_blocks_preview_image">';
	}

	private function create_default_preview_html( $slug ) {
		$src = Deepknead_ACF_Autoload_Blocks::get_directory_uri() . '/' . $slug . '/preview.jpg';

		return '<img src="' . $src . '" alt="" class="autoload_blocks_preview_image">';
	}

	private function create_fallback_preview_html( $block ) {
		return '<p>' . $block['title'] . ' (' . $block['name'] . ')</p>';
	}

	// Load block template
	public function template_part( $template, $args ) {
		$path = $this->directory . '/' . $template . '.php';
		if ( file_exists( $path ) ) {
			extract( $args );
			include $path;
		} else {
			echo __( 'Block template file not found.', 'autoload_block' ) . ' (' . $template . '.php)';
		}
	}

	// Get auto blocks
	public static function get_autoload_blocks() {
		$dir             = Deepknead_ACF_Autoload_Blocks::get_directory() . '/';
		$scan            = scandir( $dir );
		$autoload_blocks = [];

		foreach ( $scan as $slug ) {
			if ( in_array( $slug, [ '.', '..' ] ) || ! is_dir( $dir . $slug ) || strstr( $dir . $slug, $slug . '/_' ) ) {
				continue;
			}

			$json          = $dir . $slug . '/block.json';
			$template_file = $dir . $slug . '/template.php';

			if ( file_exists( $json ) && file_exists( $template_file ) ) {
				$json_data    = json_decode( file_get_contents( $json ), true );
				$preview_file = $dir . $slug . '/preview.jpg';
				if ( file_exists( $preview_file ) ) {
					$json_data['preview'] = true;
				}
				$json_data['preview']     = true;
				$args                     = [
					'key'                => $slug,
					'autoload_block_key' => $slug,
					// Backward compatability
					'acf_key'            => Deepknead_ACF_Autoload_Blocks::snake_case( 'group_block_' . $slug ),
					// field group key
					'dir'                => $dir . $slug,
					'json'               => $json,
					'settings'           => $json_data,
				];

				$autoload_blocks[ $slug ] = apply_filters( 'acf/autoload_blocks/block_settings', $args );
			}
		}

		return $autoload_blocks;
	}

	public function add_preview_image_to_blocks() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}
		acf_add_local_field_group( array(
			'key'                   => 'group_68a2e056a17d0',
			'title'                 => 'Preview Image',
			'fields'                => array(
				array(
					'key'               => 'field_68a2e0560c5b0',
					'label'             => 'Preview Image',
					'name'              => 'preview_image',
					'aria-label'        => '',
					'type'              => 'image',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'return_format'     => 'url',
					'library'           => 'all',
					'min_width'         => '',
					'min_height'        => '',
					'min_size'          => '',
					'max_width'         => '',
					'max_height'        => '',
					'max_size'          => '',
					'mime_types'        => '',
					'allow_in_bindings' => 0,
					'preview_size'      => 'thumbnail',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'block',
						'operator' => 'starts_with',
						'value'    => 'acf/',
					),
				),
			),
			'menu_order'            => 999,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		) );
	}

	public function add_starts_with_operator( $choices ) {
		$choices['starts_with'] = 'Starts with';

		return $choices;
	}

	public function match_starts_with_operator( $match, $rule, $options, $field_group ) {
		if ( $rule['operator'] === 'starts_with' ) {
			$current = $options['block'] ?? '';
			$value   = $rule['value'] ?? '';
			$match   = ( $value && strpos( $current, $value ) === 0 );
		}

		return $match;
	}

	// Return block data
	public static function get_post_blocks( $id = false ) {
		if ( empty( $id ) ) {
			$id = get_the_id();
		}

		$post   = get_post( $id );
		$blocks = [];

		if ( has_blocks( $post->post_content ) ) {
			$all_blocks = parse_blocks( $post->post_content );

			foreach ( $all_blocks as $block ) {
				if ( ! empty( $block['blockName'] ) ) {
					$blocks[] = $block;
				}
			}
		}

		return $blocks;
	}

	// Save block data as post meta on save
	public function set_post_block_meta( $post_id ) {
		$blocks = Deepknead_ACF_Autoload_Blocks::get_post_blocks( $post_id );

		foreach ( $blocks as $block ) {
			if ( strpos( $block['blockName'], 'acf/' ) == 0 ) {
				// $block_id = $block['attrs']['id'];
				if ( ! empty( $block['attrs']['data'] ) ) {
					foreach ( $block['attrs']['data'] as $field_key => $field_val ) {
						if ( substr( $field_key, 0, 1 ) !== '_' && ! empty( $block['attrs']['data'][ '_' . $field_key ] ) ) {
							// $field_obj = get_field_object( $field_key, $block_id );
							$field_obj = get_field_object( $block['attrs']['data'][ '_' . $field_key ] );

							if ( ! empty( $field_obj['autoload_block_save_to_meta'] ) ) {
								$stm_key = $field_obj['autoload_block_save_to_meta'];

								if ( $field_obj['type'] == 'repeater' ) { // repeater
									update_post_meta( $post_id, $stm_key, $field_val );
									update_post_meta( $post_id, '_' . $stm_key, $field_obj['key'] );

									foreach ( $block['attrs']['data'] as $subfield_key => $subfield_val ) {
										if ( substr( $subfield_key, 0, 1 ) !== '_' && strpos( $subfield_key, $field_key . '_' ) > - 1 ) {
											$stm_sub_key = str_ireplace( $field_key, $stm_key, $subfield_key );

											update_post_meta( $post_id, $stm_sub_key, $subfield_val );
											update_post_meta( $post_id, '_' . $stm_sub_key, $block['attrs']['data'][ '_' . $subfield_key ] );
										}
									}

								} else { // Standard fields
									if ( $stm_key == '_thumbnail_id' ) {
										// set_post_thumbnail( $post_id, $field_val );
										update_post_meta( $post_id, $stm_key, $field_val );
									} else {
										update_post_meta( $post_id, $stm_key, $field_val );
										update_post_meta( $post_id, '_' . $stm_key, $field_obj['key'] );
									}
								}
							}

						}
					}
				}
			}
		}

	}

	// Helpers
	// Get Directory
	public static function get_directory() {
		return apply_filters( 'acf/autoload_blocks/directory', get_stylesheet_directory() . '/inc/site/acf/blocks' );
	}

	public static function get_directory_uri() {
		return apply_filters( 'acf/autoload_blocks/directory_uri', get_stylesheet_directory_uri() . '/inc/site/acf/blocks' );
	}

	public static function snake_case( $text = '' ) {
		return strtolower( str_ireplace( '-', '_', $text ) );
	}

	public static function kebab_case( $text = '' ) {
		return strtolower( str_ireplace( '_', '-', $text ) );
	}

	public static function title_case( $text = '' ) {
		return ucwords( str_ireplace( [ '_', '-' ], ' ', $text ) );
	}

}

// Instance
Deepknead_ACF_Autoload_Blocks::get_instance();
