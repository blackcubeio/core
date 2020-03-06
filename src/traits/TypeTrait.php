<?php

namespace blackcube\core\traits;


trait TypeTrait
{
    /**
     * @return string controller
     */
    public function getController()
    {
        return (($this->type === null) || empty($this->type->controller) === true) ? null : $this->type->controller;
    }

    /**
     * @return string action
     */
    public function getAction()
    {
        return (($this->type === null) || empty($this->type->action) === true) ? null : $this->type->action;
    }

    /**
     * @return integer|null
     */
    public function getMinBlocs()
    {
        return (($this->type === null) || empty($this->type->minBlocs) === true) ? null : $this->type->minBlocs;
    }

    /**
     * @return integer|null
     */
    public function getMaxBlocs()
    {
        return (($this->type === null) || empty($this->type->maxBlocs) === true) ? null : $this->type->maxBlocs;
    }

}
