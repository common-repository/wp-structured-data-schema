<?php
namespace WP_Tools\Schema\Schema;

/**
 * Person.
 */
class Person
{

    /**
     * @param $settings
     */
    public static function render($settings)
    {
        if (isset($settings['entity'], $settings['entity']['type']) && ($settings['entity']['type'] == 'person')) {
            $entity = $settings['entity'];

            $output = new SchemaOutput();
            $output->setType('Person')
                ->set('@context', 'https://schema.org')
                ->set('@type', 'Person')
                ->set('name', $entity['person_name'])
                ->set('url', home_url('/'))
                ->set('image', $entity['logo'])
                ->set('sameAs', SocialProfiles::getSameAsLinkArray($settings));

            $output->render();
        }
    }

}
