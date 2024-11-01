<?php
namespace WP_Tools\Schema\Shortcodes;

/**
 * Breadcrumb.
 */
class Breadcrumb
{
    protected $container;

    /**
     * Constructor.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function render($attrs)
    {
        $attrs = shortcode_atts($this->default_shortcode_atts(), $attrs);

        $attrs['glue'] = sprintf('<li class="wpt_breadcrumb_separator">%s</li>', trim($attrs['glue']));
        require_once $this->container['plugin_dir'] . '/includes/lib/carbon-breadcrumbs/carbon-breadcrumbs.php';

        $trail = new \Carbon_Breadcrumb_Trail($attrs);
        $trail->setup(); // setup the trail by generating necessary breadcrumb items

        if ($attrs['dummy_data']) {
            $trail->add_custom_item("Level 1", "javascript:void(0);");
            $trail->add_custom_item("Level 2", "javascript:void(0);");
            $trail->add_custom_item("Level 3", "javascript:void(0);");
        }

        $trail->sort_items();
        $this->enqueue_assets($attrs['css_id']);

        $markup = sprintf('<nav aria-label="Breadcrumb" class="wpt_schema_breadcrumbs %s" id="%s"><ol>%s</ol><nav>', $attrs['css_classes'], $attrs['css_id'], $trail->render(true));

        return $markup;
    }

    public function default_shortcode_atts()
    {
        return [
            'dummy_data'        => false,
            'css_id'            => 'wpt_schema_breadcrumbs',
            'css_classes'       => '',
            'glue'              => '&gt;',
            'link_before'       => '<li>',
            'link_after'        => '</li>',
            'wrapper_before'    => '',
            'wrapper_after'     => '',
            'title_before'      => '',
            'title_after'       => '',
            'min_items'         => 2,
            'last_item_link'    => false,
            'display_home_item' => true,
            'home_item_title'   => __('Home', 'carbon_breadcrumbs'),
        ];
    }

    public function enqueue_assets($css_id = "wpt_schema_breadcrumbs")
    {
        wp_register_style('wpt-schema-breadcrumbs-style-placeholder', false);
        wp_enqueue_style('wpt-schema-breadcrumbs-style-placeholder');

        wp_enqueue_style('wpt_schema_breadcrumbs', $this->container['plugin_url'] . '/css/breadcrumbs-shortcode/breadcrumbs.css', ['wpt-schema-breadcrumbs-style-placeholder']);

        $inline_css = sprintf('nav#%s {max-width: none;}', $css_id);
        wp_add_inline_style('wpt_schema_breadcrumbs', $inline_css);
    }

}
