<?php

class MenuImageCest {
	public function _before( FunctionalTester $I ) {
		//$I->activatePlugin('menu-image');
		$I->useTheme( 'twentyseventeen' );
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
}
