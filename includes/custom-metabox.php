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
				<?php
					$checkboxes = array(
						'Apples',
						'Oranges',
						'Pears',
						'Bananas',
					);
					$saved_rows = maybe_unserialize( get_post_meta( $post->ID, 'alt_checkboxes', true ) );
					foreach( $checkboxes as $key => $checkbox ):
				?>
					<label for="alt_checkbox_<?php echo $key; ?>">
						<input type="checkbox" name="alt_checkbox_<?php echo $key; ?>" id="alt_checkbox_<?php echo $key; ?>" value="1" <?php checked( $saved_rows[ 'alt_checkbox_' . $key ], 1 ); ?> />
						<?php echo $checkbox; ?>
					</label>
				<?php
					endforeach;
				?>
			</div>
		</div>

		<div class="row row-repeater">
			<span class="label"><?php _e( 'Repeater', 'alt' ); ?></span>
			<ul>
				<?php
					$repeater_name = 'alt_repeater';
					$label_value = 'Repeater Item';

					$sentinal_row = array( 'sentinal' => 'sentinal' );
					$repeater_rows = array_merge( $sentinal_row, maybe_unserialize( get_post_meta( $post->ID, $repeater_name, true ) ) );

					foreach( $repeater_rows as $key => $row ):
						if ( $key === 'sentinal' ) {
							$classes = 'repeater-item sentinal';
							$data_order = $input_id = $input_name = $input_value = '';
						} else {
							$classes = 'repeater-item visible';
							$data_order = 'data-order="' . $key . '"';
							$input_id = $repeater_name . '_' . $key;
							$input_name = 'name="' . $repeater_name . '_' . $key . '"';
							$input_value = 'value="' . $row . '"';
						}
				?>
					<li class="<?php echo $classes; ?>" <?php echo $data_order; ?>>
						<span class="tab drag"></span>
						<span class="fields">

							<label for="<?php echo $input_id; ?>" class="label">
								<?php echo $label_value; ?>
							</label>
							<input type="text" id="<?php echo $input_id; ?>" <?php echo $input_name; ?> <?php echo $input_value; ?> />
		
						</span>
						<span class="tab delete"></span>
					</li>
				<?php
					endforeach;
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
	 * Update our checkboxes
	 */
	$checkboxes_values = array();
	foreach( $_POST as $key => $value ) {
		if ( strpos( $key, 'alt_checkbox_' ) === 0 ) {
			$checkboxes_values[$key] = sanitize_text_field( $value );
		}
	}

	/**
	 * Get values of our repeater
	 */
	$repeater_values = array();
	foreach( $_POST as $key => $value ) {
		if ( strpos( $key, 'alt_repeater_' ) === 0 ) {
			$repeater_values[] = sanitize_text_field( $value );
		}
	}
	update_post_meta( $post_id, 'alt_text_input', sanitize_text_field( $_POST['alt_text_input'] ) );
	update_post_meta( $post_id, 'alt_textarea', sanitize_text_field( $_POST['alt_textarea'] ) );
	update_post_meta( $post_id, 'alt_select', sanitize_text_field( $_POST['alt_select'] ) );
	update_post_meta( $post_id, 'alt_checkboxes', serialize( $checkboxes_values ) );
	update_post_meta( $post_id, 'alt_repeater', serialize( $repeater_values ) );

}
add_action( 'save_post', 'alt_meta_box_save', 10, 2 );