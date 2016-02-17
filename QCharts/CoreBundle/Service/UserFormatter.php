<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/5/16
 * Time: 3:09 PM
 */

namespace QCharts\CoreBundle\Service;


use QCharts\CoreBundle\Entity\User\User;
use QCharts\CoreBundle\ResultFormatter\UserFormatterInterface;

class UserFormatter implements UserFormatterInterface
{
    /**
     * @param array $users
     * @return array
     */
    public function formatUsers(array $users)
    {
        $results = [];
        foreach ($users as $user)
        {
            /** @var User $user */
            $results[] = [
                "name" => $user->getName(),
                "username" => $user->getUsername(),
                "email"=>$user->getEmail(),
                "developer" => $user->hasRole('ROLE_ADMIN')
            ];
        }
        return $results;
    }
}