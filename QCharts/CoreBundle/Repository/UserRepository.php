<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/5/16
 * Time: 12:08 PM
 */

namespace QCharts\CoreBundle\Repository;


use QCharts\CoreBundle\Entity\User\User;
use QCharts\CoreBundle\Exception\InstanceNotFoundException;
use QCharts\CoreBundle\Exception\UserRoleException;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    /**
     * @param $role
     * @return array
     */
    public function getUsersWithRole($role)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u')
            ->from($this->getEntityName(), 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $role
     * @param User $user
     * @throws InstanceNotFoundException
     * @throws UserRoleException
     */
    public function addRoleToUser($role, User $user)
    {
        if (!$user || is_null($user))
        {
            throw new InstanceNotFoundException("No user found with the given username", 404);
        }

        if ($user->hasRole($role))
        {
            throw new UserRoleException("User has already the given role", 200);
        }

        $user->addRole($role);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $role
     * @param User $user
     * @throws InstanceNotFoundException
     * @throws UserRoleException
     */
    public function removeRoleFromUser($role, User $user)
    {
        if(!$user)
        {
            throw new InstanceNotFoundException("No user found with the given username", 404);
        }

        if (!$user->hasRole($role))
        {
            throw new UserRoleException("The user does not have the given role", 200);
        }

        $user->removeRole($role);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}