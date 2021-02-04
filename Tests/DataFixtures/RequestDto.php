<?php

namespace RetailCrm\ServiceBundle\Tests\DataFixtures;

use Symfony\Component\Validator\Constraints as Assert;

class RequestDto
{
    /**
     * @var string
     * @Assert\NotNull()
     */
    public $param;
}
