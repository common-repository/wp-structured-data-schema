<?php
namespace WP_Tools\Schema\Schema;

/**
 * Resolves a schema field settings
 */
class FieldResolver
{
    /**
     * @var mixed
     */
    protected $postObj;
    /**
     * @var mixed
     */

    /**
     * @param $postObj
     * @param $fieldInfo
     */
    protected $fieldInfo;

    /**
     * @var mixed
     */
    protected $value = null;

    /**
     * @var mixed
     */
    protected $default = null;

    /**
     * @param $postObj
     * @param $fieldInfo
     */
    public function __construct(
        $postObj,
        $fieldInfo,
        $default = ''
    ) {
        $this->postObj   = $postObj;
        $this->fieldInfo = $fieldInfo;
        $this->default   = $default;

        $this->init();
    }

    public function init()
    {
        $container = \WP_Tools\Schema\Loader::get_instance();
        if (isset($this->fieldInfo['value'], $this->fieldInfo['value']['type'])) {
            switch ($this->fieldInfo['value']['type']) {
                case 'none':
                    throw new \Exception("Don't include this field", 403);
                    break;

                case 'fixed_value':
                    $this->value = isset($this->fieldInfo['fixed_value']) ? $this->fieldInfo['fixed_value'] : $this->default;
                    $this->formatValue();
                    break;

                case 'company_name':
                    $field       = new Resolver\SettingsFieldResolver('entity.company_name');
                    $this->value = $field->getValue();
                    break;

                case 'personal_name':
                    $field       = new Resolver\SettingsFieldResolver('entity.personal_name');
                    $this->value = $field->getValue();
                    break;

                case 'logo':
                    $field       = new Resolver\SettingsFieldResolver('entity.logo');
                    $this->value = $field->getValue();
                    break;

                case 'blogname':
                    $this->value = get_bloginfo('name');
                    break;

                case 'blogdescription':
                    $this->value = get_bloginfo('description');
                    break;

                case 'site_url':
                    $this->value = home_url('/');
                    break;

                case 'post_permalink':
                    $this->value = get_permalink($this->postObj);
                    break;

                case 'post_title':
                    $this->value = $this->postObj->post_title;
                    break;

                case 'post_content':
                    $this->value = do_shortcode(do_blocks($this->postObj->post_content));
                    break;

                case 'post_excerpt':
                    $this->value = do_shortcode($this->postObj->post_excerpt);
                    break;

                case 'featured_image':
                    $attachment_id  = get_post_thumbnail_id($this->postObj->ID);
                    $featured_image = '';
                    if ($attachment_id) {
                        $featured_image = wp_get_attachment_image_url($attachment_id, '');
                    }
                    $this->value = $featured_image;
                    break;

                case 'post_date':
                    $this->value = get_the_date('Y-m-d\TH:i:s', $this->postObj->ID);
                    break;

                case 'post_modified':
                    $this->value = get_the_modified_date('Y-m-d\TH:i:s', $this->postObj->ID);
                    break;

                case 'author_url':
                    $userData    = get_userdata($this->postObj->post_author);
                    $this->value = get_author_posts_url($userData->ID);
                    break;

                case 'author_name':
                    $userData    = get_userdata($this->postObj->post_author);
                    $this->value = $userData->display_name;
                    break;

                case 'author_url':
                    $userData    = get_userdata($this->postObj->post_author);
                    $this->value = get_author_posts_url($userData->ID);
                    break;

                case 'author_first_name':
                    $userData    = get_userdata($this->postObj->post_author);
                    $this->value = isset($userData->first_name) ? $userData->first_name : $userData->display_name;
                    break;

                case 'author_last_name':
                    $author_data = get_userdata($this->postObj->post_author);
                    $this->value = isset($author_data->last_name) ? $author_data->last_name : $author_data->display_name;
                    break;

                case 'author_image':
                    $this->value = [
                        0 => get_avatar_url($this->postObj->post_author),
                        1 => 96,
                        2 => 96,
                    ];
                    break;

                case 'new_custom_field':
                    $fieldMetaName = $container['custom_fields']->normaliseCarbonFieldName($this->fieldInfo['fixed_value']);
                    $this->value   = carbon_get_post_meta($this->postObj->ID, $fieldMetaName);
                    $this->formatValue();
                    break;

                case 'existing_custom_field':
                    $this->value = get_post_meta($this->postObj->ID, $this->fieldInfo['fixed_value'], true);
                    $this->formatValue();
                    break;

                default:
                    # code...
                    break;
            }
        }
    }

    /**
     * Addition format for the field. Used for fixed and post meta field types
     */
    public function formatValue()
    {
        if (isset($this->fieldInfo['definition'], $this->fieldInfo['definition']['type'])) {
            switch ($this->fieldInfo['definition']['type']) {
                case 'date':
                    $value       = strtotime($this->value);
                    $this->value = wp_date('Y-m-d\TH:i:s', $value);
                    break;

                case 'image':
                    $this->value = wp_strip_all_tags($this->value);
                    break;

                default:
                    # code...
                    break;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
