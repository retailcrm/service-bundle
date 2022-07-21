<?php

namespace RetailCrm\ServiceBundle\Serializer;

interface Adapter
{
    public function deserialize(string $data, string $type, string $format = 'json'): object;

    public function arrayToObject(array $data, string $type, ?string $format = null): object;
}
