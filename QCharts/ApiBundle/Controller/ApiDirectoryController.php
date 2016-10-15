<?php

namespace QCharts\ApiBundle\Controller;


use QCharts\CoreBundle\Entity\Directory;
use QCharts\CoreBundle\Exception\DatabaseException;
use QCharts\CoreBundle\Exception\DirectoryNotEmptyException;
use QCharts\CoreBundle\Exception\InstanceNotFoundException;
use QCharts\CoreBundle\Exception\NotFoundException;
use QCharts\CoreBundle\Exception\OverlappingException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Form\Directory\DirectoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiDirectoryController
 * @package QCharts\ApiBundle\Controller
 */
class ApiDirectoryController extends Controller
{

    /**
     * ApiDoc(
     *      description = "Registers a directory at the given directory",
     *      parameters = {
     *          {
     *              "name" = "register[name]",
     *              "description" = "Name of the directory",
     *              "required" = true,
     *              "dataType" = "string"
     *          },
     *          {
     *              "name" = "register[parent]",
     *              "description" = "The parent Id",
     *              "required" = false,
     *              "dataType" = "integer"
     *          }
     *      },
     *      statusCodes = {
     *          201 = "Entity created"
     *      }
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registerAction(Request $request)
    {
        $options = [];
        $this->get("security.token_storage");
        $authChecker = $this->get('security.authorization_checker');
        $roles = $this->getParameter('qcharts.user_roles');

        if (!$authChecker->isGranted($roles["admin"]))
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        try
        {
            $formFactory = $this->get("form.factory");
            $doctrine = $this->get("doctrine");
            $directory = new Directory();
            /** @var FormInterface $form */
            $form = $formFactory->create(new DirectoryType($doctrine), $directory);
            $form->submit($request->request->get($form->getName()));

