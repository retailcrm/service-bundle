<?php

namespace RetailCrm\ServiceBundle\Tests\DataFixtures;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

class RequestDto
{
    /**
     * @Assert\NotNull()
     * @JMS\Type("string")
     */
    public string $param;
}
