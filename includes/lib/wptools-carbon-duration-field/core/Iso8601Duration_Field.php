<?php
namespace Carbon_Field_Iso8601Duration;

use Carbon_Fields\Field\Field;
use Carbon_Fields\Value_Set\Value_Set;

class Iso8601Duration_Field extends Field {

    /**
     * Create a field from a certain type with the specified label.
     *
     * @access public
     *
     * @param  string $type  Field type
     * @param  string $name  Field name
     * @param  string $label Field label
     * @return void
     */
    public function __construct(
        $type,
        $name,
        $label
    ) {
        $this->set_value_set(new Value_Set(Value_Set::TYPE_MULTIPLE_PROPERTIES, [
            'iso8601' => '',
            'years'   => 0,
            'months'  => 0,
            'days'    => 0,
            'hours'   => 0,
            'minutes' => 0,
            'seconds' => 0,
        ]));

        parent::__construct($type, $name, $label);
    }

    /**
     * {@inheritDoc}
     */
    public function set_value_from_input($input) {

        if (!isset($input[$this->get_name()])) {
            $this->set_value(null);
            return $this;
        }

        $value_set = [
            'iso8601' => '',
            'years'   => '',
            'months'  => '',
            'days'    => '',
            'hours'   => '',
            'minutes' => '',
            'seconds' => '',
        ];

        foreach ($value_set as $key => $v) {
            if (isset($input[$this->get_name()][$key])) {
                $value_set[$key] = $input[$this->get_name()][$key];
            }
        }

        $value_set[Value_Set::VALUE_PROPERTY] = $value_set['iso8601'];

        $this->set_value($value_set);
        return $this;
    }

    /**
     * Prepare the field type for use.
     * Called once per field type when activated.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function field_type_activated() {
        $dir    = \Carbon_Field_Iso8601Duration\DIR . '/languages/';
        $locale = get_locale();
        $path   = $dir . $locale . '.mo';
        load_textdomain('carbon-field-Iso8601Duration', $path);
    }

    /**
     * Enqueue scripts and styles in admin.
     * Called once per field type.
     *
     * @static
     * @access public
     *
     * @return void
     */
    public static function admin_enqueue_scripts() {
        $root_uri = \Carbon_Fields\Carbon_Fields::directory_to_url(\Carbon_Field_Iso8601Duration\DIR);

        // Enqueue field styles.
        wp_enqueue_style('carbon-field-Iso8601Duration', $root_uri . '/build/bundle.css');

        // Enqueue field scripts.
        wp_enqueue_script('carbon-field-Iso8601Duration', $root_uri . '/build/bundle.js', ['carbon-fields-core']);
    }
}
