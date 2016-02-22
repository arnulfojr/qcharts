<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/18/16
 * Time: 3:19 PM
 */

namespace QCharts\ApiBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use QCharts\CoreBundle\Exception\InstanceNotFoundException;
use QCharts\CoreBundle\Exception\UserRoleException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UserController extends Controller
{
    /**
     * @ApiDoc(
     *      description = "Returns the users and the information",
     *      parameters = {
     *          {
     *              "name" = "developer",
     *              "dataType" = "boolean",
     *              "required" = false,
     *              "description" = "Returns the users with the given role, not given returns all users"
     *          },
     *          {
     *              "name" = "_format",
     *              "dataType" = "string",
     *              "required" = false,
     *              "description" = "The response is by the requested format, options: json or xml"
     *          }
     *      },
     *      statusCodes = {
     *          200 = "Success",
     *          404 = "No users with the given query was found"
     *      }
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function getUsersAction(Request $request)
    {
        $authChecker = $this->get("security.authorization_checker");

        $roles = $this->getParameter('qcharts.user_roles');

        if (!$authChecker->isGranted($roles["super_admin"]))
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        $userService = $this->get("qcharts.user_service");
        try
        {
            $role = $request->query->get('developer', null);
            $encoding = $request->query->get('_format', 'json');

            $userService->setEncoding($encoding);
            $userService->setRole($role);
            $users = $userService->getDetails($this->getUser());

            $response = new Response($users);
            $response->headers->set('Content-Type', "application/{$userService->getEncoding()}");
            return $response;
        }
        catch(InstanceNotFoundException $e)
        {
            return new JsonResponse([
                "status"=>$e->getCode(),
                "text"=>$e->getMessage()
            ]);
        }
    }

    /**
     * @ApiDoc(
     *      description = "Promotes the given User with username",
     *      parameters = {
     *          {
     *              "name" = "username",
     *              "dataType" = "string",
     *              "required" = true,
     *              "description" = "Username to promote"
     *          }
     *      },
     *      statusCodes = {
     *          200 = "Success",
     *          201 = "Promotion was added",
     *          404 = "No users with the given query was found",
     *          500 = ""
     *      }
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function promoteAction(Request $request)
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter('qcharts.user_roles');

        if (!$authChecker->isGranted($roles["super_admin"]))
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        $options = [];
        try
        {
            $username = $request->request->get('username');
            $userService = $this->get("qcharts.user_service");
            $userService->promoteUser($username);
            $options = [
                "status" => 201,
                "textStatus"=>"User was successfully promoted"
            ];
        }
        catch (InstanceNotFoundException $e)
        {
            $options = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage(),
            ];
        }
        catch (UserRoleException $e)
        {
            $options = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage(),
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
     *      description = "Demotes the given User with username",
     *      parameters = {
     *          {
     *              "name" = "username",
     *              "dataType" = "string",
     *              "required" = true,
     *              "description" = "Username to demote"
     *          }
     *      },
     *      statusCodes = {
     *          200 = "Success",
     *          404 = "No users with the given query was found",
     *          405 = "User does not had the given role",
     *          500 = ""
     *      }
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function demoteAction(Request $request)
    {
        $authChecker = $this->get("security.authorization_checker");
        $roles = $this->getParameter('qcharts.user_roles');

        if (!$authChecker->isGranted($roles["super_admin"]))
        {
            return new JsonResponse(ApiController::getNotValidCredentials());
        }

        $options = [];
        $username = $request->request->get("username");
        try
        {
            $userService = $this->get("qcharts.user_service");
            $userService->demoteUser($username);
            $options = [
                "status" => 200,
                "textStatus"=>"User was successfully demoted",
                "username"=>$username
            ];
        }
        catch (InstanceNotFoundException $e)
        {
            $options = [
                "status"=>$e->getCode(),
                "textStatus"=>$e->getMessage(),
                "username"=>$username
            ];
        }
        catch (UserRoleException $e)
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
}