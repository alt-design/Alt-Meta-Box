<?php
/**
 * includes/custom-metabox.php
 *
 * Functions for displaying & processing the custom meta box content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Create custom meta box
 */
function alt_meta_box_add(){
	add_meta_box( 'alt-meta-box', __( 'Alt Meta Box', 'alt' ), 'alt_meta_box_display', 'post', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'alt_meta_box_add' );

/**
 * Display custom meta box
 */
function alt_meta_box_display( $post ){ ?>

	<div class="alt-meta-box">

		<div class="row row-input">
			<label for="alt_text_input" class="label"><?php _e( 'Text Input', 'alt' ); ?></label>
			<input type="text" name="alt_text_input" id="alt_text_input" value="<?php echo esc_attr( get_post_meta( $post->ID, 'alt_text_input', true ) ); ?>" class="wide" />
		</div>

		<div class="row row-textarea">
			<label for="alt_textarea" class="label"><?php _e( 'Textarea', 'alt' ); ?></label>
			<textarea name="alt_textarea" id="alt_textarea" class="wide"><?php echo esc_textarea( get_post_meta( $post->ID, 'alt_textarea', true ) ); ?></textarea>
			<span class="description">Lorem ipsum dolor sit amet consectetur adipiscing</span>
		</div>

		<div class="row row-select">
			<label for="alt_select" class="label"><?php _e( 'Select', 'alt' ); ?></label>
			<select name="alt_select" id="alt_select" class="wide">
				<option value="">Please select...</option>
				<?php
					$user_args = array (
						'role__in' => array( 'Administrator', 'Editor' ),
						'number'   => -1
					);
					$user_query = new WP_User_Query( $user_args );
					$users = $user_query->get_results();
				?>
				<?php if ( ! empty( $users ) ): foreach ( $users as $user ): ?>
					<option value="<?php echo $user->ID; ?>"<?php if( $user->ID == get_post_meta( get_the_id(), 'alt_select', true ) ) echo ' selected="selected"'; ?>><?php echo esc_attr($user->display_name . ' (' . $user->user_email . ')'); ?></option>
				<?php endforeach; endif; ?>
			</select>
		</div>

		<div class="row row-checkboxes">
			<span class="label"><?php _e( 'Checkboxes', 'alt' ); ?></span>
			<div class="clearfix">
				<label for="alt_checkbox_apples">
					<input type="checkbox" name="alt_checkbox_apples" id="alt_checkbox_apples" value="1" <?php checked( get_post_meta( $post->ID, 'alt_checkbox_apples', true ), 1 ); ?> />
					<?php _e( 'Apples', 'alt' ); ?>
				</label>
				<label for="alt_checkbox_oranges">
					<input type="checkbox" name="alt_checkbox_oranges" id="alt_checkbox_oranges" value="1" <?php checked( get_post_meta( $post->ID, 'alt_checkbox_oranges', true ), 1 ); ?> />
					<?php _e( 'Oranges', 'alt' ); ?>
				</label>
				<label for="alt_checkbox_pears">
					<input type="checkbox" name="alt_checkbox_pears" id="alt_checkbox_pears" value="1" <?php checked( get_post_meta( $post->ID, 'alt_checkbox_pears', true ), 1 ); ?> />
					<?php _e( 'Pears', 'alt' ); ?>
				</label>
				<label for="alt_checkbox_lemons">
					<input type="checkbox" name="alt_checkbox_lemons" id="alt_checkbox_lemons" value="1" <?php checked( get_post_meta( $post->ID, 'alt_checkbox_lemons', true ), 1 ); ?> />
					<?php _e( 'Lemons', 'alt' ); ?>
				</label>
				<label for="alt_checkbox_peach">
					<input type="checkbox" name="alt_checkbox_peach" id="alt_checkbox_peach" value="1" <?php checked( get_post_meta( $post->ID, 'alt_checkbox_peach', true ), 1 ); ?> />
					<?php _e( 'Peach', 'alt' ); ?>
				</label>
			</div>
		</div>

		<div class="row row-repeater">
			<span class="label"><?php _e( 'Repeater', 'alt' ); ?></span>
			<ul>
				<li class="repeater-item sentinal">
					<span class="tab drag"></span>
					<span class="fields">
						<input type="text" name="alt_repeater_sentinal" class="repeater" />
					</span>
					<span class="tab delete"></span>
				</li>
				<?php
					$repeater_rows = maybe_unserialize( get_post_meta( $post->ID, 'alt_repeater', true ) );
					$count = count( $repeater_rows );

					if( is_array( $repeater_rows ) && $count > 0 ):
						foreach( $repeater_rows as $key => $row ):
				?>
					<li class="repeater-item visible" data-order="<?php echo $key; ?>">
						<span class="tab drag"></span>
						<span class="fields">

							<input type="text" name="alt_repeater_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $row ); ?>" />

						</span>
						<span class="tab delete"></span>
					</li>
				<?php
						endforeach;
					endif;
				?>
			</ul>
			<button class="button button-primary add-new-repeater-row">Add New</button>
		</div>

		<?php wp_nonce_field( 'alt_meta_box', 'alt_nonce_field' ); ?>

	</div>

<?php }

/**
 * Save custom meta box details
 */
function alt_meta_box_save( $post_id, $post ){

	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if( !isset( $_POST['alt_nonce_field'] ) || ! wp_verify_nonce( $_POST['alt_nonce_field'], 'alt_meta_box' ) )
		return;

	if( ! current_user_can( 'edit_post', $post->ID ) )
		return;

	if ( 'post' != $post->post_type )
		return;

	/**
	 * Get values of our repeater
	 */
	$repeater_values = array();

	foreach( $_POST as $key => $value ) {
		if ( strpos( $key, 'alt_repeater_' ) === 0 && $value != '' ) {
			$repeater_values[] = sanitize_text_field( $value );
		}
	}

	/**
	 * Update our checkboxes
	 */
	$checkboxes = array( 'alt_checkbox_apples', 'alt_checkbox_oranges', 'alt_checkbox_pears', 'alt_checkbox_lemons', 'alt_checkbox_peach' );

	foreach( $checkboxes as $checkbox ) {
		if( isset( $_POST[$checkbox] ) ){
			update_post_meta( $post_id, $checkbox, sanitize_text_field( $_POST[$checkbox] ) );
		}else{
			delete_post_meta( $post_id, $checkbox );
		}
	}

	update_post_meta( $post_id, 'alt_text_input', sanitize_text_field( $_POST['alt_text_input'] ) );
	update_post_meta( $post_id, 'alt_textarea', sanitize_text_field( $_POST['alt_textarea'] ) );
	update_post_meta( $post_id, 'alt_select', sanitize_text_field( $_POST['alt_select'] ) );
	update_post_meta( $post_id, 'alt_repeater', serialize( $repeater_values ) );

}
add_action( 'save_post', 'alt_meta_box_save', 10, 2 );