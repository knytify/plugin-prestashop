<?php

namespace Knytify\Service\Validation;

use Symfony\Component\Form\AbstractType;

class KnytifyValidation extends AbstractType
{
    private ?string $error = null;

    public function validateEmail(string $email): bool
    {
        $this->reset();
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error = 'The e-mail address must be valid.';

            return false;
        }

        return true;
    }

    public function validatePassword(string $password): bool
    {
        $this->reset();
        if (!preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/', $password)) {
            $this->error = 'Must provide a valid password.';

            return false;
        }

        return true;
    }

    private function reset()
    {
        $this->error = null;
    }

    /**
     * Get the value of error
     */
    public function getError()
    {
        return $this->error;
    }
}
