<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/4/16
 * Time: 12:52 PM
 */

namespace QCharts\CoreBundle\EventSubscriber;


use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserRegistrationSubscriber implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_SUCCESS => "onRegistrationSuccess"
        ];
    }

    /**
     *
     * Sets the default role of the user
     *
     * @param FormEvent $formEvent
     */
    public function onRegistrationSuccess(FormEvent $formEvent)
    {
        $roles = ["ROLE_USER"];
        $user = $formEvent->getForm()->getData();
        $user->setRoles($roles);
    }

}