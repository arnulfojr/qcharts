<?php

namespace QCharts\CoreBundle\Service\ServiceInterface;

use Symfony\Component\Serializer\Serializer;

interface SerializationServiceInterface
{

    /**
     * @return \Symfony\Component\Serializer\Serializer
     */
    public function getSerializer();

    /**
     * @param Serializer $serializer
     */
    public function setSerializer(Serializer $serializer);

    /**
     * @param $toSerialize
     * @param $encodingType
     * @return mixed
     */
    public function serialize($toSerialize, $encodingType);

}