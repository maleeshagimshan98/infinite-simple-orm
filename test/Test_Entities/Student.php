<?php
/**
 * Copyright - 2021 - Maleesha Gimshan (github.com/maleeshagimshan98)
*/
declare(strict_types = 1);

namespace App\Entities;

class Student {

    private $id;

    private $name;

    private $age;

    private $country;

    public function __construct($id, $name, $age, $country)
    {
        $this->id = $id;
        $this->name = $name;
        $this->age = $age;
        $this->country = $country;
    }


    
}