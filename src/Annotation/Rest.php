<?php

namespace Mooon\Rest\Annotation;

/**
 * Class Rest
 * @Annotation
 */
class Rest implements ConfigurationInterface
{
    /**
     * @var integer
     */
    public $status;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $message = '';

    /**
     * @var array
     */
    public $groups = [];

    /**
     * @return bool
     */
    public function allowArray():bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getAliasName():string
    {
        return 'rest';
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        if(!isset($this->type) || null === $this->type){
            return null;
        }
        return $this->type;
    }
}