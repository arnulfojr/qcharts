<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/10/16
 * Time: 4:36 PM
 */

namespace QCharts\FrontendBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminAction()
    {
        $authChecker = $this->get("security.authorization_checker");

        $roles = $this->getParameter("qcharts.user_roles");
        $urls = $this->getParameter("qcharts.urls");

        if (!$authChecker->isGranted($roles["super_admin"]))
        {
            return new RedirectResponse($urls["redirects"]["logout"]);
        }
        return $this->render('@Frontend/blocks/admin/admin.html.twig', [
            "redirectUrls" => $urls,
            "user_roles" => $roles
        ]);
    }

    /**
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function snapshotAction()
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        $urls = $this->getParameter("qcharts.urls");

        if (!$authChecker->isGranted($roles["super_admin"]))
        {
            return new RedirectResponse($urls["redirects"]["login"]);
        }

        return $this->render('@Frontend/blocks/snapshots/snapshotsView.html.twig', [
            "redirectUrls" => $urls,
            "user_roles" => $roles
        ]);

    }

}