<?php

namespace QCharts\CoreBundle\Service;

use QCharts\CoreBundle\Service\ServiceInterface\SerializationServiceInterface;
use QCharts\CoreBundle\Service\ServiceInterface\SerializerFactoryClass;
use Symfony\Component\Serializer\Serializer;

class SerializationService implements SerializationServiceInterface
{
    /** @var null|Serializer */
	private $serializer = null;
    /** @var  SerializerFactoryClass */
    private $serializerFactory;

    /**
     * @param SerializerFactoryClass $factoryClass
     */
    public function __construct(SerializerFactoryClass $factoryClass)
    {
        $this->serializerFactory = $factoryClass;
    }

    /**
     * @return Serializer
     */
	public function getSerializer()
    {
		return $this->serializer;
	}

	/**
	 * @param Serializer $serializer
	 */
	public function setSerializer(Serializer $serializer)
	{
		$this->serializer = $serializer;
	}

    /**
     * @param $toSerialize
     * @param $encodingType
     * @return string
     */
    public function serialize($toSerialize, $encodingType)
    {
        if (is_null($this->getSerializer()))
        {
            $this->setSerializer($this->serializerFactory->create());
        }
        return $this->getSerializer()->serialize($toSerialize, $encodingType);
    }
}