<?php
namespace WP_Tools\Schema\Schema\Renderer;

use WP_Tools\Schema\Schema\SchemaOutput;

/**
 * ArticleRenderer.
 */
abstract class SchemaRenderer
{
    /**
     * @var mixed
     */
    /**
     * @param $integration
     */
    protected $integration;

    /**
     * @var mixed
     */
    protected $postObj;

    /**
     * @var mixed
     */
    public $schemaObject;

    protected $ignores;

    /**
     * @param $integration
     */
    public function __construct(
        $integration,
        $postObj,
        $ignores = []
    ) {
        $this->integration  = $integration;
        $this->postObj      = $postObj;
        $this->ignores      = $ignores;
        $this->schemaObject = new SchemaOutput();
    }

    /**
     * @return mixed
     */
    public function getSchemaOutput()
    {
        return $this->schemaObject;
    }

    /**
     * @param $echo
     */
    public function getJsonLd()
    {
        return $this->schemaObject->render(false);
    }

    /**
     * @return mixed
     */
    public function getSchemaArray()
    {
        return $this->schemaObject->getData();
    }

    /**
     * Check if post contains schema
     */
    abstract public function doesPostContainsSchema();

    /**
     * Check if the post is excluded from the schema integration.
     */
    public function isPostExcluded()
    {
        if ($this->integration->hasPostsExcluded()) {
            if (in_array($this->postObj->ID, $this->integration->getPostsExcluded())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the post is included for the schema integration.
     */
    public function isPostIncluded()
    {
        if ($this->integration->hasPostsIncluded()) {
            if (in_array($this->postObj->ID, $this->integration->getPostsIncluded())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $post
     */
    public function isPostTypeIncluded()
    {
        if ($this->integration->hasPostTypes()) {
            if (in_array($this->postObj->post_type, $this->integration->getPostTypes())) {
                return true;
            }
        }
    }

    /**
     *  schema object for the post
     */
    abstract public function setupSchema();

    /**
     * Check if the integration has fields
     */
    public function hasFields()
    {
        return $this->integration->hasFields();
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->integration->getFields();
    }

    public function removeIgnores()
    {
        $this->removeAttributes($this->ignores);
    }

    public function removeAttributes($attributes)
    {
        $this->schemaObject->removeAttributes($attributes);
    }

}
