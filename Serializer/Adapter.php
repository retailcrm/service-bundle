<?php

namespace RetailCrm\ServiceBundle\Serializer;

/**
 * Interface Adapter
 *
 * @package RetailCrm\ServiceBundle\Serializer
 */
interface Adapter
{
    /**
     * @param string $data
     * @param string $type
     * @param string $format
     *
     * @return object
     */
    public function deserialize(string $data, string $type, string $format = 'json'): object;

    /**
     * @param array       $data
     * @param string      $type
     * @param string|null $format
     *
     * @return object
     */
    public function arrayToObject(array $data, string $type, ?string $format = null): object;
}
