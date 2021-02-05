<?php

namespace RetailCrm\ServiceBundle\Tests\DataFixtures;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RequestDto
 *
 * @package RetailCrm\ServiceBundle\Tests\DataFixtures
 */
class RequestDto
{
    /**
     * @var string
     * @Assert\NotNull()
     */
    public $param;
}
