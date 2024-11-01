<?php
namespace WP_Tools\Schema\Schema;

/**
 * Organization.
 */
class Organization {

    /**
     * Check if the current page can output the organization/person schema.
     * @param $settings
     */
    public static function canOutput($settings) {
        $loadOnHomepage = false;

        if (isset($settings['entity'], $settings['entity']['load_on_homepage'])) {
            $loadOnHomepage = $settings['entity']['load_on_homepage'];
        }

        if ($loadOnHomepage && is_front_page()) {
            return true;
        }

        if (isset($settings['entity'], $settings['entity']['load_on'], $settings['entity']['load_on']['id'])) {
            global $post;
            if (is_singular('page') && ($post->ID == $settings['entity']['load_on']['id'])) {
                return true;
            } else {
                return false;
            }
        }

        if (is_front_page()) {
            return true;
        }

        return false;

    }

    /**
     * @param $settings
     */
    public static function render($settings) {
        if (isset($settings['entity'], $settings['entity']['type']) && ($settings['entity']['type'] == 'organization')) {
            $entity = $settings['entity'];

            $type = $entity['company_type'] ? $entity['company_type'] : 'Organization';

            $output = new SchemaOutput();
            $output->setType('Organization')
                ->set('@context', 'https://schema.org')
                ->set('@type', $type)
                ->set('name', $entity['company_name'])
                ->set('url', home_url('/'))
                ->set('logo', $entity['logo'])
                ->set('sameAs', SocialProfiles::getSameAsLinkArray($settings));

            if (isset($settings['contact'], $settings['contact']['type']) && $settings['contact']['type']) {
                $output->set('ContactPoint', ContactPoint::getData($settings['contact']));
            }

            $output->render();
        }
    }

}
