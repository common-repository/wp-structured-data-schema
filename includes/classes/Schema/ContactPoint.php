<?php
namespace WP_Tools\Schema\Schema;

/**
 * ContactPoint.
 */
class ContactPoint
{

    /**
     * @param $settings
     */
    public static function getData($contactPoint)
    {
        $output = new SchemaOutput();

        $output->set('@type', 'ContactPoint')
            ->set('contactType', $contactPoint['type']);

        if (isset($contactPoint['telephone']) && $contactPoint['telephone']) {
            $output->set('telephone', $contactPoint['telephone']);
        }
        $contactOption = [];

        if (isset($contactPoint['toll_free']) && $contactPoint['toll_free']) {
            $contactOption[] = 'TollFree';
        }

        if (isset($contactPoint['hearing_impared_supported']) && $contactPoint['hearing_impared_supported']) {
            $contactOption[] = 'HearingImpairedSupported';
        }

        if (!empty($contactOption)) {
            $output->set('contactOption', $contactOption);
        }

        if (!empty($contactPoint['area_served'])) {
            $areaServed = [];
            foreach ($contactPoint['area_served'] as $area) {
                $areaCode = '';

                // key `code` was initially used. It was changed to `value` later.
                // this ensures legacy support.
                if (isset($area['code'])) {
                    $areaCode = $area['code'];
                }
                if (isset($area['value'])) {
                    $areaCode = $area['value'];
                }
                if ($areaCode) {
                    $areaServed[] = $areaCode;
                }

            }
            $output->set('areaServed', $areaServed);
        }
        if (isset($contactPoint['available_language']) && $contactPoint['available_language']) {
            $availableLanguage = [];
            $parts             = explode(',', $contactPoint['available_language']);
            foreach ($parts as $lang) {
                $availableLanguage[] = trim($lang);
            }

            $output->set('availableLanguage', $availableLanguage);
        }

        return $output->getData();
    }

}
