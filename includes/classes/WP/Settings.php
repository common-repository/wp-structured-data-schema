<?php

namespace WP_Tools\Schema\WP;

class Settings {
    /**
     * @var mixed
     */
    protected $container;

    /**
     * @var mixed
     */
    protected $schema_settings = null;

    /**
     * @param $container
     */
    public function __construct( $container ) {
        $this->container = $container;
    }

    /**
     * Initialize
     */
    public function init() {
        add_action( 'admin_menu', [$this, 'admin_menu'] );
        add_action( 'admin_enqueue_scripts', function () {
            $css = 'li#toplevel_page_wpt_structured_data_settings img {width: 20px;}';
            wp_register_style( 'wpt-seo-plugin-admin-inline-css', false );
            wp_enqueue_style( 'wpt-seo-plugin-admin-inline-css' );
            wp_add_inline_style( 'wpt-seo-plugin-admin-inline-css', $css );
        } );
    }

    /**
     * Admin menu
     */
    public function admin_menu() {
        $page_hook_suffix = add_menu_page(
            __( 'SEO Schema', 'textdomain' ),
            __( 'SEO Schema', 'textdomain' ),
            'manage_options',
            'wpt_structured_data_settings',
            [$this, 'options_page_callback'],
            $this->container['plugin_url'] . '/images/menu-icon.png'
        );
        add_action( "admin_print_scripts-{$page_hook_suffix}", [$this, 'admin_print_scripts'] );
    }

    /**
     * Options page HTML.
     */
    public function options_page_callback() {
        echo '<div class="wrap wpt-seo-setting-wrap"><span id="wpt-seo-settings-loader" style="margin-left: calc(50% - 45px);">Loading...</span><h1>SEO Schema</h1><div id="wpt-structured-data-settings-app"></div></div>';
    }

    /**
     * Enqueue js file for settings page
     */
    public function admin_print_scripts() {
        $script_asset_path = $this->container['plugin_dir'] . "/settings/build/index.asset.php";
        $script_asset = (require $script_asset_path);
        $this->container['bootstrap']->set_freemius_license_javascript_var();
        wp_enqueue_media();
        wp_enqueue_script(
            'wpt-structured-data-settings-app-script',
            $this->container['plugin_url'] . '/settings/build/index.js',
            [
                'wp-api',
                'wp-i18n',
                'wp-components',
                'wp-element',
                'wp-api-fetch',
                'wp-blocks',
                'wp-editor'
            ],
            $script_asset['version'],
            true
        );
        $inline_css = '.wpt-seo-wrap {position: relative;} #wpt-seo-settings-loader {position: absolute;
    background: #0073aa;
    padding: 5px 15px;
    margin-left: calc(50% - 45px);
    color: white;}';
        wp_register_style( 'wpt-seo-settings-inline-css', false );
        wp_enqueue_style( 'wpt-seo-settings-inline-css' );
        wp_add_inline_style( 'wpt-seo-settings-inline-css', $inline_css );
    }

    public function get_schema_settings() {
        return get_option( 'wpt_structured_data_settings', $this->container['settings']->get_default_schema() );
    }

    /**
     * @return mixed
     */
    public function get_default_schema() {
        if ( is_null( $this->schema_settings ) ) {
            $this->schema_settings = [
                'load_schema_in' => 'head',
                'entity'         => [
                    'type'             => '',
                    'company_name'     => '',
                    'company_type'     => 'organization',
                    'person_name'      => '',
                    'logo'             => '',
                    'facebook'         => '',
                    'twitter'          => '',
                    'instagram'        => '',
                    'youtube'          => '',
                    'linkedin'         => '',
                    'pinterest'        => '',
                    'sound_cloud'      => '',
                    'tumbler'          => '',
                    'load_on_homepage' => true,
                ],
                'contact'        => [
                    'type'                      => '',
                    'telephone'                 => '',
                    'area_served'               => [],
                    'available_language'        => '',
                    'toll_free'                 => false,
                    'hearing_impared_supported' => false,
                ],
                'breadcrumb'     => [
                    'enabled' => false,
                ],
                'article'        => [
                    'integrations' => [],
                ],
            ];
        }
        return $this->schema_settings;
    }

}
