<?php

if ( !function_exists( 'wpt_seo_schema_fs' ) ) {
    // Create a helper function for easy SDK access.
    function wpt_seo_schema_fs() {
        global $wpt_seo_schema_fs;
        if ( !isset( $wpt_seo_schema_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $wpt_seo_schema_fs = fs_dynamic_init( [
                'id'             => '6071',
                'slug'           => 'wp-structured-data-schema',
                'type'           => 'plugin',
                'public_key'     => 'pk_3fb335f08eee5205c48c6f97b2784',
                'is_premium'     => false,
                'premium_suffix' => 'Premium',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => [
                    'days'               => 7,
                    'is_require_payment' => true,
                ],
                'menu'           => [
                    'slug' => 'wpt_structured_data_settings',
                ],
                'is_live'        => true,
            ] );
        }
        return $wpt_seo_schema_fs;
    }

    // Init Freemius.
    wpt_seo_schema_fs();
    // Signal that SDK was initiated.
    do_action( 'wpt_seo_schema_fs_loaded' );
}