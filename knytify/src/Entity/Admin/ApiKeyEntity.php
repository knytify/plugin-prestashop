<?php

namespace Knytify\Entity\Admin;

class ApiKeyEntity
{
    protected string $api_key = '';

    /**
     * Get the value of api_key
     */
    public function getApi_key()
    {
        return $this->api_key;
    }

    /**
     * Set the value of api_key
     *
     * @return  self
     */
    public function setApi_key($api_key)
    {
        $this->api_key = $api_key;

        return $this;
    }
}