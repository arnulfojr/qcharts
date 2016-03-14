<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/10/16
 * Time: 3:08 PM
 */

namespace QCharts\ApiBundle\Controller;


use QCharts\ApiBundle\Exception\InvalidCredentialsException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UrlController extends Controller
{
    /**
     * @ApiDoc(
     *      description = "returns the URLs for Tables and Users"
     * )
     * @return JsonResponse
     */
    public function urlsAction()
    {
        //return the URLs for using the user admin
        $roles = $this->getParameter('qcharts.user_roles');
        $authChecker = $this->get("security.authorization_checker");
        $allow_demo_users = $this->getParameter('qcharts.allow_demo_users');

        try
        {
            ApiController::checkCredentials($authChecker, $roles, ApiController::USER, $allow_demo_users);

            $tableInfo = $this->generateUrl('qcharts.api.table_info');
            $tablesUrl = $this->generateUrl('qcharts.api.tables');
            $connections = $this->generateUrl('qcharts.api.connection_names');
            $connectionSchemas = $this->generateUrl('qcharts.api.connection_schemas');

            //$roleUrl = $this->generateUrl('qcharts.api.user_promote');
            $roleUrl = "";

            $frontendTableInfo = $this->generateUrl('qcharts.frontend.table_information');
            $frontendHomepage = $this->generateUrl('qcharts.frontend.homepage');
            $frontendBase = $this->generateUrl('qcharts.frontend.base');

            $directoryUrl = $this->generateUrl('qcharts.api.directory_get');
            $directoryQuery = $this->generateUrl('qcharts.api.directory_query');

            $snapshots = $this->generateUrl('qcharts.api.snapshot.get');
            $snapshotDownload = $this->generateUrl('qcharts.api.snapshot.snapshot_download');

            $favoritesGet = $this->generateUrl('qcharts.api.favorite_get');

            $options = [
                "status"=>200,
                "textStatus"=>"Urls returned",
                "urls"=>[
                    "frontend"=> [
                        "tableInfo" => $frontendTableInfo,
                        "homepage" => $frontendHomepage,
                        "base"=>$frontendBase
                    ],
                    "query" => $this->generateUrl('qcharts.api.query_get'),
                    "database"=>[
                        "tableInfo"=>$tableInfo,
                        "tables"=>$tablesUrl,
                        "connections"=>$connections,
                        "schemas"=>$connectionSchemas
                    ],
                    "snapshots" => [
                        "snapshots" => $snapshots,
                        "download"=>$snapshotDownload
                    ],
                    "favorite" => [
                        "base" => $favoritesGet
                    ],
                    "directory" => [
                        "base" => $directoryUrl,
                        "query" => $directoryQuery
                    ],
                    "user"=>[
                        "role"=>$roleUrl
                    ],
                    "chart"=> $this->generateUrl('qcharts.api.chart_data')
                ]
            ];

            return new JsonResponse($options);
        }
        catch (InvalidCredentialsException $e)
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }
    }
}