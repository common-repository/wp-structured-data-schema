<?php
namespace WP_Tools\Schema\Schema;

/**
 * Breadcrumb.
 */
class Breadcrumb
{

    /**
     * @param $settings
     */
    public static function render(
        $settings,
        $container
    ) {
        if (isset($settings['breadcrumb'], $settings['breadcrumb']['enabled']) && $settings['breadcrumb']['enabled']) {
            require_once $container['plugin_dir'] . '/includes/lib/carbon-breadcrumbs/carbon-breadcrumbs.php';

            // add_filter(
            //     'carbon_breadcrumb_enable_admin',
            //     function ($enabled) {
            //         return false;
            //     }
            // );

            $trail = new \Carbon_Breadcrumb_Trail([
                'glue'              => ' >> ',
                'link_before'       => '<li>',
                'link_after'        => '</li>',
                'wrapper_before'    => '<ul>',
                'wrapper_after'     => '</ul>',
                'title_before'      => '',
                'title_after'       => '',
                'min_items'         => 2,
                'last_item_link'    => false,
                'display_home_item' => true,
                'home_item_title'   => __('Home', 'carbon_breadcrumbs'),
            ]);
            $trail->setup();
            $trail->sort_items();
            $items = $trail->get_flat_items();

            $breadcrumb_array = [];

            if (!empty($items)) {
                foreach ($items as $index => $item) {
                    $breadcrumb_list_item = [
                        '@type'    => 'ListItem',
                        'position' => $index + 1,
                        'name'     => $item->get_title(),
                        'item'     => $item->get_link(),
                    ];
                    if ($index == (count($items) - 1)) {
                        unset($breadcrumb_list_item['item']);
                    }
                    $breadcrumb_array[] = $breadcrumb_list_item;
                }
            }

            $output = new SchemaOutput();
            $output->setType('BreadcrumbList')
                ->set('@context', 'https://schema.org')
                ->set('@type', 'BreadcrumbList')
                ->set('itemListElement', $breadcrumb_array);

            $name = get_bloginfo('name');

            if ($name) {
                $output->set('name', $name);
            }

            $output->render();
        }
    }

}
