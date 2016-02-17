<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/10/16
 * Time: 4:54 PM
 */

namespace QCharts\FrontendBundle\Controller;


use QCharts\CoreBundle\Entity\QueryRequest;
use QCharts\CoreBundle\Exception\InstanceNotFoundException;
use QCharts\CoreBundle\Form\Directory\DirectoryType;
use QCharts\CoreBundle\Form\QueryRequestType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class QueryController extends Controller
{

    /**
     * @param null $queryId
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction($queryId = null)
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        $urls = $this->getParameter("qcharts.urls");

        if (!$authChecker->isGranted($roles["user"]))
        {
            return new RedirectResponse($urls["redirects"]["login"]);
        }

        $queryService = $this->get("qcharts.query");

        try
        {
            /** @var QueryRequest $queryRequest */
            $queryRequest = $queryService->getQueryRequestById($queryId);

            return $this->render('@Frontend/views/showQuery/mainView.html.twig', [
                'queryRequest'=>$queryRequest,
                'user_roles'=>$roles,
                'exceptionMessage' => '',
                "redirectUrls" => $this->getParameter("qcharts.urls")
            ]);
        }
        catch (InstanceNotFoundException $e)
        {
            //not a valid query request Id
            return new RedirectResponse(""); //TODO: implement something cool here!
        }
    }

    /**
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction()
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        $urls = $this->getParameter("qcharts.urls");

        if (!$authChecker->isGranted($roles["admin"]))
        {
            return new RedirectResponse($urls["redirects"]["login"]);
        }

        $formFactory = $this->get("form.factory");
        $charts = $this->getParameter("qcharts.chart_types");
        $doctrine = $this->get("doctrine");
        $form = $formFactory->create(new QueryRequestType($doctrine, $charts));

        $dirForm = $formFactory->create(new DirectoryType($doctrine));

        return $this->render('@Frontend/views/registerQuery/register.html.twig',
            array(
                'title'=>'Create a new Query',
                'form' => $form->createView(),
                'dirForm' => $dirForm->createView(),
                "user_roles" => $roles,
                "redirectUrls" => $this->getParameter("qcharts.urls")
            ));
    }

    /**
     * @param $queryId
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($queryId)
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        $urls = $this->getParameter("qcharts.urls");

        if (!$authChecker->isGranted($roles["admin"]))
        {
            return new RedirectResponse($urls["redirects"]["login"]);
        }

        $queryService = $this->get("qcharts.query");

        try
        {
            /** @var QueryRequest $queryRequest */
            $queryRequest = $queryService->getQueryRequestById($queryId);

            if (!$queryRequest)
            {
                throw new InstanceNotFoundException('The given Query does not exists.');
            }

            $formFactory = $this->get("form.factory");
            $doctrine = $this->get("doctrine");
            $chartTypes = $this->getParameter("qcharts.chart_types");
            $editForm = $formFactory->create(new QueryRequestType($doctrine, $chartTypes), $queryRequest);

            $dirForm = $formFactory->create(new DirectoryType($doctrine));

            $opts = array(
                'title'=>'Edit Query',
                "queryRequest" => $queryRequest,
                'editForm' => $editForm->createView(),
                'dirForm' => $dirForm->createView(),
                "redirectUrls" => $this->getParameter("qcharts.urls"),
                "user_roles" => $roles
            );

            return $this->render('@Frontend/views/editQuery/editQuery.html.twig', $opts);

        }
        catch(InstanceNotFoundException $e)
        {
            return $this->render('@Frontend/views/main/unexpectedError.html.twig', [
                'errorMessage' => $e->getMessage()
            ]);
        }

    }
}