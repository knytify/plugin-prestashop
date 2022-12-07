<?php

namespace Knytify\Service\Admin;

use Symfony\Component\Form\AbstractType;
use Knytify\Entity\Admin\RegistrationEntity;


class KnytifyClient extends AbstractType
{

    protected ?string $error = null;

    public function register(RegistrationEntity $data)
    {

        return true;
    }

    public function getError()
    {
        return $this->error;
    }
}