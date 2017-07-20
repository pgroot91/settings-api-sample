<?php

namespace wordcamp\settings;

/**
 * Creates the options page under Settings in the dashboard
 */
add_action( 'admin_menu', function(){
    add_options_page( 'wordcamp demo', 'WordCamp Demo', 'manage_options', 'wordcamp-settings', 'wordcamp\settings\output_options_page' );
});

/**
 * Render the options page
 */
function output_options_page() { ?>
    <div class="wrap">
        
        <h2><?php _e( 'WordCamp Options Page', 'wordcamp' ); ?></h2>
        
        <?php
        // Determine which tab is active
        $active_tab = 'wordcamp-general-tab';
        if( isset( $_GET['tab'] ) ) :
            $active_tab = $_GET['tab'];
        endif;
        
        ?>
        
        <!-- Output the tabs -->
        <h2 class="nav-tab-wrapper">
            <a href="<?php echo esc_url( admin_url( 'options-general.php?page=wordcamp-settings&tab=wordcamp-general-tab' ) ); ?>" 
               class="nav-tab <?php echo $active_tab == 'wordcamp-general-tab' ? 'nav-tab-active' : ''; ?> "><?php _e('General', 'wordcamp'); ?>
            </a>
            <a href="<?php echo esc_url( admin_url( 'options-general.php?page=wordcamp-settings&tab=wordcamp-appearance-tab' ) ); ?>" 
               class="nav-tab <?php echo $active_tab == 'wordcamp-appearance-tab' ? 'nav-tab-active' : ''; ?> "><?php _e('Appearance', 'wordcamp'); ?>
            </a>
        </h2>
        
        <!-- Output the submission form -->
        <form action="options.php" method="POST">
            <?php settings_fields( $active_tab ); ?>
            <?php do_settings_sections( $active_tab ); ?>
            <?php submit_button(); ?>
        </form>
        
    </div>
    <?php
}

/**
 * Register Settings
 * Each setting registered here, has a corresponding Settings Field
 */
function register_settings() {
    
    
    register_setting( 'wordcamp-general-tab', 'wordcamp-api-key', array(
        'type'                  => 'string',
        'sanitize_callback'     => 'sanitize_text_field'
    ) );
    
    register_setting( 'wordcamp-general-tab', 'wordcamp-api-url', array(
        'type'                  => 'string',
        'sanitize_callback'     => 'esc_url_raw'
    ) );
    
    register_setting( 'wordcamp-appearance-tab', 'wordcamp-column-count', array(
        'type'                  => 'integer',
        'sanitize_callback'     => 'intval',
        'default'               => 6
    ) );
    
    register_setting( 'wordcamp-appearance-tab', 'wordcamp-color', array(
        'type'                  => 'string',
        'sanitize_callback'     => 'sanitize_hex_color',
        'default'               => '#cc0000'
    ) );
    
}
add_action( 'init', 'wordcamp\settings\register_settings' );


/**
 * Output Settings Fields
 */
function add_settings_fields() {
    
    add_settings_field(
        'wordcamp-api-key',
        __( 'API Key', 'wordcamp' ),
        'wordcamp\settings\render_text_field',
        'wordcamp-general-tab',
        'api-settings',
        array(
            'name'              => 'wordcamp-api-key',
            'value'             => get_option( 'wordcamp-api-key' ),
            'attrs'             => array( 
                'class'         => 'regular-text', 
                'placeholder'   => 'Enter the API key'
            ),
        )
    );
    
    add_settings_field(
        'wordcamp-api-url',
        __( 'API URL', 'wordcamp' ),
        'wordcamp\settings\render_text_field',
        'wordcamp-general-tab',
        'api-settings',
        array(
            'name'              => 'wordcamp-api-url',
            'value'             => get_option( 'wordcamp-api-url' ),
            'attrs'             => array( 
                'class'         => 'regular-text', 
                'placeholder'   => 'Enter the site URL'
            ),
        )
    );
    
    add_settings_field(
        'wordcamp-column-count',
        __( 'Columns', 'wordcamp' ),
        'wordcamp\settings\render_select_field',
        'wordcamp-appearance-tab',
        'layout',
        array(
            'name'              => 'wordcamp-column-count',
            'selected'          => get_option( 'wordcamp-column-count' ),
            'options'           => columns(),
            'attrs'             => array( 
                'class'         => 'regular-text',
            ),
        )
    );
    
    add_settings_field(
        'wordcamp-color',
        __( 'Main color', 'wordcamp' ),
        'wordcamp\settings\render_text_field',
        'wordcamp-appearance-tab',
        'colors',
        array(
            'name'              => 'wordcamp-color',
            'value'             => get_option( 'wordcamp-color' ),
            'attrs'             => array(
                'class'         => 'color-picker'
            )
        )
    );
    
    
}
add_action( 'admin_init', 'wordcamp\settings\add_settings_fields' );


/**
 * Create Settings Sections
 */
function create_settings_sections() {
    add_settings_section( 'api-settings', __( 'API Settings', 'wordcamp' ), '', 'wordcamp-general-tab' );
    add_settings_section( 'layout', __( 'Layout', 'wordcamp' ), '', 'wordcamp-appearance-tab' );
    add_settings_section( 'colors', __( 'Colors', 'wordcamp' ), '', 'wordcamp-appearance-tab' );
    
}
add_action( 'admin_init', 'wordcamp\settings\create_settings_sections' );


function render_text_field( $args ) { ?>
    
    <input type="text" 
           name="<?php echo esc_attr( $args['name'] ) ?>" 
           value="<?php echo esc_attr( get_option( $args['name'] ) ) ?>" 
           class="<?php echo isset( $args['attrs']['class'] ) ? $args['attrs']['class'] : ''; ?>"
           placeholder="<?php echo isset( $args['attrs']['placeholder'] ) ? $args['attrs']['placeholder'] : ''; ?>"/>
    
<?php }

function render_select_field( $args ) { ?>
    
    <select name="<?php echo esc_attr( $args['name'] ) ?>">
        
        <?php foreach( $args['options'] as $key => $val ) : ?>
        <option <?php echo $args['selected'] == $key ? 'selected=selected' : ''; ?> value="<?php echo esc_attr( $key ); ?>"><?php _e( $val, 'wordcamp' ); ?></option>
        <?php endforeach; ?>
        
    </select>
    
<?php } 

function sanitize_columns( $val ) {
    
    if( ! array_key_exists( $val, columns() ) ) :
        $val = get_option( 'wordcamp-column-count' );
    endif;
    
    return $val;
    
}

function columns() {
    return array(
        12        => __( 'Stacked', 'wordcamp' ),
        6         => __( '2-Column', 'wordcamp' ),
        3         => __( '4-Column', 'wordcamp' ),
    );
}
