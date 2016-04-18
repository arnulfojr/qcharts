<?php

namespace QCharts\DevApiBundle\Controller;


use QCharts\ApiBundle\Exception\InvalidCredentialsException;
use QCharts\CoreBundle\Exception\InstanceNotFoundException;
use QCharts\CoreBundle\Exception\NotFoundException;
use QCharts\CoreBundle\Exception\SnapshotException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SnapshotController extends Controller
{
    /**
     *
     * @ApiDoc(
     *     description = "Returns the list of snapshots from the queried Query",
     *     parameters = {
     *          {
     *              "name" = "q",
     *              "required" = true,
     *              "description" = "Query Id",
     *              "dataType" = "string"
     *          }
     *     },
     *     statusCodes = {
     *          200 = "The snapshot list was returned",
     *          558 = "Query does not support snapshots",
     *          402 = "Credentials not valid",
     *          454 = "Snapshot directory not found, snapshot was not yet created"
     *     }
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function snapshotListAction(Request $request)
    {
        $authService = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        $allow_demo_users = $this->getParameter("qcharts.allow_demo_users");
        $options = [];
        try
        {
            ApiController::checkCredentials($authService, $roles, "user", $allow_demo_users);
            $snapshotService = $this->get("qcharts.core.snapshot_service");
            $queryService = $this->get("qcharts.query");
            $queryId = $request->query->get("q", null);
            if (is_null($queryId))
            {
                throw new InstanceNotFoundException("Query not passed", 404);
            }
            $queryRequest = $queryService->getQueryRequestById($queryId);
            $snapshots = $snapshotService->getSnapshotsFormatted($queryRequest);

            $options = [
                "status" => 200,
                "textStatus" => "Snapshots were returned",
                "snapshots" => $snapshots
            ];
        }
        catch (InvalidCredentialsException $e)
        {
            $options = [
                "status" => $e->getCode(),
                "textStatus" => $e->getMessage()
            ];
        }
        catch (InstanceNotFoundException $e)
        {
            $options = [
                "status" => $e->getCode(),
                "textStatus" => $e->getMessage()
            ];
        }
        catch (SnapshotException $e)
        {
            $options = [
                "status" => $e->getCode(),
                "textStatus" => $e->getMessage()
            ];
        }
        catch (IOException $e)
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
     *
     * @ApiDoc(
     *     description = "Download snapshot file",
     *     parameters = {
     *          {
     *              "name" = "q",
     *              "description" = "Query Request Id to get the desired snapshot",
     *              "dataType" = "integer",
     *              "required" = true
     *          }, {
     *              "name" = "snapshot",
     *              "description" = "The desired snapshot to download, if absent the most recent will be returned",
     *              "dataType" = "string",
     *              "required" = false
     *          }
     *     }, statusCodes = {
     *          402 = "Credentials not valid"
     *     }
     * )
     *
     * @param Request $request
     * @return JsonResponse|BinaryFileResponse
     */
    public function downLoadSnapshotAction(Request $request)
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        $allow_demo_users = $this->getParameter("qcharts.allow_demo_users");
        $response = null;

        try
        {
            ApiController::checkCredentials($authChecker, $roles, "user", $allow_demo_users);
            $snapshotService = $this->get("qcharts.core.snapshot_service");
            $queryService = $this->get("qcharts.query");

            $queryRequest = $request->query->get("q");
            $snapshotName = $request->query->get("snapshot", null);

            $queryRequest = $queryService->getQueryRequestById($queryRequest);
            $file = $snapshotService->getSnapshotFile($queryRequest, $snapshotName);

            $response =  new BinaryFileResponse($file);
            $response->trustXSendfileTypeHeader();
            $response->headers->set("Content-Type", "text/csv");
            $fileName = substr($file->getFilename(), 0, -4);
            $fileName = "{$snapshotService->formatSnapshotName($queryRequest, $fileName)}.csv";
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                $fileName,
                iconv('UTF-8', 'ASCII//TRANSLIT', $fileName));
        }
        catch (InvalidCredentialsException $e)
        {
            $response = new JsonResponse(ApiController::getNotValidCredentials());
        }
        catch (InstanceNotFoundException $e)
        {
            $response = new JsonResponse([
                "status" => $e->getCode(),
                "textStatus" => $e->getMessage()
            ]);
        }
        catch (SnapshotException $e)
        {
            $response = new JsonResponse([
                "status" => $e->getCode(),
                "textStatus" => $e->getMessage()
            ]);
        }
        catch(NotFoundException $e)
        {
            $response = new JsonResponse([
                "status" => $e->getCode(),
                "textStatus" => $e->getMessage()
            ]);
        }
        finally
        {
            return $response;
        }
    }

    /**
     * @ApiDoc(
     *     description = "Deletes the requested snapshot file",
     *     parameters = {
     *          {
     *              "name" = "snapshot",
     *              "dataType" = "integer",
     *              "description" = "The snapshot name to delete",
     *              "required" = true
     *          },
     *          {
     *              "name" = "q",
     *              "dataType" = "integer",
     *              "description" = "Query request that has the snapshot",
     *              "required" = true
     *          }
     *     },
     *     statusCodes = {
     *          202 = "Snapshot deleted",
     *          402 = "Credentials invalid"
     *     }
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteSnapshotAction(Request $request)
    {
        $options = [];
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter("qcharts.user_roles");
        try
        {
            ApiController::checkCredentials($authChecker, $roles, ApiController::SUPER_ADMIN);

            $snapshotService = $this->get("qcharts.core.snapshot_service");
            $queryService = $this->get("qcharts.query");

            $snapshotId = $request->request->get("snapshot", null);
            $queryId = $request->request->get("q", null);

            $queryRequest = $queryService->getQueryRequestById($queryId);
            $snapshot = $snapshotService->getSnapshotFile($queryRequest, $snapshotId);

            $snapshotService->deleteSnapshot($queryRequest, $snapshot);

            $options = [
                "status" => 202,
                "textStatus" => "The snapshot was deleted"
            ];
        }
        catch (InvalidCredentialsException $e)
        {
            $options = ApiController::getNotValidCredentials();
        }
        catch (SnapshotException $e)
        {
            $options = [
                "status" => $e->getCode(),
                "textStatus" => $e->getMessage()
            ];
        }
        catch (InstanceNotFoundException $e)
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

}