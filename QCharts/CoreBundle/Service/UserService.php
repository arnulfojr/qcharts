<?php

namespace QCharts\CoreBundle\Service;


use QCharts\CoreBundle\Entity\User\QChartsSubjectInterface;
use QCharts\CoreBundle\Entity\User\User;
use QCharts\CoreBundle\Exception\InstanceNotFoundException;
use QCharts\CoreBundle\Repository\UserRepository;
use QCharts\CoreBundle\ResultFormatter\UserFormatterInterface;
use QCharts\CoreBundle\Service\ServiceInterface\SerializerFactoryClass;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserService
{
    /** @var  UserRepository $repository */
    private $repository;
    /** @var  SerializerFactoryClass $serializerFactory */
    private $serializerFactory;
    /** @var  UserFormatterInterface $formatter */
    private $formatter;
    /** @var  UserManager $userManager */
    private $userManager;

    private $role = null;
    private $encoding = 'json';
    private $username = null;

    /**
     * @param UserRepository $repository
     * @param SerializerFactoryClass $serializerFactoryClass
     * @param UserManager $userManager
     * @param UserFormatterInterface $userFormatterInterface
     */
    public function __construct(
        UserRepository $repository,
        SerializerFactoryClass $serializerFactoryClass,
        UserManager $userManager,
        UserFormatterInterface $userFormatterInterface)
    {
        $this->repository = $repository;
        $this->serializerFactory = $serializerFactoryClass;
        $this->formatter = $userFormatterInterface;
        $this->userManager = $userManager;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * @param boolean $role
     */
    public function setRole($role)
    {
        if ($role)
        {
            $this->role = "ROLE_ADMIN";
        }else{
            $this->role = "ROLE_USER";
        }

        if(is_null($role))
        {
            $this->role = null;
        }
    }

    /**
     * @return null|string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param QChartsSubjectInterface $developer
     * @return string|\Symfony\Component\Serializer\Encoder\scalar
     * @throws InstanceNotFoundException
     */
    public function getDetails(QChartsSubjectInterface $developer)
    {
        $users = $this->getUsers($developer);
        $users = $this->formatter->formatUsers($users);
        $serializer = $this->getSerializer();
        return $serializer->serialize($users, $this->getEncoding());
    }

    /**
     * @param $username
     * @throws InstanceNotFoundException
     * @throws \QCharts\CoreBundle\Exception\UserRoleException
     */
    public function promoteUser($username)
    {
        $user = $this->getUserFromUsername($username);
        $this->repository->addRoleToUser('ROLE_ADMIN', $user);
    }

    /**
     * @param $username
     * @throws InstanceNotFoundException
     * @throws \QCharts\CoreBundle\Exception\UserRoleException
     */
    public function demoteUser($username)
    {
        $user = $this->getUserFromUsername($username);
        $this->repository->removeRoleFromUser('ROLE_ADMIN', $user);
    }

    /**
     * @param $username
     * @return User
     * @throws InstanceNotFoundException
     */
    protected function getUserFromUsername($username)
    {
        $user = $this->userManager->findUserByUsername($username);
        if (!$user)
        {
            throw new InstanceNotFoundException("User with the given username was not found: {$username}", 404);
        }
        return $user;
    }

    /**
     * @param QChartsSubjectInterface $developer
     * @return array
     * @throws InstanceNotFoundException
     */
    protected function getUsers(QChartsSubjectInterface $developer)
    {
        if ($this->role && !is_null($this->role))
        {
            $users =  $this->repository->getUsersWithRole($this->role);
        }
        else
        {
            $users = $this->repository->findAll();
        }

        if (!$users)
        {
            throw new InstanceNotFoundException("No users found", 404);
        }

        if (($key = array_search($developer, $users)) !== false)
        {
            unset($users[$key]);
        }

        return $users;
    }

    /**
     * @return \Symfony\Component\Serializer\Serializer
     */
    protected function getSerializer()
    {
        $ignore = [
            'id', 'salt', 'password', 'plainPassword', 'lastLogin', 'confirmationToken', 'accountNonExpired',
            'accountNonLocked', 'credentialsNonExpired', 'credentialsExpired', 'expired', 'locked', 'passwordRequestedAt',
            'groups', 'groupNames', 'emailCanonical', 'usernameCanonical', 'enabled', 'superAdmin'
        ];
        $this->serializerFactory->setIgnoredAttributes($ignore);
        $normalizers = [new GetSetMethodNormalizer(), new ObjectNormalizer()];
        $this->serializerFactory->setNormalizers($normalizers);
        $serializer = $this->serializerFactory->create();
        return $serializer;
    }

}