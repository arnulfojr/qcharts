<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/8/16
 * Time: 11:24 AM
 */

namespace QCharts\ApiBundle\Controller;


use QCharts\ApiBundle\Exception\InvalidCredentialsException;
use QCharts\CoreBundle\Exception\InstanceNotFoundException;
use QCharts\CoreBundle\Exception\OverlappingException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class FavoriteController extends Controller
{

    /**
     * @ApiDoc(
     *     description="Returns the favorite Query from the current user",
     *     statusCodes = {
     *          200 = "Returns the favorites from the current user"
     *     }
     * )
     * @return JsonResponse
     * @throws \QCharts\ApiBundle\Exception\InvalidCredentialsException
     */
    public function getFavoritesAction()
    {
        $roles = $this->getParameter("qcharts.user_roles");
        $authService = $this->get("security.authorization_checker");
        $favService = $this->get("qcharts.core.favorite_service");

        $options = [];

        try
        {
            ApiController::checkCredentials($authService, $roles, "user");

            $user = $this->getUser();
            $favorites = $favService->getFavourites($user);
            $options = [
                "status" => 200,
                "textStatus" => "favorites returned",
                "favorites" => $favorites,
                "count" => count($favorites)
            ];
        }
        catch (InvalidCredentialsException $e)
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
     *     description = "Adds the given Query as a favorite of the current user",
     *     parameters = {
     *          {
     *              "name" = "q",
     *              "required" = true,
     *              "dataType" = "integer",
     *              "description" = "The Id from the query request to favorite"
     *          }
     *     },
     *     statusCodes = {
     *          201 = "Description of the favorites returned",
     *          402 = "Invalid credentials",
     *          500 = "Error while attempting to add the favorite to the user",
     *          555 = "The given query is already in the same context"
     *
     *     }
     * )
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function registerFavoriteAction(Request $request)
    {
        $roles = $this->getParameter("qcharts.user_roles");
        $authService = $this->get("security.authorization_checker");
        $favService = $this->get("qcharts.core.favorite_service");
        $queryService = $this->get("qcharts.query");

        $options = [];

        try
        {
            ApiController::checkCredentials($authService, $roles, "user");

            $user = $this->getUser();
            $queryId = $request->request->get("q", null);
            // query request to add to favorite!
            $queryRequest = $queryService->getQueryRequestById($queryId);
            $favService->addFavourite($user, $queryRequest);
            $options = [
                "status" => 201,
                "textStatus" => "The query was added to the favorites"
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
     * @ApiDoc(
     *     description = "Removes the given query request from the favorites list of the user",
     *     parameters = {
     *          {
     *              "name" = "q",
     *              "required" = true,
     *              "dataType" = "integer",
     *              "description" = "The query Id to remove from the user's list"
     *          }
     *     },
     *     statusCodes = {
     *          202 = "The query was removed from the user's favorite list",
     *          500 = "Error while attempting to remove the query form the user's favorite list"
     *     }
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function removeFavoriteAction(Request $request)
    {
        $roles = $this->getParameter("qcharts.user_roles");
        $authService = $this->get("security.authorization_checker");

        $options = [];

        try
        {
            ApiController::checkCredentials($authService, $roles, "user");
            $user = $this->getUser();
            $queryId = $request->request->get("q", null);
            $queryService = $this->get("qcharts.query");
            $favService = $this->get("qcharts.core.favorite_service");

            $queryRequest = $queryService->getQueryRequestById($queryId);
            $favService->removeFavourite($user, $queryRequest);

            $options = [
                "status" => 202,
                "textStatus" => "The favorite was removed from the user's list"
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