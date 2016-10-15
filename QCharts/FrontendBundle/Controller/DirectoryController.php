<?php


namespace QCharts\FrontendBundle\Controller;


use QCharts\ApiBundle\Controller\ApiController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DirectoryController extends Controller
{
    /**
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function directoryAction()
    {
        //TODO: finish this implementation
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        $urls = $this->getParameter("qcharts.urls");

        if (!$authChecker->isGranted($roles["admin"]))
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        return $this->render('@Frontend/blocks/directory/directory.html.twig', [
            "user_roles" => $roles,
            "redirectUrls" => $urls
        ]);
    }
}