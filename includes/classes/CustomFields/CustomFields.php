<?php

namespace WP_Tools\Schema\CustomFields;

use Carbon_Fields\Field;
use Carbon_Fields\Container;
/**
 * CustomFields.
 */
class CustomFields {
    /**
     * @var mixed
     */
    protected $container;

    /**
     * Constructor.
     */
    public function __construct( $container ) {
        $this->container = $container;
    }

    public function init() {
        add_action( 'after_setup_theme', [$this, 'after_setup_theme'] );
        add_action( 'carbon_fields_register_fields', [$this, 'carbon_fields_register_fields'] );
    }

    /**
     * Boot carbon fields
     */
    public function after_setup_theme() {
        if ( !defined( 'Carbon_Fields\\DIR' ) ) {
            define( 'Carbon_Fields\\DIR', $this->container['plugin_dir'] . '/vendor/htmlburger/carbon-fields' );
        }
        require_once $this->container['plugin_dir'] . '/includes/lib/wptools-carbon-duration-field/field.php';
        \Carbon_Fields\Carbon_Fields::boot();
        do_action( 'wpt_schema_carbon_fields_after_boot' );
    }

    /**
     * @param $meta_key
     */
    public function get_video_field( $meta_key, $metabox_name = 'Video' ) {
        $field = Field::make( 'complex', $meta_key, $metabox_name )->set_max( 1 );
        $field->add_fields( [
            Field::make( 'text', 'name', 'Title' )->set_help_text( 'Enter name of the video' ),
            Field::make( 'textarea', 'description', 'Description' )->set_help_text( 'Enter description of the video' ),
            Field::make( 'text', 'thumbnail_url', 'Thumbnail URL' )->set_help_text( 'Enter image thumbnail URL representing the video' ),
            Field::make( 'text', 'content_url', 'Content URL' )->set_help_text( 'A URL pointing to the actual video media file' ),
            Field::make( 'text', 'embed_url', 'Embed URL' )->set_help_text( 'A URL pointing to a player for the specific video' ),
            Field::make( 'date', 'upload_date', 'Upload Date' )->set_help_text( 'Select video upload date' ),
            Field::make( 'iso8601duration', 'duration', 'Duration' )->set_help_text( 'The duration of the video' )
        ] )->set_header_template( '
                      <%- name %>
                     ' );
        return $field;
    }

    /**
     * @param $integration
     */
    public function get_fields( $integration, $ignores = [], $fieldDefinitions = [] ) {
        $fields = [];
        if ( isset( $integration['fields'] ) && !empty( $integration['fields'] ) ) {
            foreach ( $integration['fields'] as $name => $fieldItem ) {
                if ( in_array( $name, $ignores ) ) {
                    continue;
                }
                if ( isset( $fieldItem['value'], $fieldItem['value']['type'] ) && $fieldItem['value']['type'] == 'new_custom_field' ) {
                    $fieldMetaName = $this->normaliseCarbonFieldName( $fieldItem['fixed_value'] );
                    $label = $fieldDefinitions[$name]['label'];
                    // if the custom field name is empty ignore it.
                    if ( !$fieldMetaName ) {
                        continue;
                    }
                    switch ( $fieldDefinitions[$name]['type'] ) {
                        case 'image':
                            $field = Field::make( 'image', $fieldMetaName, $label )->set_value_type( 'url' );
                            break;
                        case 'date':
                            $field = Field::make( 'date_time', $fieldMetaName, $label );
                            break;
                        case 'text':
                        default:
                            $carbonFieldType = 'text';
                            if ( isset( $fieldDefinitions[$name]['carbonFieldType'] ) ) {
                                $carbonFieldType = $fieldDefinitions[$name]['carbonFieldType'];
                            }
                            $field = Field::make( $carbonFieldType, $fieldMetaName, $label );
                            break;
                    }
                    $help_text = ( isset( $fieldDefinitions[$name]['info'] ) ? $fieldDefinitions[$name]['info'] : '' );
                    $field->set_help_text( $help_text );
                    $fields[] = $field;
                }
            }
        }
        return $fields;
    }

    /**
     * Normalize the carbon field name as per their given standards.
     * This should prevent fatal errors.
     */
    public function normaliseCarbonFieldName( $fieldName ) {
        $fieldName = trim( strtolower( $fieldName ) );
        $fieldName = str_replace( ' ', '_', $fieldName );
        $fieldName = str_replace( '-', '_', $fieldName );
        return $fieldName;
    }

    /**
     * Register post meta fields.
     */
    public function carbon_fields_register_fields() {
        \WP_Tools\Schema\Schema\Article::carbon_fields_register_fields( $this->container );
    }

}
