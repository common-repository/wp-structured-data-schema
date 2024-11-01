<?php
namespace WP_Tools\Schema\Schema;

/**
 * SocialProfiles.
 */
class SocialProfiles
{

    /**
     * @param $settings
     */
    public static function getSameAsLinkArray($settings)
    {
        $sameAs = [];

        if (isset($settings['entity'])) {
            $entity = $settings['entity'];

            if (isset($entity['facebook']) && $entity['facebook']) {
                $sameAs[] = $entity['facebook'];
            }
            if (isset($entity['twitter']) && $entity['twitter']) {
                $sameAs[] = $entity['twitter'];
            }
            if (isset($entity['instagram']) && $entity['instagram']) {
                $sameAs[] = $entity['instagram'];
            }
            if (isset($entity['youtube']) && $entity['youtube']) {
                $sameAs[] = $entity['youtube'];
            }
            if (isset($entity['linkedin']) && $entity['linkedin']) {
                $sameAs[] = $entity['linkedin'];
            }
            if (isset($entity['pinterest']) && $entity['pinterest']) {
                $sameAs[] = $entity['pinterest'];
            }
            if (isset($entity['sound_cloud']) && $entity['sound_cloud']) {
                $sameAs[] = $entity['sound_cloud'];
            }
            if (isset($entity['tumblr']) && $entity['tumblr']) {
                $sameAs[] = $entity['tumblr'];
            }

        }

        return $sameAs;

    }

}
