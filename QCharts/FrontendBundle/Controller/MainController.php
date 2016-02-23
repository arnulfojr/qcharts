<?php

namespace QCharts\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use QCharts\CoreBundle\Exception\NoTableNamesException;

class MainController extends Controller
{
    /**
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function mainAction()
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        $urls = $this->getParameter("qcharts.urls");

        if ($authChecker->isGranted($roles["user"]))
        {
            try
            {
                return $this->render('@Frontend/blocks/main/main.html.twig', array(
                    'isAdmin' => $authChecker->isGranted($roles["admin"]),
                    'user_roles' => $roles,
                    'redirectUrls' => $urls
                ));
            }
            catch (NoTableNamesException $e)
            {
                return $this->render('@Frontend/blocks/main/main.html.twig', array(
                    'queries' => [],
                    'user_roles' => $roles,
                    'redirectUrls' => $urls
                ));
            }

        }

        return new RedirectResponse('/login');
    }

    /**
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function successAction()
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        $urls = $this->getParameter("qcharts.urls");

        if (!$authChecker->isGranted($roles["admin"]))
        {
            return new RedirectResponse($urls["redirects"]["login"]);
        }

        return $this->render('@Frontend/blocks/registerQuery/confirmed.html.twig', [
            'redirectUrls' => $urls,
            "user_roles" => $roles
        ]);

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction()
    {
        $urls = $this->getParameter("qcharts.urls");
        return $this->render('@Frontend/blocks/about/about.html.twig', [
            "user_roles" => $this->getParameter("qcharts.user_roles"),
            'redirectUrls' => $urls
        ]);
    }

    // TODO: continue this here!
    public function databaseSelection(Request $request)
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        $urls = $this->getParameter("qcharts.urls");
        if (!$authChecker->isGranted($roles["user"]))
        {
            return new RedirectResponse($this->generateUrl($urls["redirects"]["logout"]));
        }

        //return the page to load!
    }

}