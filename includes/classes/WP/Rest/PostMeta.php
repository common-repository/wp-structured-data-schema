<?php
namespace WP_Tools\Schema\WP\Rest;

/**
 * PostMeta.
 */
class PostMeta
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
     * Get list of unique post meta
     */
    public function get_post_meta_list($request)
    {
        global $wpdb;

        $params = $request->get_query_params();
        // if query is not set
        if (!isset($params['s'])) {
            return [];
        }

        $prepared_sql = $wpdb->prepare(
            "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
            '%' . $params['s'] . '%'
        );

        // phpcs:ignore
        $post_meta_list_results = $wpdb->get_results($prepared_sql, ARRAY_A);

        $post_meta_list = [];
        if ($post_meta_list_results && !empty($post_meta_list_results)) {
            foreach ($post_meta_list_results as $item) {
                if (isset($item['meta_key'])) {
                    $post_meta_list[] = $item['meta_key'];
                }
            }
        }

        return $post_meta_list;
    }

}
