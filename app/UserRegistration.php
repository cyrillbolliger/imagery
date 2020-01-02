<?php


namespace App;


class UserRegistration
{
    private $firstName;
    private $lastName;
    private $city;
    private $email;
    private $comment;

    /**
     * UserRegistration constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->firstName = $data['first_name'];
        $this->lastName  = $data['last_name'];
        $this->city      = $data['city'];
        $this->email     = $data['email'];
        $this->comment   = $data['comment'];
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }
}
