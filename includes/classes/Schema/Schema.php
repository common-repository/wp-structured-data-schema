<?php

namespace WP_Tools\Schema\Schema;

use WP_Tools\Schema\Schema\Integration;
/**
 * Schema.
 */
class Schema {
    /**
     * @var mixed
     */
    protected $container;

    /**
     * Constructor.
     */
    public function __construct( $container ) {
        $this->container = $container;
    }

    public function init() {
        $settings = $this->container['settings']->get_schema_settings();
        $load_in = 'head';
        if ( isset( $settings['load_in'] ) && $settings['load_in'] ) {
            $load_in = $settings['load_in'];
        }
        if ( $load_in == 'head' ) {
            add_action( 'wp_head', [$this, 'load_schema'] );
            add_action( 'wp_head', [$this, 'load_site_wide_schema'] );
        } else {
            add_action( 'wp_footer', [$this, 'load_schema'] );
            add_action( 'wp_footer', [$this, 'load_site_wide_schema'] );
        }
    }

    /**
     * Load schema associated with posts
     */
    public function load_schema() {
        if ( is_singular() ) {
            global $post;
            $settings = $this->container['settings']->get_schema_settings();
            $this->outputSchemaFromRenders( Article::getRenderers( $settings, $post ), 1 );
        }
    }

    /**
     * Output the schema with count constraints. -1 means no constraint.
     */
    public function outputSchemaFromRenders( $renderers, $count = -1 ) {
        foreach ( $renderers as $iter => $renderer ) {
            // this is will remove unwanted attributes from schema.
            // For example, customMarker from local business
            $renderer->removeIgnores();
            // count is without constraint or iterator is less than equal to count
            if ( $count == -1 || $iter + 1 <= $count ) {
                // phpcs:ignore
                echo $renderer->getJsonLd();
            }
        }
    }

    /**
     * Load site wide schema like organization , breadcrumbs etc
     */
    public function load_site_wide_schema() {
        $settings = $this->container['settings']->get_schema_settings();
        if ( Organization::canOutput( $settings ) ) {
            Organization::render( $settings );
            Person::render( $settings );
        }
        Breadcrumb::render( $settings, $this->container );
    }

    /**
     * Get the posts associated with the schema type.
     */
    public function getSchemaAssociatedPosts( $schemaKey ) {
        $settings = $this->container['settings']->get_schema_settings();
        $associatedPosts = [];
        if ( isset( $settings[$schemaKey], $settings[$schemaKey]['integrations'] ) && !empty( $settings[$schemaKey]['integrations'] ) ) {
            foreach ( $settings[$schemaKey]['integrations'] as $integrationData ) {
                $integration = new Integration($integrationData);
                $integrationPosts = $integration->getAssociatedPosts();
                if ( $integrationPosts ) {
                    foreach ( $integrationPosts as $post_id => $post_item ) {
                        $associatedPosts[$post_id] = $post_item;
                    }
                }
            }
        }
        return $associatedPosts;
    }

}
