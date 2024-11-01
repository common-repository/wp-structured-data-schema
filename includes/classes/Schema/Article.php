<?php
namespace WP_Tools\Schema\Schema;

use Carbon_Fields\Container;

/**
 * Article.
 */
class Article
{

    /**
     * @param $container
     */
    public static function carbon_fields_register_fields($container)
    {
        $settings = $container['settings']->get_schema_settings();

        if (isset($settings['article'], $settings['article']['integrations']) && !empty($settings['article']['integrations'])) {
            $ignores = [];
            foreach ($settings['article']['integrations'] as $integrationData) {
                $fields = $container['custom_fields']->get_fields(
                    $integrationData,
                    $ignores,
                    $container['article_schema_definition']->get_fields()
                );

                if (!empty($fields)) {
                    $postMetaContainer = Container::make('post_meta', 'Article Schema - ' . $integrationData['label']);
                    $postMetaContainer->add_fields($fields);

                    $integration = new Integration($integrationData);

                    if ($integration->hasPostTypes()) {
                        $postMetaContainer->where('post_type', 'IN', $integration->getPostTypes());
                    }

                    if ($integration->hasPostsIncluded()) {
                        $postMetaContainer->or_where('post_id', 'IN', $integration->getPostsIncluded());
                    }

                    if ($integration->hasPostsExcluded()) {
                        $postMetaContainer->where('post_id', 'NOT IN', $integration->getPostsExcluded());
                    }

                }
            }
        }

    }

    /**
     * @param $settings
     * @param $type
     */
    public static function getRenderers(
        $settings,
        $post
    ) {
        $renderers = [];
        if (isset($settings['article'])) {

            if (isset($settings['article'], $settings['article']['integrations']) && !empty($settings['article']['integrations'])) {
                foreach ($settings['article']['integrations'] as $integrationData) {
                    $integration = new Integration($integrationData);
                    $renderer    = new Renderer\ArticleRenderer($integration, $post);

                    if ($renderer->doesPostContainsSchema($post)) {
                        $renderer->setupSchema();
                        $renderers[] = $renderer;
                    }
                }
            }
        }

        return $renderers;
    }

}
