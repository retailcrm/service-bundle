<?php

namespace RetailCrm\ServiceBundle\Tests\DataFixtures;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

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
     * @JMS\Type("string")
     */
    public $param;
}
