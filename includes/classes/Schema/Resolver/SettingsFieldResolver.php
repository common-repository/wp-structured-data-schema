<?php
namespace WP_Tools\Schema\Schema\Resolver;

/**
 * SettingsFieldResolver.
 */
class SettingsFieldResolver {
    /**
     * @var mixed
     */
    protected $dotNotationPath;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param $dotNotationPath
     */
    function __construct($dotNotationPath) {
        $this->dotNotationPath = $dotNotationPath;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        $container = \WP_Tools\Schema\Loader::get_instance();
        $settings  = $container['settings']->get_schema_settings();

        $parts = explode('.', $this->dotNotationPath);

        $error = false;
        // loop over the array. find is key is set. If one is not set, set value to null.
        // Else reduce settings to latest key and traverse
        while (!empty($parts)) {
            $key = array_shift($parts);
            if (!isset($settings[$key])) {
                $error       = true;
                $this->value = null;
                break;
            } else {
                $settings = $settings[$key];
            }

        }

        if (!$error) {
            $this->value = $settings;
        }

        return $this->value;
    }
}