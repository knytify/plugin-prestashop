<?php

namespace Knytify\Entity\Admin;

class ConfigurationEntity
{
    protected ?string $api_key = null;
    protected bool $enabled = false;


    /**
     * Get the value of api_key
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * Set the value of api_key
     *
     * @return  self
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;

        return $this;
    }

    /**
     * Get the value of enabled
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set the value of enabled
     *
     * @return  self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }
}