<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/28/16
 * Time: 5:36 PM
 */

namespace QCharts\FrontendBundle\Controller;


use QCharts\ApiBundle\Controller\ApiController;
use QCharts\ApiBundle\Controller\ApiDirectoryController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DirectoryController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function directoryAction(Request $request)
    {
        //TODO: finish this implementation
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        $urls = $this->getParameter("qcharts.urls");

        if (!$authChecker->isGranted($roles["admin"]))
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        return $this->render('@Frontend/views/directory/directory.html.twig', [
            "user_roles" => $roles,
            "redirectUrls" => $urls
        ]);
    }
}