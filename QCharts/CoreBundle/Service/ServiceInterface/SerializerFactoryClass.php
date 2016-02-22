<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/4/16
 * Time: 2:26 PM
 */

namespace QCharts\CoreBundle\Service\ServiceInterface;


use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

abstract class SerializerFactoryClass
{
    protected $normalizers = null;
    protected $ignoredAttributes = null;
    protected $encoders = null;

    /**
     * @return \Symfony\Component\Serializer\Serializer
     */
    abstract public function create();

    /**
     * @param array $normalizers
     */
    abstract public function setNormalizers(array $normalizers);

    /**
     * @return array
     */
    public function getNormalizers()
    {
        if (is_null($this->normalizers))
        {
            $this->normalizers = [new GetSetMethodNormalizer()];
        }
        return $this->normalizers;
    }

    /**
     * @param array $encoders
     */
    abstract public function setEncoders(array $encoders);

    /**
     * @return array
     */
    public function getEncoders()
    {
        if (is_null($this->encoders))
        {
            $this->encoders = [new XmlEncoder(), new JsonEncode()];
        }

        return $this->encoders;
    }

    /**
     * @return array
     */
    public function getIgnoredAttributes()
    {
        if (is_null($this->ignoredAttributes))
        {
            $this->ignoredAttributes = [
                'salt', 'password', 'plainPassword', 'lastLogin', 'confirmationToken', 'accountNonExpired',
                'accountNonLocked', 'credentialsNonExpired', 'credentialsExpired', 'expired', 'locked', 'passwordRequestedAt',
                'groups', 'groupNames', 'emailCanonical', 'usernameCanonical', 'enabled', 'superAdmin', 'roles', 'favoritedBy'
            ];
        }

        return $this->ignoredAttributes;
    }

    /**
     * @param array $ignoredOnes
     */
    abstract function setIgnoredAttributes(array $ignoredOnes);
}