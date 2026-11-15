<?php
/**
 * Contact Form 7 integration.
 *
 * Forms are edited in CF7 with the markup's field HTML:
 *   <label class="field"><span class="field__label">Name *</span>[text* your-name class:field__input placeholder "…"]</label>
 * The parts CF7 would wrap differently from the design are theme tags that
 * print the markup exactly:
 *   [vtf_select* type "Pick…" "Cafe" "Shop"]     → span.field__select > select (custom dropdown, app-select.js)
 *   [vtf_topics topic "Question" "Review" …]      → .form__topics radio pills (first checked)
 *   [vtf_consent* agree]                          → .check with the privacy-policy link (Settings → Privacy page)
 *   [vtf_submit "Send"]                           → .btn with the arrow tile
 *   [vtf_rating* rating]                          → the review stars (fieldset.rate)
 *   [vtf_product product]                         → hidden ID of the current product; the mail
 *                                                   shows its name. A form with [vtf_rating]
 *                                                   also saves a «Pending» review (moderation).
 * CF7's own styles and auto-paragraphs are off.
 *
 * Front-end behaviour (error states, loader, "thanks" popup): js/app-cf7.js.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VTF_Forms {

	public function __construct() {
		add_action( 'wpcf7_init', [ $this, 'register_tags' ] );
		add_filter( 'wpcf7_load_css', '__return_false' );
		add_filter( 'wpcf7_autop_or_not', '__return_false' );
		add_filter( 'wpcf7_validate_vtf_select*', [ $this, 'validate_required' ], 10, 2 );
		add_filter( 'wpcf7_validate_vtf_consent*', [ $this, 'validate_required' ], 10, 2 );
		add_filter( 'wpcf7_validate_vtf_rating*', [ $this, 'validate_rating' ], 10, 2 );
		add_filter( 'wpcf7_validate_vtf_product', [ $this, 'validate_product' ], 10, 2 );
		add_filter( 'wpcf7_posted_data', [ $this, 'product_name_for_mail' ] );
		add_action( 'wpcf7_before_send_mail', [ $this, 'save_review' ], 10, 3 );
	}

	public function register_tags(): void {
		wpcf7_add_form_tag( 'vtf_submit', [ $this, 'submit_tag' ] );
		wpcf7_add_form_tag( [ 'vtf_select', 'vtf_select*' ], [ $this, 'select_tag' ], [ 'name-attr' => true ] );
		wpcf7_add_form_tag( 'vtf_topics', [ $this, 'topics_tag' ], [ 'name-attr' => true ] );
		wpcf7_add_form_tag( [ 'vtf_consent', 'vtf_consent*' ], [ $this, 'consent_tag' ], [ 'name-attr' => true ] );
		wpcf7_add_form_tag( [ 'vtf_rating', 'vtf_rating*' ], [ $this, 'rating_tag' ], [ 'name-attr' => true ] );
		wpcf7_add_form_tag( 'vtf_product', [ $this, 'product_tag' ], [ 'name-attr' => true ] );
	}

	/**
	 * Product ID posted by [vtf_product] (0 when missing / not a published product).
	 */
	private function posted_product(): int {
		$id = isset( $_POST['product'] ) ? absint( wp_unslash( $_POST['product'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- CF7 checks its own nonce.

		return $id && 'vtf_product' === get_post_type( $id ) && 'publish' === get_post_status( $id ) ? $id : 0;
	}

	/**
	 * Rating must be 1–5.
	 *
	 * @param WPCF7_Validation $result Validation result.
	 * @param WPCF7_FormTag    $tag    Form tag.
	 */
	public function validate_rating( $result, $tag ) {
		$value = isset( $_POST[ $tag->name ] ) ? absint( wp_unslash( $_POST[ $tag->name ] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- CF7 checks its own nonce.

		if ( $value < 1 || $value > 5 ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}

		return $result;
	}

	/**
	 * A posted product must exist (no reviews for made-up IDs).
	 *
	 * @param WPCF7_Validation $result Validation result.
	 * @param WPCF7_FormTag    $tag    Form tag.
	 */
	public function validate_product( $result, $tag ) {
		if ( ! empty( $_POST[ $tag->name ] ) && ! $this->posted_product() ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- CF7 checks its own nonce.
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}

		return $result;
	}

	/**
	 * The mail shows the product's name and format instead of its ID.
	 *
	 * @param array $data Posted data.
	 */
	public function product_name_for_mail( $data ): array {
		$data = (array) $data;

		if ( isset( $data['product'] ) ) {
			$id = $this->posted_product();

			$data['product'] = $id ? get_the_title( $id ) . ' (' . vtf_product_format( vtf_product_data( $id ) ) . ')' : '';
		}

		return $data;
	}

	/**
	 * A review form (has [vtf_rating]) saves the review as «Pending» — an
	 * editor publishes it in Products → Reviews.
	 *
	 * @param WPCF7_ContactForm $form       Form.
	 * @param bool              $abort      Abort sending.
	 * @param WPCF7_Submission  $submission Submission.
	 */
	public function save_review( $form, &$abort, $submission ): void {
		if ( ! $form || empty( $form->scan_form_tags( [ 'type' => [ 'vtf_rating', 'vtf_rating*' ] ] ) ) ) {
			return;
		}

		$product = $this->posted_product();
		$rating  = isset( $_POST['rating'] ) ? absint( wp_unslash( $_POST['rating'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- CF7 checks its own nonce.

		if ( ! $product || $rating < 1 || $rating > 5 ) {
			return;
		}

		$name    = sanitize_text_field( (string) $submission->get_posted_data( 'your-name' ) );
		$email   = sanitize_email( (string) $submission->get_posted_data( 'your-email' ) );
		$message = sanitize_textarea_field( (string) $submission->get_posted_data( 'message' ) );

		$review = wp_insert_post(
			[
				'post_type'    => 'vtf_review',
				'post_status'  => 'pending',
				'post_title'   => '' !== $name ? $name : __( 'Anonymous', 'vitfit' ),
				'post_content' => $message,
			],
			true
		);

		if ( is_wp_error( $review ) ) {
			vtf_log( 'reviews', 'Review not saved: ' . $review->get_error_message(), [ 'product' => $product ] );

			return;
		}

		update_post_meta( $review, 'rating', $rating );
		update_post_meta( $review, '_rating', 'field_' . substr( sha1( 'group_vtf_review/rating' ), 0, 16 ) );
		update_post_meta( $review, 'product', $product );
		update_post_meta( $review, '_product', 'field_' . substr( sha1( 'group_vtf_review/product' ), 0, 16 ) );
		update_post_meta( $review, 'email', $email );
		update_post_meta( $review, '_email', 'field_' . substr( sha1( 'group_vtf_review/email' ), 0, 16 ) );
	}

	/**
	 * [vtf_rating* rating] → the stars of the review popup (5 → 1, CSS-only).
	 *
	 * @param WPCF7_FormTag $tag Form tag.
	 */
	public function rating_tag( $tag ): string {
		if ( empty( $tag->name ) ) {
			return '';
		}

		ob_start();
		?>
		<span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr( $tag->name ); ?>"><fieldset class="rate"<?php echo $tag->is_required() ? ' data-vtf-required' : ''; ?>>
			<legend class="field__label"><?php esc_html_e( 'Rating', 'vitfit' ); ?><?php echo $tag->is_required() ? ' *' : ''; ?></legend>
			<div class="rate__stars">
				<?php for ( $vtf_n = 5; $vtf_n >= 1; $vtf_n-- ) { ?>
					<label><input type="radio" name="<?php echo esc_attr( $tag->name ); ?>" value="<?php echo esc_attr( (string) $vtf_n ); ?>"><?php vtf_sprite( 'i-rate' ); ?><span class="visually-hidden"><?php echo esc_html( (string) $vtf_n ); ?></span></label>
				<?php } ?>
			</div>
		</fieldset></span>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * [vtf_product product] → hidden ID of the product on the current page.
	 *
	 * @param WPCF7_FormTag $tag Form tag.
	 */
	public function product_tag( $tag ): string {
		$id = vtf_current_product();

		if ( empty( $tag->name ) || ! $id ) {
			return '';
		}

		return sprintf( '<input type="hidden" name="%s" value="%d">', esc_attr( $tag->name ), $id );
	}

	/**
	 * Required theme tags: an empty value is an error.
	 *
	 * @param WPCF7_Validation $result Validation result.
	 * @param WPCF7_FormTag    $tag    Form tag.
	 */
	public function validate_required( $result, $tag ) {
		$value = isset( $_POST[ $tag->name ] ) ? trim( sanitize_text_field( wp_unslash( $_POST[ $tag->name ] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- CF7 checks its own nonce.

		if ( '' === $value ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}

		return $result;
	}

	/**
	 * [vtf_submit "Send"] → the design's button.
	 *
	 * @param WPCF7_FormTag $tag Form tag.
	 */
	public function submit_tag( $tag ): string {
		$label = ! empty( $tag->values[0] ) ? (string) $tag->values[0] : __( 'Send', 'vitfit' );

		ob_start();
		?>
		<button class="btn" type="submit"><span class="btn__label"><?php echo esc_html( $label ); ?></span><span class="btn__tile"><?php vtf_sprite( 'i-arrow' ); ?></span></button>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * [vtf_select* name "Placeholder" "Option" …] → span.field__select > select.
	 *
	 * @param WPCF7_FormTag $tag Form tag.
	 */
	public function select_tag( $tag ): string {
		if ( empty( $tag->name ) ) {
			return '';
		}

		$values      = array_values( (array) $tag->values );
		$placeholder = (string) array_shift( $values );
		$required    = $tag->is_required();

		ob_start();
		?>
		<span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr( $tag->name ); ?>"><span class="field__select"><select class="field__input<?php echo $required ? ' wpcf7-validates-as-required' : ''; ?>" name="<?php echo esc_attr( $tag->name ); ?>"<?php echo $required ? ' aria-required="true"' : ''; ?>>
			<option value="" disabled selected><?php echo esc_html( $placeholder ); ?></option>
			<?php foreach ( $values as $value ) { ?>
				<option><?php echo esc_html( (string) $value ); ?></option>
			<?php } ?>
		</select></span></span>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * [vtf_topics name "Question" "Review" …] → the radio pills, first checked.
	 *
	 * @param WPCF7_FormTag $tag Form tag.
	 */
	public function topics_tag( $tag ): string {
		if ( empty( $tag->name ) || empty( $tag->values ) ) {
			return '';
		}

		ob_start();
		?>
		<span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr( $tag->name ); ?>"><span class="form__topics" role="radiogroup" aria-label="<?php esc_attr_e( 'Topic', 'vitfit' ); ?>">
			<?php foreach ( array_values( (array) $tag->values ) as $n => $value ) { ?>
				<label><input type="radio" name="<?php echo esc_attr( $tag->name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>"<?php checked( 0, $n ); ?>><span><?php echo esc_html( (string) $value ); ?></span></label>
			<?php } ?>
		</span></span>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * [vtf_consent* agree] → the consent checkbox with the privacy policy link.
	 *
	 * @param WPCF7_FormTag $tag Form tag.
	 */
	public function consent_tag( $tag ): string {
		if ( empty( $tag->name ) ) {
			return '';
		}

		$privacy  = (string) get_privacy_policy_url();
		$required = $tag->is_required();
		$text     = '' !== $privacy
			/* translators: %s: privacy policy URL */
			? sprintf( __( 'I agree to the processing of personal data under the <a href="%s">privacy policy</a>', 'vitfit' ), esc_url( $privacy ) )
			: __( 'I agree to the processing of personal data', 'vitfit' );

		ob_start();
		?>
		<span class="wpcf7-form-control-wrap" data-name="<?php echo esc_attr( $tag->name ); ?>"><label class="check"><input type="checkbox" name="<?php echo esc_attr( $tag->name ); ?>" value="1"<?php echo $required ? ' class="wpcf7-validates-as-required" aria-required="true"' : ''; ?>><span class="check__box"><?php vtf_sprite( 'i-check' ); ?></span><span><?php echo wp_kses( $text, [ 'a' => [ 'href' => [] ] ] ); ?></span></label></span>
		<?php
		return (string) ob_get_clean();
	}
}

new VTF_Forms();

/**
 * Does the CF7 form exist?
 *
 * @param int $form_id CF7 form post ID.
 */
function vtf_has_form( int $form_id ): bool {
	return $form_id > 0 && function_exists( 'wpcf7_contact_form' ) && wpcf7_contact_form( $form_id );
}

/**
 * Renders a CF7 form with the markup's form classes.
 *
 * @param int    $form_id CF7 form post ID.
 * @param string $classes Classes for the <form>, e.g. 'modal__form'.
 */
function vtf_form( int $form_id, string $classes = '' ): void {
	if ( ! vtf_has_form( $form_id ) ) {
		return;
	}

	echo do_shortcode( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CF7 output.
		sprintf(
			'[contact-form-7 id="%d" html_class="%s"]',
			$form_id,
			esc_attr( trim( 'vtf-form form ' . $classes ) )
		)
	);
}
