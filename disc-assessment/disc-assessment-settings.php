<?php
// Settings page
function disc_assessment_settings_page() {
    ?>
    <div class="wrap">
        <h1>DiSC Assessment Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'disc_assessment_options' ); ?>
            <?php do_settings_sections( 'disc_assessment' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register settings
function disc_assessment_register_settings() {
    register_setting( 'disc_assessment_options', 'disc_assessment_options', 'disc_assessment_validate_options' );
    add_settings_section( 'disc_assessment_general_section', 'General Settings', 'disc_assessment_general_section_callback', 'disc_assessment' );
    add_settings_field( 'disc_assessment_email_field', 'Email Address', 'disc_assessment_email_field_callback', 'disc_assessment', 'disc_assessment_general_section' );
}

// Validate settings
function disc_assessment_validate_options( $input ) {
    // Validate email address
    $input['disc_assessment_email'] = sanitize_email( $input['disc_assessment_email'] );
    return $input;
}

// General settings section callback
function disc_assessment_general_section_callback() {
    ?>
    <p>General settings for the DiSC Assessment plugin.</p>
    <?php
}

// Email field callback
function disc_assessment_email_field_callback() {
    $options = get_option( 'disc_assessment_options' );
    ?>
    <input type="text" name="disc_assessment_options[disc_assessment_email]" value="<?php echo esc_attr( $options['disc_assessment_email'] ); ?>" />
    <?php
}

// Add settings page to menu
add_action( 'admin_menu', 'disc_assessment_add_settings_page' );
function disc_assessment_add_settings_page() {
    add_options_page( 'DiSC Assessment Settings', 'DiSC Assessment', 'manage_options', 'disc_assessment', 'disc_assessment_settings_page' );
}

// Register settings
add_action( 'admin_init', 'disc_assessment_register_settings' );
