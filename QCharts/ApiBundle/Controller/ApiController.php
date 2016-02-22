<?php

namespace QCharts\ApiBundle\Controller;

use QCharts\ApiBundle\Exception\ExceptionMessage;
use QCharts\ApiBundle\Exception\InvalidCredentialsException;
use QCharts\CoreBundle\Exception\DatabaseException;
use QCharts\CoreBundle\Exception\InstanceNotFoundException;
use QCharts\CoreBundle\Exception\OffLimitsException;
use QCharts\CoreBundle\Exception\ParameterNotPassedException;
use QCharts\CoreBundle\Exception\SnapshotException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Exception\WriteReadException;
use QCharts\CoreBundle\Form\QueryRequestType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


class ApiController extends Controller
{
    /* Key constants from the user's roles in configuration */
    const USER = "user";
    const ADMIN = "admin";
    const SUPER_ADMIN = "super_admin";

    /**
     * @return array
     */
    static public function getNotValidCredentials()
    {
        return [
            "status"=>402,
            "textStatus"=>"Credentials not valid"
        ];
    }

    /**
     * @param $authService
     * @param array $roles
     * @param $role
     * @return bool
     * @throws InvalidCredentialsException
     */
    public static function checkCredentials($authService, array $roles, $role)
    {
        /** @var AuthorizationChecker $authService */
        if ($authService->isGranted($roles[$role]))
        {
            return true;
        }
        throw new InvalidCredentialsException(ExceptionMessage::CREDENTIALS_NOT_VALID($role), 402);
    }

    /**
     * @ApiDoc(
     *      description = "Save a query request",
     *      parameters = {
     *          {
     *              "name" = "query_request[title]",
     *              "description" = "Title of the Query",
     *              "required" = true,
     *              "dataType" = "string"
     *          }, {
     *              "name" = "query_request[description]",
     *              "description" = "Brief or concrete description of the query",
     *              "required" = true,
     *              "dataType" = "string"
     *          }, {
     *              "name" = "query_request[config][databaseConnection]",
     *              "required" = true,
     *              "dataType" = "string",
     *              "description" = "The connection to use"
     *          }, {
     *              "name" = "query_request[config][typeOfChart]",
     *              "description" = "Type of chart to display and/or to format query results,
                        please refer to the documentation for supported chart types",
     *              "required" = true,
     *              "dataType" = "string"
     *          }, {
     *              "name" = "query_request[query][query]",
     *              "description" = "Query to execute",
     *              "required" = true,
     *              "dataType" = "string"
     *          }, {
     *              "name" = "query_request[config][queryLimit]",
     *              "required" = false,
     *              "dataType" = "integer",
     *              "description" = "The limit of rows to fetch from the database,
                        refer to the documentation to see the default limit"
     *          }, {
     *              "name"="query_request[config][offset]",
     *              "required" = true,
     *              "dataType" = "integer",
     *              "description" = "The offset from the limit"
     *          }, {
     *              "name"="query_request[config][executionLimit]",
     *              "required" = true,
     *              "dataType" = "number",
     *              "description" = "The time limit execution"
     *          }, {
     *              "name" = "query_request[config][isCached]",
     *              "required" = true,
     *              "dataType" = "integer",
     *              "description" = "type of cached used",
     *              "format" = "[0-2]"
     *          }, {
     *              "name" = "query_request[cronExpression]",
     *              "required" = true,
     *              "dataType" = "string",
     *              "description" = "Cron expression",
     *          }, {
     *              "name" = "query_request[directory]",
     *              "required" = false,
     *              "dataType" = "integer",
     *              "description" = "The folder id to save the query in"
     *          }
     *      },
     *      statusCodes = {
     *          200 = "Registration was successful",
     *          402 = "Credentials not valid",
     *          500 = "The process encountered an unexpected exception"
     *      }
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
	public function registerAction(Request $request)
    {
        $authorizationChecker = $this->get('security.authorization_checker');
		$queryService = $this->get('qcharts.query');
        $snapshotService = $this->get("qcharts.core.snapshot_service");
		$formFactory = $this->get('form.factory');

        $roles = $this->getParameter('qcharts.user_roles');

		if (!$authorizationChecker->isGranted($roles['admin']))
        {
			return $this->getCredentialsNotValid();
		}
		
		try
        {
            $doctrine = $this->get("doctrine");
            $chartTypes = $this->getParameter("qcharts.chart_types");
			/** @var Form $form */
			$form = $formFactory->create(new QueryRequestType($doctrine, $chartTypes));
			$form->submit($request->request->get($form->getName()));
			
