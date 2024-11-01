<?php
namespace WP_Tools\Schema\Schema\Renderer;

use WP_Tools\Schema\Schema\FieldResolver;

/**
 * ArticleRenderer.
 */
class ArticleRenderer extends SchemaRenderer
{

    /**
     * @param $post
     */
    public function doesPostContainsSchema()
    {
        if ($this->isPostExcluded()) {
            return false;
        }

        if ($this->isPostIncluded()) {
            return true;
        }

        if ($this->isPostTypeIncluded()) {
            return true;
        }

        return false;
    }

    /**
     * @param $post
     */
    public function setupSchema()
    {

        if ($this->hasFields()) {
            $this->schemaObject->set('@context', 'https://schema.org');
            $this->schemaObject->set('mainEntityOfPage.@type', 'WebPage');

            $container = \WP_Tools\Schema\Loader::get_instance();
            $fields    = $this->getFields();
            $fields    = $container['schema_definitions']->syncFields(
                $fields,
                $container['article_schema_definition']->get_fields()
            );

            foreach ($fields as $name => $fieldInfo) {
                $schemaKey = '';
                $default   = '';

                switch ($name) {
                    case 'type':
                        $schemaKey = '@type';
                        $default   = 'Article';
                        break;

                    case 'url':
                        $schemaKey = 'mainEntityOfPage.@id';
                        break;

                    case 'headline':
                        $schemaKey = 'headline';
                        break;

                    case 'description':
                        $schemaKey = 'description';
                        break;

                    case 'image':
                        $schemaKey = 'image';
                        break;

                    case 'publisher':

                        $schemaKey = 'publisher.name';
                        break;

                    case 'publisherLogo':
                        $schemaKey = 'publisher.logo.url';

                        break;

                    case 'datePublished':
                        $schemaKey = 'datePublished';
                        break;

                    case 'dateModified':
                        $schemaKey = 'dateModified';
                        break;

                    case 'authorName':
                        $schemaKey = 'author.name';
                        break;

                    case 'authorUrl':
                        $schemaKey = 'author.url';
                        break;

                    default:
                        # code...
                        break;
                }

                if ($schemaKey) {
                    try {
                        $field = new FieldResolver($this->postObj, $fieldInfo);
                    } catch (\Exception $e) {
                        // field set to `none` so dont't set schema field value.
                        continue;
                    }
                    $this->schemaObject->set($schemaKey, $field->getValue(), $default);
                }
            }

            $schemaData = $this->schemaObject->getData();

            if (isset($schemaData['publisher'], $schemaData['publisher']['logo'], $schemaData['publisher']['logo']['url']) && $schemaData['publisher']['logo']['url']) {
                $this->schemaObject->set('publisher.logo.@type', 'ImageObject');
            }

            if (isset($schemaData['publisher'], $schemaData['publisher']['name']) && $schemaData['publisher']['name']) {
                $this->schemaObject->set('publisher.@type', 'Organization');
            }

            if (isset($schemaData['author'], $schemaData['author']['name']) && $schemaData['publisher']['name']) {
                $this->schemaObject->set('author.@type', 'Person');
            }

            if (isset($schemaData['@type'])) {
                $this->schemaObject->setType($schemaData['@type']);
            }

        }
    }

}
