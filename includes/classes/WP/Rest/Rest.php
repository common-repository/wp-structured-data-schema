<?php
namespace WP_Tools\Schema\WP\Rest;

/**
 * Rest.
 */
class Rest
{
    /**
     * @var mixed
     */
    protected $container;

    /**
     * Constructor.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function init()
    {
        add_action('rest_api_init', [$this, 'rest_api_init']);
    }

    /**
     * Rest API init
     */
    public function rest_api_init()
    {
        register_rest_route(
            'wptools-seo-schema/v1',
            '/get_settings',
            [
                'methods'             => 'GET',
                'callback'            => [$this->container['settings_rest'], 'get_settings'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
            ]
        );

        register_rest_route(
            'wptools-seo-schema/v1',
            '/save_settings',
            [
                'methods'             => 'POST',
                'callback'            => [$this->container['settings_rest'], 'save_settings'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
            ]
        );

        // get posts
        register_rest_route(
            'wptools-seo-schema/v1',
            '/get_posts',
            [
                'methods'             => 'GET',
                'callback'            => [$this->container['settings_rest'], 'autocomplete_get_posts'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
            ]
        );

        register_rest_route(
            'wptools-seo-schema/v1',
            '/get_post_meta',
            [
                'methods'             => 'GET',
                'callback'            => [$this->container['post_meta_rest'], 'get_post_meta_list'],
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
            ]
        );

    }

}
