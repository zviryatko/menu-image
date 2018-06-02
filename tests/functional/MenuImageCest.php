<?php

class MenuImageCest {
	public function _before( FunctionalTester $I ) {
		$I->useTheme( 'twentyseventeen' );
		// Need this to use core functions.
		$I->bootstrapWp();
	}

	public function _after( FunctionalTester $I ) {
	}

	/**
	 * @dataProvider imagesProvider
	 */
	public function testMenuImagesUpload( FunctionalTester $I, \Codeception\Example $examples ) {
		// Logout to be sure that's anon user.
		$I->logOut();
		$I->loginAsAdmin();
		$I->haveMenuInDatabase( 'Navigation', 'top' );
		$item_id = $I->haveMenuItemInDatabase( 'Navigation', 'Test link' );
		$html_id = '#menu-item-' . $item_id;

		// Check and open menu item.
		$I->amOnAdminPage( '/nav-menus.php' );
		$I->click( "{$html_id} a#edit-{$item_id}" );
		$I->see( 'Menu image', "{$html_id} .menu-item-images label" );
		$I->see( 'Image on hover', "{$html_id} .menu-item-images label" );

		// Upload images first.
		$images = [];
		foreach ( $examples as $key => $example ) {
			$images[ $key ]['image_id'] = $I->haveAttachmentInDatabase( codecept_data_dir( $example['image'] ) );
			$I->seeAttachmentInDatabase( [ 'ID' => $images[ $key ]['image_id'] ] );
		}

		// Then select it.
		foreach ( $examples as $key => $example ) {
			$I->click( $example['button'], "$html_id {$example['button_css']}" );
			$I->haveSelectMediaAttachment( $images[ $key ]['image_id'] );
			$image_src = $I->grabAttributeFrom( "{$html_id} {$example['button_css']} label a img", 'src' );
			$I->assertEquals( basename( $image_src ), $example['image'] );
		}
	}

	/**
	 * @return array
	 */
	protected function imagesProvider() {
		$image = [
			'button'     => 'Set image',
			'button_css' => '.menu-item-images p.description:first-child',
			'image'      => 'image.png',
		];
		$hover = [
			'button'     => 'Set image on hover',
			'button_css' => '.menu-item-images p.description:last-child',
			'image'      => 'hover.png',
		];

		return [
			[ $image ],
			[ $hover ],
			[ $image, $hover ],
		];
	}

	/**
	 * @dataProvider menuItemsProvider
	 */
	public function testMenuImagesDisplay( FunctionalTester $I, \Codeception\Example $examples ) {
		// Prepare menu, items and their values.
		$I->haveMenuInDatabase( 'Navigation', 'top' );
		$item_id = $I->haveMenuItemInDatabase( 'Navigation', 'Test link' );
		$thumbnail_id = $I->haveAttachmentInDatabase( codecept_data_dir( $examples[ 'thumbnail' ][ 0 ] ) );
		$I->havePostmetaInDatabase( $item_id, '_thumbnail_id', $thumbnail_id );
		if ( ! empty( $examples[ 'thumbnail' ][ 1 ] ) ) {
			$hover_id = $I->haveAttachmentInDatabase( codecept_data_dir( $examples[ 'thumbnail' ][ 1 ] ) );
			$I->havePostmetaInDatabase( $item_id, '_thumbnail_hover_id', $hover_id );
		}
		// Regenerate images after upload.
		$I->cli('--yes media regenerate');
		$I->havePostmetaInDatabase( $item_id, '_menu_item_image_title_position', $examples[ 'image_title_position' ] );
		$I->havePostmetaInDatabase( $item_id, '_menu_item_image_size', $examples[ 'image_size' ] );
		// Now check everything.
		$I->amOnPage('/');
		$I->seeElement("#menu-item-$item_id a");
		$I->seeElement("#menu-item-$item_id a.menu-image-title-{$examples[ 'image_title_position' ]}");
		list($w, $h) = explode('x', explode('-', $examples[ 'image_size' ])[1]);
		$I->seeElement("#menu-item-$item_id img", ['width' => $w, 'height' => $h]);
		$thumb_id = get_post_meta($item_id, '_thumbnail_id', true);
		$I->seeElement("#menu-item-$item_id img", ['src' => reset(wp_get_attachment_image_src($thumb_id, $examples[ 'image_size' ]))]);
		if (!empty($examples[ 'thumbnail' ][ 1 ])) {
			$hover_id = get_post_meta($item_id, '_thumbnail_hover_id', true);
			$I->seeElement("#menu-item-$item_id img", ['src' => reset(wp_get_attachment_image_src($hover_id, $examples[ 'image_size' ]))]);
		}
	}

	/**
	 * Provide all possible values for menu image item configuration.
	 *
	 * @return array
	 *   Examples will be in next format:
	 *      - Array of options with next keys:
	 *          - field: configuration field name.
	 *          - value: selected value.
	 *          - value_set_callback: callback to set value programmatically to speed up testing.
	 *          - test_callback: callback to test that option looks right.
	 */
	protected function menuItemsProvider() {
		$options  = [
			'image_title_position' => [ 'hide', 'above', 'below', 'before', 'after' ],
			'image_size'           => [ 'menu-24x24', 'menu-36x36', 'menu-48x48' ],
			'thumbnail'            => [
				[ 'image.png' ], // Just an image.
				[ 'image.png', 'hover.png' ], // Image with hover.
			],
		];
		$examples = [];
		// Iterate through all variants and create unique pairs.
		$examples_count = array_product( array_map( 'count', $options ) );
		foreach ( $options as $field => $variants ) {
			for ( $i = 1; $i <= $examples_count; $i ++ ) {
				$examples[ $i ][ $field ] = $variants[ $i % count( $variants ) ];
			}
		}

		// Examples self-test (it's answer on: who should test the tests?).
		$examples_test = array_map('serialize', $examples);
		$uniq = array_unique($examples_test) === $examples_test;
		if (!$uniq) {
			throw new \Exception('Provided examples are not unique.');
		}

		return $examples;
	}
}
