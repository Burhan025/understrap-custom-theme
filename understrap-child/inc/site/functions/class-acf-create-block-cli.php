<?php
// wp dkblocks create_block foo-bar --name="Foo Bar" --icon="editor-code"

// // Exit if accessed directly.
 if ( ! defined( 'ABSPATH' ) ) {
   exit;
 }

class Deepknead_ACF_Autoload_Blocks_CLI {

	public function clean_cache( $args = [], $assoc_args = [] ) {
		$this->clean_blocks_cache();

		 WP_CLI::success( '🧹 Cache Cleaned' );
	}

	private function clean_blocks_cache() {
		delete_transient( 'autoload_blocks' );

		WP_CLI::success( '🧹 Block Cache Cleaned' );
	}

	public function create_block( $args = [], $assoc_args = [] ) {
		$key_kebab = Deepknead_ACF_Autoload_Blocks::kebab_case( $args[0] );
		$key_snake = Deepknead_ACF_Autoload_Blocks::snake_case( $key_kebab );
		$name      = Deepknead_ACF_Autoload_Blocks::title_case( ! empty( $assoc_args['name'] ) ? $assoc_args['name'] : $key_kebab );
		$icon      = ! empty( $assoc_args['icon'] ) ? $assoc_args['icon'] : 'editor-code';

		if ( empty( $key_kebab ) ) {
			WP_CLI::error( 'Missing block key' );
		}

		$dir = Deepknead_ACF_Autoload_Blocks::get_directory() . '/' . $key_kebab . '/';

		if ( is_dir( $dir ) ) {
			WP_CLI::error( "Block '{$key_kebab}' already exists" );
		}

		mkdir( $dir );

		$values = [
			'{key}'       => $key_kebab,
			'{key_kebab}' => $key_kebab,
			'{key_snake}' => $key_snake,
			'{dir}'       => $dir,
			'{name}'      => $name,
			'{icon}'      => $icon,
		];

		file_put_contents( $dir . '/block.json', $this->build_block_json( $values ) );
		WP_CLI::log( '✅ Created: block.json' );

		file_put_contents( $dir . "/group_block_{$key_snake}.json", $this->build_block_fieldgroup( $values ) );
		WP_CLI::log( "✅ Created: group_block_{$key_snake}.json" );

		$file = apply_filters( 'acf/autoload_blocks/cli/template_file', 'template', $key_kebab );

		file_put_contents( $dir . "/{$file}.php", $this->build_block_template( $values ) );
		WP_CLI::log( "✅ Created: {$file}.php" );

		do_action( 'acf/autoload_blocks/cli/make_block', $args, $assoc_args, $values );

		WP_CLI::success( '👌 Block Created: ' . $key_kebab );

		$this->clean_blocks_cache();
	}

	private function build_block_json( $values ) {
		$json = Deepknead_ACF_Autoload_Blocks_CLI::find_replace( '{
  "apiVersion": 2,
  "version": "1.0.0",
  "name": "acf/block-{key_kebab}",
  "title": "{name}",
  "description": "",
  "category": "deepknead_blocks",
  "icon": "{icon}",
  "keywords": [
  ],
  "acf": {
    "mode": "preview",
    "postTypes": [
      "post", "page", "locations"
    ]
  },
  "supports": {
    "align": false,
    "mode": false
  }
}', $values );

		return apply_filters( 'acf/autoload_blocks/cli/build_json', $json, $values );
	}

	// ACF Field Group

	private function build_block_fieldgroup( $values ) {
		$group = Deepknead_ACF_Autoload_Blocks_CLI::find_replace( '{
    "key": "group_block_{key_snake}",
    "title": "Block - {name}",
    "fields": [
    ],
    "location": [
        [
            {
                "param": "block",
                "operator": "==",
                "value": "acf\/block-{key_kebab}"
            }
        ]
    ],
    "menu_order": 0,
    "position": "normal",
    "style": "default",
    "label_placement": "top",
    "instruction_placement": "label",
    "hide_on_screen": "",
    "active": true,
    "description": "",
    "show_in_rest": 0,
    "modified": 1669655322
}', $values );

		return apply_filters( 'acf/autoload_blocks/cli/build_field_group', $group, $values );
	}

	// Template

	private function build_block_template( $values ) {
		$template = Deepknead_ACF_Autoload_Blocks_CLI::find_replace( '<?php
// $foo = $data[\'foo\']
if (!empty($data) && is_array($data)) {
	extract( $data );
}

?>
<div class="block_{key_snake}">

</div>', $values );

		return apply_filters( 'acf/autoload_blocks/cli/build_template', $template, $values );
	}

	static function find_replace( $text = '', $values = [] ) {
		$find    = array_keys( $values );
		$replace = $values;

		return str_ireplace( $find, $replace, $text );
	}

}

// Register CLI Commands
function Deepknead_ACF_Autoload_Blocks_cli_register_commands() {
	WP_CLI::add_command( 'dkblocks', 'Deepknead_ACF_Autoload_Blocks_CLI' );
}

add_action( 'cli_init', 'Deepknead_ACF_Autoload_Blocks_cli_register_commands' );
