<?php

namespace Knytify\Entity\Admin;

class RegistrationEntity
{
    protected string $username = '';
    protected string $password = '';
    protected string $passwordCheck = '';

    /**
     * Get the value of username
     */ 
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */ 
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
    

    /**
     * Get the value of passwordCheck
     */ 
    public function getPasswordCheck()
    {
        return $this->passwordCheck;
    }

    /**
     * Set the value of passwordCheck
     *
     * @return  self
     */ 
    public function setPasswordCheck($passwordCheck)
    {
        $this->passwordCheck = $passwordCheck;

        return $this;
    }
}