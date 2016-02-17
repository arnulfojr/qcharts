<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/4/16
 * Time: 2:37 PM
 */

namespace QCharts\CoreBundle\Service;


use QCharts\CoreBundle\Service\ServiceInterface\SerializerFactoryClass;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Serializer;

class SerializerFactory extends SerializerFactoryClass
{
    /**
     * @return \Symfony\Component\Serializer\Serializer
     */
    public function create()
    {
        foreach ($this->getNormalizers() as $normalizer)
        {
            $normalizer->setIgnoredAttributes($this->getIgnoredAttributes());
        }
        return new Serializer($this->getNormalizers(), $this->getEncoders());
    }

    /**
     * @param array $normalizers
     */
    public function setNormalizers(array $normalizers)
    {
        $this->normalizers = $normalizers;
    }

    /**
     * @param array $encoders
     */
    public function setEncoders(array $encoders)
    {
        $this->encoders = $encoders;
    }

    /**
     * @param array $ignoredOnes
     */
    function setIgnoredAttributes(array $ignoredOnes)
    {
        $this->ignoredAttributes = $ignoredOnes;
    }
}