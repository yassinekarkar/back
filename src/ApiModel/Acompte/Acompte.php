<?php

namespace App\ApiModel\Acompte;

use SSH\MsJwtBundle\Request\CommonParameterBag;
use Symfony\Component\Validator\Constraints as Assert;

class Acompte extends CommonParameterBag
{

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $value;


}
