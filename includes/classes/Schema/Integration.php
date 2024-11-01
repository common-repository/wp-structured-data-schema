<?php
namespace WP_Tools\Schema\Schema;

/**
 * Integration.
 */
class Integration
{
    /**
     * @var mixed
     */
    protected $data;
    /**
     * @var mixed
     */
    protected $post_types;
    /**
     * @var mixed
     */
    protected $posts_included;
    /**
     * @var mixed
     */
    protected $posts_excluded;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data           = $data;
        $this->post_types     = [];
        $this->posts_included = [];
        $this->posts_excluded = [];

        // post types

        if (isset($this->data['post_types_included']) && !empty($this->data['post_types_included'])) {

            foreach ($this->data['post_types_included'] as $post_type) {
                $this->post_types[] = $post_type['name'];
            }

        }

        if (isset($this->data['posts_included']) && !empty($this->data['posts_included'])) {
            foreach ($this->data['posts_included'] as $item) {
                $this->posts_included[] = $item['id'];
            }

        }
        if (isset($this->data['posts_excluded']) && !empty($this->data['posts_excluded'])) {
            foreach ($this->data['posts_excluded'] as $item) {
                $this->posts_excluded[] = $item['id'];
            }

        }
    }

    /**
     * Check if post types are there
     */
    public function hasPostTypes()
    {
        return count($this->post_types);
    }

    /**
     * Get post types
     */
    public function getPostTypes()
    {
        return $this->post_types;
    }

    /**
     * Check if posts included are there
     */
    public function hasPostsIncluded()
    {
        return count($this->posts_included);
    }

    /**
     * Get posts included.
     */
    public function getPostsIncluded()
    {
        return $this->posts_included;
    }

    /**
     * Check if posts excluded are there
     */
    public function hasPostsExcluded()
    {
        return count($this->posts_excluded);
    }

    /**
     * Get posts excluded.
     */
    public function getPostsExcluded()
    {
        return $this->posts_excluded;
    }

    public function hasFields()
    {
        return isset($this->data['fields']) && !empty($this->data['fields']);
    }

    /**
     * Get the integration fields.
     */
    public function getFields()
    {
        return isset($this->data['fields']) ? $this->data['fields'] : [];
    }

    public function getAssociatedPosts()
    {
        $posts = [];

        $excludes = $this->posts_excluded;
        $includes = $this->posts_included;

        if (!empty($this->post_types)) {
            $all_posts = get_posts([
                'numberposts' => -1,
                'post_type'   => $this->post_types,
                'post_status' => 'publish',
            ]);

            if ($all_posts) {
                foreach ($all_posts as $post_item) {
                    if (in_array($post_item->ID, $excludes)) {
                        continue;
                    }
                    $posts[$post_item->ID] = $post_item;
                }
            }
        }

        if (!empty($includes)) {

            $included_posts = get_posts([
                'post__in'  => $includes,
                'post_type' => 'any',
            ]);

            if ($included_posts) {
                foreach ($included_posts as $post_item) {
                    if (in_array($post_item->ID, $excludes)) {
                        continue;
                    }
                    $posts[$post_item->ID] = $post_item;
                }
            }

        }

        return $posts;
    }

}
