<?php
namespace WP_Tools\Schema\Schema;

/**
 * SchemaOutput.
 */
class SchemaOutput
{

    /**
     * @var mixed
     */
    public $data;

    /**
     * @var mixed
     */
    protected $debug = false;

    /**
     * @var mixed
     */
    protected $type;

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->type = trim($type);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $path
     * @param mixed  $value
     */
    public function set(
        $path,
        $value
    ) {
        if (is_string($value)) {
            $value = trim($value);
        }

        $keys = explode('.', $path);
        $at   = &$this->data;

        while (count($keys) > 0) {
            if (count($keys) === 1) {
                $at[array_shift($keys)] = $value;
            } else {
                $key = array_shift($keys);
                if (!isset($at[$key])) {
                    $at[$key] = [];
                }
                $at = &$at[$key];
            }
        }

        return $this;
    }

    /**
     * Render the ld json schema
     */
    public function render($output = true)
    {
        $schema = '';
        if (!empty($this->data)) {

            $add_comment = apply_filters('wptools_add_comment_for_schema', true);

            if ($add_comment) {
                $schema .= '<!-- ' . $this->type . ' Schema by WP Tools, Begin -->';
            }
            $schema .= '<script type="application/ld+json" class="wptools-schema-markup">';
            $schema .= wp_json_encode($this->data);
            $schema .= '</script>';

            if ($add_comment) {
                $schema .= '<!-- ' . $this->type . ' Schema by WP Tools, End -->';
            }

        }

        if ($this->debug) {
            $schema = sprintf('<code>%s</code>', htmlentities2($schema));
        }

        if ($output) {
            // phpcs:ignore
            echo $schema;
            return null;
        } else {
            return $schema;
        }

    }

    /**
     * Remove attributes from the schema data
     */
    public function removeAttributes($attributes = [])
    {
        foreach ($attributes as $attribute) {
            if (isset($this->data[$attribute])) {
                unset($this->data[$attribute]);
            }
        }
    }

}
