<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/10/16
 * Time: 5:27 PM
 */

namespace QCharts\FrontendBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DatabaseController extends Controller
{
    /**
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function tableInfoAction(Request $request)
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        $urls = $this->getParameter("qcharts.urls");

        if(!$authChecker->isGranted($roles["admin"]))
        {
            return new RedirectResponse($urls["redirects"]["login"]);
        }

        return $this->render('@Frontend/views/tableInfo/tableInfo.html.twig', [
            'tableName' => $request->query->get('tableName'),
            "redirectUrls" => $urls,
            "user_roles" => $roles,
        ]);
    }
}