            if ($form->isValid())
            {
                $directoryService = $this->get("qcharts.directory.directory_service");
                $directoryService->add($form);

                $options = [
                    "status"=>201,
                    "textStatus"=>"Directory created"
                ];

            }

        }
        catch (OverlappingException $e)
        {
            $options = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage()
            ];
        }
        finally
        {
            return new JsonResponse($options);
        }
    }

    /**
     *
     * ApiDoc(
     *      description = "",
     *      parameters = {
     *          {
     *              "name" = "delete[id]",
     *              "dataType" = "integer",
     *              "required" = true,
     *              "description" = "Directory id"
     *          }
     *      },
     *      statusCodes = {
     *          202 = "The directory was deleted",
     *          555 = "The form passed was not valid"
     *      }
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function deleteAction(Request $request)
    {
        $roles = $this->getParameter("qcharts.user_roles");
        if (!$this->isGranted($roles["admin"]))
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        $options = [];
        try
        {
            $directoryId = $request->request->get("id");

            $dirService = $this->get("qcharts.directory.directory_service");
            $parentId = $dirService->delete($directoryId);
            $options = [
                "status" => 202,
                "textStatus" => "Folder was deleted",
                "parentId" => $parentId
            ];
        }
        catch (ValidationFailedException $e)
        {
            $options = [
                "status" => $e->getCode(),
                "textStatus" => $e->getMessage()
            ];
        }
        catch (NotFoundException $e)
        {
            $options = [
                "status" => $e->getCode(),
                "textStatus" => $e->getMessage()
            ];
        }
        catch (DatabaseException $e)
        {
            $options = [
                "status" => $e->getCode(),
                "textStatus" => $e->getMessage()
            ];
        }
        catch (DirectoryNotEmptyException $e)
        {
            $options = [
                "textStatus" => $e->getMessage(),
                "status" => $e->getCode()
            ];
        }
        finally
        {
            return new JsonResponse($options);
        }

    }

    /**
     * ApiDoc(
     *      description = "Edit the desired Directory",
     *      parameters = {
     *          {
     *              "name" = "edit[id]",
     *              "required" = true,
     *              "description" = "The id of the directory",
     *              "dataType" = "integer"
     *          },
     *          {
     *              "name" = "edit[name]",
     *              "required" = true,
     *              "description" = "The new name of the directory",
     *              "dataType" = "string"
     *          }
     *      },
     *      statusCodes = {
     *          200 = "Success",
     *          555 = "Form is not valid",
     *          500 = "Unexpected error"
     *      }
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function editAction(Request $request)
    {
        $roles = $this->getParameter("qcharts.user_roles");
        $dirService = $this->get("qcharts.directory.directory_service");

        if (!$this->isGranted($roles["admin"]))
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        $options = [];
        try
        {
            $formFactory = $this->get("form.factory");
            $parameters = $request->request->get('edit');
            $directoryId = $request->request->get("id");
            $doctrine = $this->get("doctrine");
            $directory = $dirService->getDirectory($directoryId);
            $parameters["parent"] = ($directory->getParent()) ? $directory->getParent()->getId() : null;
            /** @var FormInterface $form */
            $form = $formFactory->create(new DirectoryType($doctrine), $directory);
            $form->submit($parameters);

            if (!$form->isValid())
            {
                //return form not valid
                throw new ValidationFailedException("the form sent was not valid", 555);
            }

            //edit it!
            $id = $dirService->edit($form);

            $options = [
                "status" => 200,
                "textStatus" => "The desired folder was updated",
                "directoryId" => $id
            ];
        }
        catch (ValidationFailedException $e)
        {
            $options = [
                "status" => $e->getCode(),
                "textStatus" => "Something was not as expected, {$e->getMessage()}"
            ];
        }
        catch (TypeNotValidException $e)
        {
            //something went wrong with the types!
            $options = [
                "status" => $e->getCode(),
                "textStatus" => "Something happened while attempting to save the folder, {$e->getMessage()}"
            ];
        }
        catch (NotFoundException $e)
        {
            $options = [
                "status" => $e->getCode(),
                "textStatus" => "We couldn't locate the desired folder, {$e->getMessage()}"
            ];
        }
        catch (OverlappingException $e)
        {
            $options = [
                "status" => $e->getCode(),
                "textStatus" => $e->getMessage()
            ];
        }
        finally
        {
            return new JsonResponse($options);
        }

    }

    /**
     * ApiDoc(
     *      description="Returns the directories in the given directory",
     *      parameters = {
     *          {
     *              "name" = "currentDirectory",
     *              "description" = "The current directory to ask the directories from",
     *              "required" = "false",
     *              "dataType" = "string"
     *          }
     *      }
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function getAction(Request $request)
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter('qcharts.user_roles');
        $allow_demo_users = $this->getParameter('qcharts.allow_demo_users');

        if (!$authChecker->isGranted($roles["user"]) && !$allow_demo_users)
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        $responseData = [];
        try
        {
            $rootDirectory = $request->query->get('currentDirectory', null);
            $directoryService = $this->get("qcharts.directory.directory_service");
            $tree = $directoryService->getDirectories($rootDirectory);
            $serializer = $this->get("qcharts.serializer");

            $responseData = [
                "status" => 200,
                "textStatus" => "Directories returned",
                "directories" => $tree
            ];

            $responseData = $serializer->serialize($responseData, 'json');
        }
        catch (NotFoundException $e)
        {
            $responseData = $e->getMessage();
        }
        catch (TypeNotValidException $e)
        {
            $responseData = $e->getMessage();
        }
        catch (InstanceNotFoundException $e)
        {
            $responseData = $e->getMessage();
        }
        finally
        {
            $response = new Response($responseData);
            $response->headers->set('content-type', 'application/json');
            return $response;
        }
    }

    /**
     * ApiDoc(
     *      description = "Returns the Queries in the requested directory",
     *      parameters = {
     *          {
     *              "name" = "dir",
     *              "dataType" = "integer",
     *              "required" = true,
     *              "description" = "The directory Id"
     *          },
     *          {
     *              "name" = "_format",
     *              "dataType" = "string",
     *              "required" = false,
     *              "description" = "The encoding type"
     *          }
     *      },
     *      statusCodes = {
     *          200 = "The queries were returned"
     *      }
     * )
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function queriesInDirectoryAction(Request $request)
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        $serializer = $this->get("qcharts.serializer");
        $allow_demo_users = $this->getParameter('qcharts.allow_demo_users');

        if (!$authChecker->isGranted($roles["user"]) && !$allow_demo_users)
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        $encodingType = $request->query->get("_format", "json");
        $options = [];

        try
        {
            $directoryId = $request->query->get("dir", null);
            $queryService = $this->get("qcharts.query");
            $results = $queryService->getQueriesInDirectory($directoryId);
            $options = [
                "queries"=>$results,
                "count"=> count($results),
                "status"=>200,
                "textStatus"=>"Queries returned"
            ];
        }
        catch (TypeNotValidException $e)
        {
            $options = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage()
            ];
        }
        catch (DatabaseException $e)
        {
            $options = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage()
            ];
        }
        finally
        {
            $options = $serializer->serialize($options, $encodingType);
            $response = new Response($options);
            $response->headers->set("Content-type", "application/json");
            return $response;
        }

    }

}