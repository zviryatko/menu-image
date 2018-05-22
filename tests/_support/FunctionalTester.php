<?php


/**
 * Inherited Methods
 * @method void wantToTest( $text )
 * @method void wantTo( $text )
 * @method void execute( $callable )
 * @method void expectTo( $prediction )
 * @method void expect( $prediction )
 * @method void amGoingTo( $argumentation )
 * @method void am( $role )
 * @method void lookForwardTo( $achieveValue )
 * @method void comment( $description )
 * @method \Codeception\Lib\Friend haveFriend( $name, $actorClass = null )
 *
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends \Codeception\Actor {
	use _generated\FunctionalTesterActions;

	/**
	 * Define custom actions here
	 */
	public function logOut() {
		if ( ! empty( $this->grabWordPressAuthCookie() ) ) {
			$this->amOnPage( '/wp-logout.php' );
			$this->seeLink( 'log out' );
			$this->click( 'log out' );
			$this->seeCurrentUrlMatches( '/wp-login.php?loggedout=true' );
		}
	}

	public function haveSelectMediaAttachment( $attachment_id ) {
		// Get active uploader id.
		$frame_html_id = '#' . $this->grabAttributeFrom( 'div.supports-drag-drop:not([style*="display: none"]):last-child .media-frame', 'id' );
		$this->seeElement( $frame_html_id );
		$this->see( 'Chose menu image', $frame_html_id );
		// Select image from library.
		$this->click( 'Media Library', $frame_html_id );
		// Check if uploaded file exists.
		$this->seeElement( "{$frame_html_id} li.attachment", [ 'data-id' => $attachment_id ] );
		$button_css = "$frame_html_id .media-toolbar-primary button.media-button-select";
		$disabled   = $this->grabAttributeFrom( $button_css, 'disabled' );
		$this->assertNotEmpty( $disabled );
		$this->click( "{$frame_html_id} li.attachment[data-id='{$attachment_id}']" );
		$disabled = $this->grabAttributeFrom( $button_css, 'disabled' );
		$this->assertIsEmpty( $disabled );
		$this->click( $button_css );
	}
}
