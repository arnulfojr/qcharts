<?php

namespace QCharts\CoreBundle\Service\Favourite;


use QCharts\CoreBundle\Entity\QueryRequest;
use QCharts\CoreBundle\Entity\User\QChartsSubjectInterface;
use QCharts\CoreBundle\Exception\DatabaseException;
use QCharts\CoreBundle\Exception\OverlappingException;
use QCharts\CoreBundle\Repository\QueryRepository;
use QCharts\CoreBundle\Service\QueryService;

class FavouriteService
{
    /** @var QueryRepository $queryRepository */
    private $queryRepository;

    /**
     * FavouriteService constructor.
     * @param QueryRepository $queryRepository
     */
    public function __construct(QueryRepository $queryRepository)
    {
        $this->queryRepository = $queryRepository;
    }

    /**
     * @param QChartsSubjectInterface $user
     * @param QueryRequest $queryRequest
     * @throws DatabaseException
     * @throws OverlappingException
     */
    public function addFavourite(QChartsSubjectInterface $user, QueryRequest $queryRequest)
    {
        try
        {
            $queryRequest->addFavoritedBy($user);
            $this->queryRepository->update($queryRequest);
        }
        catch (OverlappingException $e)
        {
            throw new OverlappingException("The given query is already in the given context", 555, $e);
        }
        catch (DatabaseException $e)
        {
            throw new DatabaseException("Error while adding to favorites, {$e->getMessage()}", 500, $e);
        }

    }

    /**
     * @param QChartsSubjectInterface $user
     * @param QueryRequest $queryRequest
     * @throws DatabaseException
     */
    public function removeFavourite(QChartsSubjectInterface $user, QueryRequest $queryRequest)
    {
        try
        {
            $queryRequest->removeFavoritedBy($user);
            $this->queryRepository->update($queryRequest);
        }
        catch (\Exception $e)
        {
            throw new DatabaseException("Error while attempting to remove the favorite, {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * @param QChartsSubjectInterface $user
     * @return array
     * @throws DatabaseException
     */
    public function getFavourites(QChartsSubjectInterface $user)
    {
        try
        {
            $queryRequests = $this->queryRepository->getFavouritesBy($user);
            return $queryRequests;
        }
        catch (DatabaseException $e)
        {
            throw $e;
        }
    }

}