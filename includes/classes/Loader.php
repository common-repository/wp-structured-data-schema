<?php

namespace WP_Tools\Schema;

/**
 * Container - Dependencies
 */
class Loader extends \WPTools\Pimple\Container {
    /**
     * @var mixed
     */
    public static $container = null;

    /**
     * Contructor
     */
    public function __construct() {
        parent::__construct();
        $this->init();
    }

    /**
     * Get instance.
     */
    public static function get_instance() {
        if ( is_null( self::$container ) ) {
            self::$container = new Loader();
        }
        return self::$container;
    }

    /**
     * Initialize registration
     */
    public function init() {
        $this['bootstrap'] = function ( $container ) {
            return new WP\Bootstrap($container);
        };
        $this['wp'] = function ( $container ) {
            return new WP\WP($container);
        };
        $this['custom_fields'] = function ( $container ) {
            return new CustomFields\CustomFields($container);
        };
        $this['schema'] = function ( $container ) {
            return new Schema\Schema($container);
        };
        // wp settings.
        $this['settings'] = function ( $container ) {
            return new WP\Settings($container);
        };
        // Rest
        $this['settings_rest'] = function ( $container ) {
            return new WP\Rest\Settings($container);
        };
        $this['post_meta_rest'] = function ( $container ) {
            return new WP\Rest\PostMeta($container);
        };
        $this['rest'] = function ( $container ) {
            return new WP\Rest\Rest($container);
        };
        // post types
        $this['post_types'] = function ( $container ) {
            return new WP\PostTypes($container);
        };
        // shortcodes
        $this['breadcrumb_shortcode'] = function ( $container ) {
            return new Shortcodes\Breadcrumb($container);
        };
        // sitemap
        $this['sitemap'] = function ( $container ) {
            return new WP\Sitemap($container);
        };
        $this->init_schema_definitions();
    }

    /**
     * schema definitions
     */
    public function init_schema_definitions() {
        $this['schema_definitions'] = function ( $container ) {
            return new Schema\Definitions\Schema($container);
        };
        $this['sitemap_schema_definition'] = function ( $container ) {
            return new Schema\Definitions\Sitemap($container);
        };
        $this['article_schema_definition'] = function ( $container ) {
            return new Schema\Definitions\Article($container);
        };
        $this['entity_schema_definition'] = function ( $container ) {
            return new Schema\Definitions\Entity($container);
        };
        $this['contact_schema_definition'] = function ( $container ) {
            return new Schema\Definitions\Contact($container);
        };
        $this['faq_schema_definition'] = function ( $container ) {
            return new Schema\Definitions\FAQ($container);
        };
        $this['howto_schema_definition'] = function ( $container ) {
            return new Schema\Definitions\HowTo($container);
        };
        $this['specialAnnouncement_schema_definition'] = function ( $container ) {
            return new Schema\Definitions\SpecialAnnouncement($container);
        };
        $this['localBusiness_schema_definition'] = function ( $container ) {
            return new Schema\Definitions\LocalBusiness($container);
        };
    }

    /**
     * Bootstrap things
     */
    public function run() {
        $this['sitemap']->add_hooks();
        $this['custom_fields']->init();
        $this['settings']->init();
        $this['rest']->init();
        $this['schema']->init();
        $this['bootstrap']->add_shortcodes();
        add_action( 'admin_bar_menu', [$this['bootstrap'], 'admin_bar_menu'], 999 );
    }

}
