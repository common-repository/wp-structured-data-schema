<?php
namespace WP_Tools\Schema\WP;

/**
 * PostTypes.
 */
class PostTypes
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
     * Get the available post type definitions.
     */
    public function get_public_post_types()
    {

        $post_types         = [];
        $builtin_post_types = get_post_types(['public' => true, '_builtin' => true], 'objects');
        unset($builtin_post_types['attachment']);

        if (!empty($builtin_post_types)) {
            $post_types = array_merge($post_types, $builtin_post_types);
        }

        $custom_post_types = get_post_types(['_builtin' => false, 'public' => true], 'objects');
        if (!empty($custom_post_types)) {
            $post_types = array_merge($post_types, $custom_post_types);
        }

        $response = [];

        foreach ($post_types as $post_type) {
            $item = [
                'label' => ucwords($post_type->label),
                'name'  => $post_type->name,
            ];

            $response[] = $item;
        }

        return $response;
    }

    public function get_taxonomies()
    {
        $post_types = $this->get_public_post_types();

        foreach ($post_types as $post_type) {
            $taxonomies[$post_type['name']][] = [
                'label' => 'None',
                'value' => '',
            ];
            $postTypeTaxonomies = get_taxonomies([
                'public'      => true,
                'object_type' => [$post_type['name']],
            ], 'objects');

            if (isset($postTypeTaxonomies['post_format'])) {
                unset($postTypeTaxonomies['post_format']);
            }

            if (isset($postTypeTaxonomies['post_tag'])) {
                unset($postTypeTaxonomies['post_tag']);
            }

            foreach ($postTypeTaxonomies as $taxonomy) {
                $taxonomies[$post_type['name']][] = [
                    'label' => ucwords($taxonomy->label),
                    'value' => $taxonomy->name,
                ];
            }
        }

        return $taxonomies;
    }

    /**
     * Get unique taxonomies
     */
    public function get_unique_taxonomies()
    {
        $posts_taxonomies = $this->get_taxonomies();

        $taxonomies = [];
        foreach ($posts_taxonomies as $post_type => $post_taxonomies) {
            foreach ($post_taxonomies as $item) {
                if ($item['value']) {
                    $taxonomies[] = $item;
                }
            }
        }
        return $taxonomies;
    }

}
