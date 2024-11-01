<?php
namespace WP_Tools\Schema\WP\Rest;

use WP_Query;

/**
 * SettingsRest.
 */
class Settings
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

    /**
     * Autocomplete API to get posts
     */
    public function autocomplete_get_posts($request)
    {

        $params = $request->get_query_params();
        // if query is not set
        if (!isset($params['s']) || !isset($params['post_types'])) {
            return [];
        }

        add_filter(
            'posts_where',
            function (
                $where,
                $wp_query
            ) use ($params) {
                global $wpdb;

                $search_term = $wpdb->esc_like($params['s']);
                $search_term = ' \'%' . $search_term . '%\'';
                $where       = $where . ' AND post_title like ' . $search_term;

                return $where;
            },
            10,
            2
        );

        $query = new WP_Query([
            'posts_per_page' => '10',
            'post_status'    => 'publish',
            'post_type'      => explode(',', $params['post_types']),
        ]);

        $response = [];
        foreach ($query->posts as $p) {
            $response[] = [
                'id'    => $p->ID,
                'title' => $p->post_title,
            ];
        }

        return ['posts' => $response];
    }

    /**
     * Save Settings
     */
    public function save_settings($request)
    {
        $post = $request->get_json_params();
        if (!isset($post['schema'])) {
            return ['success' => false];
        }
        $schema = $post['schema'];

        update_option('wpt_structured_data_settings', $schema, true);

        return ['success' => true];
    }

    /**
     * Rest API - GetSettings
     */
    public function get_settings($request)
    {
        $schema      = get_option('wpt_structured_data_settings', $this->container['settings']->get_default_schema());
        $post_types  = $this->container['post_types']->get_public_post_types();
        $taxonomies  = $this->container['post_types']->get_taxonomies();
        $definitions = [];

        if (!isset($schema['load_schema_in'])) {
            $schema['load_schema_in'] = 'head';
        }

        // breadcrumb
        $schema['breadcrumb']['changed'] = false;

        if (!isset($schema['breadcrumb']['taxonomies'])) {
            $schema['breadcrumb']['taxonomies'] = [];
        }

        foreach ($post_types as $index => $post_type) {

            if (!isset($taxonomies[$post_type['name']], $schema['breadcrumb']['taxonomies'][$post_type['name']])) {
                $schema['breadcrumb']['taxonomies'][$post_type['name']] = '';
            }
        }

        if (!isset($schema['entity'])) {
            $schema['entity'] = $this->container['entity_schema_definition']->get();
        }

        if (!isset($schema['contact'])) {
            $schema['contact'] = $this->container['contact_schema_definition']->get();
        }

        $schema['sitemap'] = $this->container['sitemap']->get_settings();

        //articles
        if (!isset($schema['article'], $schema['article']['integrations'])) {
            $schema['article']['integrations'] = [];
        }
        $schema['article']['integrations'] = $this->container['article_schema_definition']->syncIntegrations($schema['article']['integrations']);
        $definitions['article']            = $this->container['article_schema_definition']->get();

        //faq
        if (!isset($schema['faq'], $schema['faq']['integrations'])) {
            $schema['faq']['integrations'] = [];
        }
        $definitions['faq'] = $this->container['faq_schema_definition']->get();

        //howto
        if (!isset($schema['howto'], $schema['howto']['integrations'])) {
            $schema['howto']['integrations'] = [];
        }

        $schema['howto']['integrations'] = $this->container['howto_schema_definition']->syncIntegrations($schema['howto']['integrations']);
        $definitions['howto']            = $this->container['howto_schema_definition']->get();

        //localBusiness
        if (!isset($schema['localBusiness'], $schema['localBusiness']['integrations'])) {
            $schema['localBusiness']['integrations'] = [];
        }

        $schema['localBusiness']['integrations'] = $this->container['localBusiness_schema_definition']->syncIntegrations($schema['localBusiness']['integrations']);
        $definitions['localBusiness']            = $this->container['localBusiness_schema_definition']->get();

        //specialAnnouncement
        if (!isset($schema['specialAnnouncement'], $schema['specialAnnouncement']['integrations'])) {
            $schema['specialAnnouncement']['integrations'] = [];
        }

        $schema['specialAnnouncement']['integrations'] = $this->container['specialAnnouncement_schema_definition']->syncIntegrations($schema['specialAnnouncement']['integrations']);
        $definitions['specialAnnouncement']            = $this->container['specialAnnouncement_schema_definition']->get();

        return [
            'schema'            => $schema,
            'post_types'        => $post_types,
            'taxonomies'        => $taxonomies,
            'unique_taxonomies' => $this->container['post_types']->get_unique_taxonomies(),
            'definitions'       => $definitions,
            'fieldTypes'        => $this->container['schema_definitions']->get_types(),
        ];
    }

}