			if ($form->isValid())
            {
				$user = $this->getUser();

                $results = $queryService->add($form, $user);

                $snapshotService->writeSnapshot($results["query"], $results["results"]);

                $opts = [
						"status"=>201,
						"textStatus"=>"Query created"
				];
				return new JsonResponse($opts);
			}
		}
        catch (OffLimitsException $e)
        {
            $snapshotService->writeSnapshot($e->getData()["query"], $e->getData()["results"]);

            $options = [
                "status"=>201,
                "textStatus"=>"{$e->getMessage()}. {$e->getPrevious()->getMessage()}"
            ];

            return new JsonResponse($options);
        }
        catch(ParameterNotPassedException $e)
        {
			return $this->getTheFormNotvalid();
		}
        catch (ValidationFailedException $e)
        {
            $options = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage()
            ];
            return new JsonResponse($options);
        }
        catch (TypeNotValidException $e)
        {
            $options = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage()
            ];
            return new JsonResponse($options);
        }
        catch (DatabaseException $e)
        {
            $options = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage()
            ];
            return new JsonResponse($options);
        }
		
		return $this->getTheFormNotValid();
	}

    /**
     *
     * @ApiDoc(
     *      description = "Edit the given query",
     *      parameters = {
     *          {
     *              "name" = "id",
     *              "description" = "Query Request Id to edit",
     *              "dataType" = "integer",
     *              "required" = "true"
     *          }, {
     *              "name" = "query_request[title]",
     *              "description" = "Title of the Query",
     *              "required" = true,
     *              "dataType" = "string"
     *          }, {
     *              "name" = "query_request[description]",
     *              "description" = "Brief or concrete description of the query",
     *              "required" = true,
     *              "dataType" = "string"
     *          }, {
     *              "name" = "query_request[config][databaseConnection]",
     *              "required" = true,
     *              "dataType" = "string",
     *              "description" = "The connection to use"
     *          }, {
     *              "name" = "query_request[config][typeOfChart]",
     *              "description" = "Type of chart to display and/or to format query results,
    please refer to the documentation for supported chart types",
     *              "required" = true,
     *              "dataType" = "string"
     *          }, {
     *              "name" = "query_request[query][query]",
     *              "description" = "Query to execute",
     *              "required" = true,
     *              "dataType" = "string"
     *          }, {
     *              "name" = "query_request[config][queryLimit]",
     *              "required" = false,
     *              "dataType" = "integer",
     *              "description" = "The limit of rows to fetch from the database,
     * refer to the documentation to see the default limit"
     *          }, {
     *              "name"="query_request[config][offset]",
     *              "required" = true,
     *              "dataType" = "integer",
     *              "description" = "The offset from the limit"
     *          }, {
     *              "name"="query_request[config][executionLimit]",
     *              "required" = true,
     *              "dataType" = "number",
     *              "description" = "The time limit execution"
     *          }, {
     *              "name" = "query_request[config][isCached]",
     *              "required" = true,
     *              "dataType" = "integer",
     *              "description" = "type of cached used",
     *              "format" = "[0-2]"
     *          }, {
     *              "name" = "query_request[cronExpression]",
     *              "required" = true,
     *              "dataType" = "string",
     *              "description" = "Cron expression",
     *          }, {
     *              "name" = "query_request[directory]",
     *              "required" = false,
     *              "dataType" = "integer",
     *              "description" = "The folder id to save the query in"
     *          }
     *      }, statusCodes = {
     *          200 = "Requested Query was saved with new data",
     *          402 = "Credentials not valid",
     *          500 = "Form not valid"
     *      }
     * )
     *
     * @param Request $request
     * @param null $queryId
     * @return JsonResponse
     */
	public function editAction(Request $request, $queryId = null)
    {
		$authChecker = $this->get('security.authorization_checker');
		$queryService = $this->get('qcharts.query');
        $snapshotService = $this->get("qcharts.core.snapshot_service");

        $roles = $this->getParameter('qcharts.user_roles');

		if (!$authChecker->isGranted($roles["admin"]))
        {
			return $this->getCredentialsNotValid();
		}

        $opts = [];
        try
        {
            $queryId = $request->request->get('id', null);
            $queryRequest = $queryService->getQueryRequestById($queryId);
            /** @var Form $form */
            $formFactory = $this->get("form.factory");
            $chartTypes = $this->getParameter("qcharts.chart_types");
            $form = $formFactory->create(new QueryRequestType($this->get("doctrine"), $chartTypes), $queryRequest);
            $form->submit($request->request->get($form->getName()));

            if (!$form->isValid() || !$form->isSubmitted())
            {
                throw new ValidationFailedException("Data passed in the form was not valid", 500, null);
            }

            $user = $this->getUser();
            $results = $queryService->edit($form, $user, $queryId);

            $snapshotService->writeSnapshot($results["query"], $results["results"]);

            $opts = [
                "status" =>200,
                "textStatus" => "Query was saved",
                "queryId" => $queryId
            ];

        }
        catch (InstanceNotFoundException $e)
        {
            $opts = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage(),
                "queryId"=>$queryId
            ];
        }
        catch(ValidationFailedException $e)
        {
            $opts = [
                    "status"=>$e->getCode(),
                    "textStatus" => $e->getMessage(),
                    "queryId" => $queryId
            ];

        }
        catch (ParameterNotPassedException $e)
        {
            $opts = [
                "status"=>$e->getCode(),
                "textStatus" => $e->getMessage(),
                "queryId" => $queryId
            ];
        }
        catch (OffLimitsException $e)
        {
            $opts = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage(),
                "queryId"=>$queryId
            ];
        }
        catch (DatabaseException $e)
        {
            $opts = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage(),
                "queryId"=>$queryId
            ];
        }
        catch (TypeNotValidException $e)
        {
            $opts = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage(),
                "queryId"=>$queryId
            ];
        }
        catch (SnapshotException $e)
        {
            $opts = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage(),
                "queryId"=>$queryId
            ];
        }
        catch (WriteReadException $e)
        {
            $opts = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage(),
                "queryId"=>$queryId
            ];
        }
        finally
        {
            return new JsonResponse($opts);
        }
		
	}

    /**
     *
     * @ApiDoc(
     *      description = "Deletes the passed Query",
     *      parameters = {
     *          {
     *              "name" = "id",
     *              "dataType" = "integer",
     *              "required" = true,
     *              "description" = "The Id from the requested Query to delete"
     *          },
     *      },
     *      statusCodes = {
     *          202 = "Requested Query was deleted",
     *          402 = "Credentials are not valid",
     *          404 = "The given Id was not found",
     *          500 = "Error in the deletion"
     *      }
     * )
     *
     * @param Request $request
     * @param null $queryId
     * @return JsonResponse
     */
	public function deleteAction(Request $request, $queryId = null)
    {
		$authChecker = $this->get('security.authorization_checker');
		$queryService = $this->get('qcharts.query');

        $roles = $this->getParameter('qcharts.user_roles');

		if (!$authChecker->isGranted($roles["admin"]))
        {
			return $this->getCredentialsNotValid();
		}

        $opts = [];

        try
        {
			($queryId == null) ? $queryId = $request->request->get('id', null) : false;
			$queryService->delete($queryId);
            //TODO: delete the snapshots! call the service and clean the snapshots!
			$opts = [
					"status"=>202,
					"textStatus" => "Query was deleted"
			];
		}
        catch (ParameterNotPassedException $e)
        {
            $opts = [
                "status"=> $e->getCode(),
                "textStatus" => $e->getMessage()
            ];
        }
        catch (InstanceNotFoundException $e)
        {
			$opts = [
					"status"=> $e->getCode(),
					"textStatus" => $e->getMessage()
			];
		}
        finally
        {
            return new JsonResponse($opts);
        }
	}

    /**
     *
     * @ApiDoc(
     *      description = "Get Action, returns the information of the requested query",
     *      parameters = {
     *          {
     *              "name" = "q",
     *              "dataType" = "integer",
     *              "required" = false,
     *              "description" = "The requested query to fetch the details from, by default all Queries returned"
     *          },
     *          {
     *              "name" = "_format",
     *              "dataType" = "string",
     *              "required" = false,
     *              "description" = "The desired encoding to apply. By default json is applied. Options: json or xml"
     *          },
     *          {
     *              "name" = "tm",
     *              "dataType" = "boolean",
     *              "required" = false,
     *              "description" = "Flag to return only the Time Machined ones"
     *          }
     *      },
     *      statusCodes = {
     *          200 = "Queries returned",
     *          402 = "Credentials were not valid",
     *          404 = "Returns when the given requested query Id was not found",
     *      }
     * )
     * @param Request $request
     * @return JsonResponse|Response
     */
	public function getAction(Request $request)
    {
		$authChecker = $this->get('security.authorization_checker');
		$queryService = $this->get('qcharts.query');
        $roles = $this->getParameter('qcharts.user_roles');

		if (!$authChecker->isGranted($roles["user"]))
        {
			return $this->getCredentialsNotValid($roles["user"]);
		}

		$queryId = $request->query->get('q', null);
        $encodingType = $request->query->get('_format', 'json');
        $cachedFlag = $request->query->get("tm", false);

        try
		{
            $serializer = $this->get("qcharts.serializer");

            if ($cachedFlag)
            {
                $queries = $queryService->getTimeMachinedQueries();
            }
            else
            {
                $queries = $queryService->getQueries($queryId);
            }

            $toSend = [
                "status" => 200,
                "textStatus" => "Queries returned",
                "queries" => $queries,
                "count" => count($queries)
            ];

            $toSend = $serializer->serialize($toSend, $encodingType);

            $response = new Response($toSend);
			$response->headers->set('Content-Type', "application/{$encodingType}");
			return $response;
		}
		catch(InstanceNotFoundException $e)
		{
			$options = [
				"status" => 404,
				"textStatus" => $e->getMessage(),
				"queryId" => $queryId
			];
			return new JsonResponse($options);
		}
	}

    /**
     * @param null $queryId
     * @return JsonResponse
     */
    protected function getTheFormNotValid($queryId = null)
    {
        $opts = [
            "status"=>500,
            "textStatus" => "Data passed was not valid",
            "queryId"=>$queryId
        ];
        return new JsonResponse($opts);
    }

    /**
     * @param null $role
     * @return JsonResponse
     */
    protected function getCredentialsNotValid($role = null)
    {
        return new JsonResponse([
            "status" => 402,
            "textStatus" => "Credentials are not valid {$role}"
        ]);
    }

}