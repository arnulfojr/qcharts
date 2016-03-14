<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/10/16
 * Time: 3:55 PM
 */

namespace QCharts\ApiBundle\Controller;


use QCharts\CoreBundle\Exception\DatabaseException;
use QCharts\CoreBundle\Exception\InstanceNotFoundException;
use QCharts\CoreBundle\Exception\ParameterNotPassedException;
use QCharts\CoreBundle\Exception\SQLException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Service\QueryService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use QCharts\CoreBundle\Exception\NoTableNamesException;
use QCharts\CoreBundle\Exception\NotFoundException;
use QCharts\CoreBundle\Exception\NotPlotableException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use QCharts\CoreBundle\Exception\SnapshotException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use QCharts\CoreBundle\Entity\QueryRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class DatabaseController extends Controller
{
    /**
     * @ApiDoc(
     * 		description="chartData Action, returns the data formatted for the chart",
     * 		parameters = {
     *          {
     *              "name" = "q",
     *              "dataType" = "integer",
     *              "required"  = true,
     *              "description" = "The requested Query Id"
     *          },
     *          {
     *              "name" = "snapshot",
     *              "dataType" = "string",
     *              "required" = false,
     *              "description" = "The selected snapshot to format, if not given uses the freshest snapshot"
     *          },
     *          {
     *              "name" = "type",
     *              "dataType" = "string",
     *              "required" = false,
     *              "description" = "Type of formatting to be applied to the results of the query, default = 'line'"
     *          }
     *      },
     *      statusCodes = {
     *          200 = "Returned successfully",
     *          402 = "Credentials not valid",
     *          404 = "Requested Query was not found with the given Id",
     *          405 = "Requested formatting is not supported",
     *          500 = "Exception found and returned with error",
     *          555 = "The snapshot given was not a valid format"
     *      }
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function chartDataAction(Request $request)
    {
        $authChecker = $this->get("security.authorization_checker");
        $queryService = $this->get("qcharts.query");
        $resultsFormatter = $this->get("qcharts.query_results_formatter");
        $strategyFactory = $this->get("qcharts.core.fetching_factory");
        $snapshotService = $this->get("qcharts.core.snapshot_service");

        $roles = $this->getParameter('qcharts.user_roles');
        $allow_demo_users = $this->getParameter('qcharts.allow_demo_users');

        if ($authChecker->isGranted($roles["user"]) || $allow_demo_users)
        {
            $opts = [];
            try
            {
                $queryId = $request->query->get("q", 0);
                $formatType = $request->query->get("type", 'line');
                $snapshot = $request->query->get("snapshot", null);

                /** @var QueryRequest $queryRequest */
                $queryRequest = $queryService->getQueryRequestById($queryId);

                $strategy = $strategyFactory->createStrategy($queryRequest->getConfig()->getIsCached(), $snapshot);

                $results = $strategy->getResults($queryRequest);
                $queryDuration = $strategy->getDuration();

                $chartResults = $resultsFormatter->formatResults($results, $formatType);

                try
                {
                    $snapshotUsed = $snapshotService->formatSnapshotName($queryRequest, $snapshot);
                }
                catch (SnapshotException $e)
                {
                    $snapshotUsed = "Got results live";
                }

                $opts = [
                    "status"=>200,
                    "textStatus"=>"Data for chart returned",
                    "chartData"=>$chartResults,
                    "duration"=>"{$queryDuration} secs",
                    "snapshot" => $snapshotUsed
                ];

            }
            catch (NotPlotableException $e)
            {
                $opts = [
                    "status" => $e->getCode(),
                    "textStatus" => $e->getMessage()
                ];
            }
            catch (ValidationFailedException $e)
            {
                $opts = [
                    "status" => $e->getCode(),
                    "textStatus" => $e->getMessage()
                ];
            }
            catch (InstanceNotFoundException $e)
            {
                $opts = [
                    "status" => $e->getCode(),
                    "textStatus" => $e->getMessage()
                ];
            }
            catch (ParameterNotPassedException $e)
            {
                $opts = [
                    "status" => $e->getCode(),
                    "textStatus" => $e->getMessage()
                ];
            }
            catch (SQLException $e)
            {
                $opts = [
                    "status" => $e->getCode(),
                    "textStatus" => $e->getMessage()
                ];
            }
            catch (TypeNotValidException $e)
            {
                $opts = [
                    "status" => $e->getCode(),
                    "textStatus" => $e->getMessage()
                ];
            }
            catch (SnapshotException $e)
            {
                $opts = [
                    "status"=> 404,
                    "textStatus"=> "The snapshot is not yet cached, sorry for the inconvenience"
                ];
            }
            catch (NotFoundException $e)
            {
                $opts = [
                    "status" => $e->getCode(),
                    "textStatus" => $e->getMessage()
                ];
            }
            catch (IOException $e)
            {
                $opts = [
                    "status"=>$e->getCode(),
                    "textStatus"=>$e->getMessage()
                ];
            }
            finally
            {
                return new JsonResponse($opts);
            }
        }

        return new JsonResponse(ApiController::getNotValidCredentials());

    }

    /**
     *
     * @ApiDoc(
     *      description = "Returns the information of the requested table from the database",
     *      parameters = {
     *          {
     *              "name" = "tableName",
     *              "dataType" = "string",
     *              "required" = true,
     *              "description" = "Name of the table in the database"
     *          },
     *          {
     *              "name" = "connection",
     *              "dataType" = "string",
     *              "required" = false,
     *              "description" = "The name of the connection used"
     *          }
     *      },
     *      statusCodes = {
     *          200 = "Information of the table returned",
     *          402 = "Credentials not valid",
     *          404 = "Table information not found",
     *          500 = "Uncaught Exception"
     *      }
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function tableInformationAction(Request $request)
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter('qcharts.user_roles');

        if (!$authChecker->isGranted($roles["admin"]))
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        $options = [];
        try
        {
            $queryService = $this->get("qcharts.query");
            $columns = $queryService->getTableInformation($request->query);
            $options = [
                "status"=>200,
                "textStatus"=>"Information from the requested table returned",
                "data"=>$columns
            ];
        }
        catch (ParameterNotPassedException $e)
        {
            $options = [
                "data" => [],
                "status" => $e->getCode(),
                "textStatus"=> $e->getMessage()
            ];
        }
        catch (NoTableNamesException $e)
        {
            $options = [
                "data" => [],
                "status" => $e->getCode(),
                "textStatus"=> $e->getMessage()
            ];
        }
        catch (ValidationFailedException $e)
        {
            $options = [
                "data"=>[],
                "status"=>$e->getCode(),
                "textStatus"=> $e->getMessage()
            ];
        }
        finally
        {
            return new JsonResponse($options);
        }
    }

    /**
     *
     * @ApiDoc(
     * 		description = "Returns the schema tables",
     *      parameters = {
     *          {
     *              "name"="connection",
     *              "dataType"="string",
     *              "required"=false,
     *              "description"="The name of the connection to use"
     *          },
     *          {
     *              "name"="schema",
     *              "dataType"="string",
     *              "required"=true,
     *              "description"="The name of the schema to use"
     *          },
     *      },
     * 		statusCodes = {
     * 			200 = "Database tables returned",
     * 			402 = "Credentials not authorized"
     * 		}
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function tablesAction(Request $request)
    {
        $authChecker = $this->get("security.authorization_checker");
        $queryService = $this->get("qcharts.query");

        $roles = $this->getParameter('qcharts.user_roles');

        if (!$authChecker->isGranted($roles["admin"]))
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        $options = [];
        try
        {
            $connectionName = $request->query->get('connection', 'default');
            $schemaName = $request->query->get('schema');
            $tableNames = $queryService->getTableNames($schemaName, $connectionName);
            $options = [
                "status" => 200,
                "textStatus" => "Table names returned",
                "tables" => $tableNames
            ];
        }
        catch(NoTableNamesException $e)
        {
            $options = [
                "status" => $e->getCode(),
                "textStatus" => $e->getMessage(),
                "tables" => []
            ];
        }
        catch (ValidationFailedException $e)
        {
            $options = [
                "status" => $e->getCode(),
                "textStatus"=>$e->getMessage(),
                "tables" => []
            ];
        }
        finally
        {
            return new JsonResponse($options);
        }

    }

    /**
     *
     * @ApiDoc(
     *      description = "Returns the name of the schemas found in the given connection",
     *      parameters = {
     *          {
     *              "name" = "connection",
     *              "dataType" = "string",
     *              "required" = true,
     *              "description" = "The connection to retrieve the desired schema information from"
     *          }
     *      },
     *      statusCodes = {
     *          200 = "Schema names returned",
     *          500 = "problem with the given connection name"
     *      }
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function schemasAction(Request $request)
    {
        $authChecker = $this->get("security.authorization_checker");
        $queryService = $this->get("qcharts.query");

        $roles = $this->getParameter('qcharts.user_roles');

        if (!$authChecker->isGranted($roles["admin"]))
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        $options = [];
        try
        {
            $connectionName = $request->query->get('connection', "default");
            $schemas = $queryService->getSchemas($connectionName);
            $options = [
                "schemas"=>$schemas,
                "status"=>200,
                "textStatus"=>"The schema information from the connection were returned"
            ];
        }
        catch (ValidationFailedException $e)
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
            return new JsonResponse($options);
        }

    }

    /**
     *
     * @ApiDoc(
     *      description = "Returns the names of the current supported connections",
     *      statusCodes = {
     *          200 = "Success, connections returned",
     *          500 = "Unexpected exception"
     *      }
     * )
     *
     * @return JsonResponse
     */
    public function connectionsAction()
    {
        $authChecker = $this->get('security.authorization_checker');
        $roles = $this->getParameter('qcharts.user_roles');

        if (!$authChecker->isGranted($roles["admin"]))
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        $options = [];
        try
        {
            $queryService = $this->get("qcharts.query");
            $connections = $queryService->getConnections();
            $options = [
                "connections" => $connections,
                "status" => 200,
                "textStatus" => "Connection names returned"
            ];
        }
        catch (\Exception $e)
        {
            $options = [
                "status"=>$e->getCode(),
                "textStatus"=> $e->getMessage(),
                "connections"=>[]
            ];
        }
        finally
        {
            return new JsonResponse($options);
        }

    }

    /**
     * @ApiDoc(
     * 		description="Run Action, returns the results directly from database",
     * 		parameters={
     * 			{
     * 				"name"="query",
     * 				"dataType"="string",
     * 				"required"=true,
     * 				"description"="The query to run in the database"
     * 			},
     * 			{
     * 				"name"="limit",
     * 				"dataType"="integer",
     * 				"required"=false,
     * 				"description"="The limit to use"
     * 			},
     *          {
     *              "name"="connection",
     *              "dataType"="string",
     *              "required"=false,
     *              "description"="Connection to query from, default connection is used if not passed"
     *          }
     * 		},
     *      statusCodes = {
     *          200 = "Returned successfully",
     *          402 = "Credentials not valid",
     *          500 = "Exception found and returned with error"
     *      }
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function runAction(Request $request)
    {
        $authChecker = $this->get('security.authorization_checker');

        /** @var QueryService $queryService */
        $queryService = $this->get('qcharts.query');

        $chartValidator = $this->get("qcharts.chart_validator");

        $roles = $this->getParameter('qcharts.user_roles');

        if (!$authChecker->isGranted($roles["admin"]))
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        $query = $request->request->get('query');
        try
        {
            $queryLimit = $request->request->get('limit', 0);
            $connection = $request->request->get('connection', 'default');

            $result = $queryService->getResultsFromQuery($query, $queryLimit, $connection);

            $isPieCompatible = false;
            try
            {
                $isPieCompatible = $chartValidator->resultsArePieCompatible($result["results"]);
                $isPieCompatible = $isPieCompatible && $chartValidator->resultsAreNumeric($result["results"], 'pie');
            }
            catch (ValidationFailedException $e)
            {
                $isPieCompatible = false;
            }
            finally
            {
                return new JsonResponse([
                    "status" => 200,
                    "originalQuery" => $query,
                    "textStatus" => "Results are returned",
                    "results" => $result["results"],
                    "lengthResults" => count($result["results"]),
                    "queryDuration" => $result["duration"],
                    "limit"=>$queryLimit,
                    "isPieChartCompatible" => $isPieCompatible,
                ]);
            }

        }
        catch (ParameterNotPassedException $e)
        {
            //return the error as an error
            return new JsonResponse([
                "status"=>500,
                "textStatus" => $e->getMessage(),
                "originalQuery" => $query,
            ]);
        }
        catch (DatabaseException $e)
        {
            return new JsonResponse([
                "status" => $e->getCode(),
                "textStatus"=>$e->getMessage()
            ]);
        }
        catch (ValidationFailedException $e)
        {
            return new JsonResponse([
                "status" => $e->getCode(),
                "textStatus"=>$e->getMessage(),
                "originalQuery"=>$query
            ]);
        }
        catch (\InvalidArgumentException $e)
        {
            return new JsonResponse([
                "status"=>500,
                "textStatus"=>$e->getMessage(),
                "originalQuery"=>$query
            ]);
        }
    }

}