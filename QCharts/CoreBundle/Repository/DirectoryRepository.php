<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/26/16
 * Time: 9:42 AM
 */

namespace QCharts\CoreBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use QCharts\CoreBundle\Entity\Directory;
use QCharts\CoreBundle\Exception\DirectoryNotEmptyException;
use QCharts\CoreBundle\Exception\Messages\ExceptionMessage;
use QCharts\CoreBundle\Exception\NotFoundException;
use QCharts\CoreBundle\Exception\OverlappingException;
use QCharts\CoreBundle\Exception\TypeNotValidException;

class DirectoryRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getTreeDirectory()
    {
        $query = $this->createQueryBuilder('d');
        $query = $query->getQuery();
        // TODO: finish the implementation
        $results = $query->getResult();

        return $results;
    }

    /**
     * @param $directoryId
     * @return bool
     * @throws DirectoryNotEmptyException
     * @throws NotFoundException
     */
    public function directoryHasFiles($directoryId)
    {
        $this->directoryExists($directoryId);

        /** @var QueryRepository $qrRepo */
        $qrRepo = $this->getEntityManager()->getRepository('CoreBundle:QueryRequest');
        $requests = $qrRepo->getQueriesInDirectory($directoryId);

        if ($requests && count($requests) > 0) {
            return true;
        }
        throw new DirectoryNotEmptyException(ExceptionMessage::DIRECTORY_NOT_EMPTY_TEXT, 500);
    }

    /**
     * @param $directoryId
     * @return bool
     * @throws DirectoryNotEmptyException
     * @throws NotFoundException
     */
    public function directoryIsEmpty($directoryId)
    {
        $this->directoryExists($directoryId);
        /** @var QueryRepository $qrRepo */
        $qrRepo = $this->getEntityManager()->getRepository("CoreBundle:QueryRequest");
        $requests = $qrRepo->getQueriesInDirectory($directoryId);

        $dirs = $this->getArchivesUnder($directoryId);
        if (count($requests) > 0 || count($dirs) > 0) {
            throw new DirectoryNotEmptyException(ExceptionMessage::DIRECTORY_NOT_EMPTY_TEXT, 500);
        }
        return true;
    }

    /**
     * @param $directoryId
     * @return bool
     * @throws NotFoundException
     */
    public function directoryExists($directoryId)
    {
        $dir = $this->find($directoryId);
        if ($dir || !is_null($dir))
        {
            return true;
        }
        throw new NotFoundException(ExceptionMessage::DIRECTORY_NOT_FOUND("with directory id: {$directoryId}"), 404);
    }

    /**
     * @param null $directoryId
     * @return array
     * @throws TypeNotValidException
     */
    public function getArchivesUnder($directoryId = null)
    {
        $query = $this->getQueryForRoot($directoryId);
        $directories = $query->getResult();
        return $directories;

    }

    /**
     * @param null $directoryId
     * @return \Doctrine\ORM\Query
     * @throws NotFoundException
     * @throws TypeNotValidException
     */
    protected function getQueryForRoot($directoryId = null)
    {
        // TODO: refactor this!
        if ($directoryId && !is_null($directoryId))
        {
            if (is_numeric($directoryId))
            {
                if ($this->directoryExists($directoryId))
                {
                    $queryBuilder = $this->createQueryBuilder('d');
                    $queryBuilder
                        ->where('d.parent = ?1')
                        ->setParameter(1, $directoryId);
                    return $queryBuilder->getQuery();
                }
            }
            throw new TypeNotValidException(ExceptionMessage::TYPE_NOT_VALID("directory id has to be numeric"), 500);
        }

        return $this->createQueryBuilder('d')->where('d.parent is null')->getQuery();
    }

    /**
     * @param $name
     * @param null $parentId
     * @return bool
     * @throws OverlappingException
     */
    public function nameExists($name, $parentId = null)
    {
        $kids = $this->getArchivesUnder($parentId);
        foreach ($kids as $kid)
        {
            /** @var Directory $kid */
            if ($kid->getName() === $name)
            {
                throw new OverlappingException(ExceptionMessage::NAME_OVERLAPPING_TEXT, 500);
            }
        }
        return false;
    }

    /**
     * @param Directory $directory
     * @param bool|true $autoFlush
     * @throws OverlappingException
     */
    public function safeSave(Directory $directory, $autoFlush = true)
    {
        //get the directories that have the same parent, check if they have the same name
        // or query for the dirs that have the same name with the same parent!
        // if nothing then save it
        $parentToCheck = $directory->getParent();
        $parentID = ($parentToCheck) ? $parentToCheck->getId() : null;
        $siblings = $this->getArchivesUnder($parentID);

        foreach ($siblings as $sibling)
        {
            /** @var Directory $sibling */
            if ($sibling->getName() === $directory->getName() && $directory !== $sibling)
            {
                throw new OverlappingException(ExceptionMessage::NAME_OVERLAPPING("name: {$directory->getName()} vs {$sibling->getName()}"), 500);
            }
        }
        $this->save($directory, $autoFlush);
    }

    /**
     * @param Directory $directory
     * @param bool|true $autoFlush
     */
    public function save(Directory $directory, $autoFlush = true)
    {
        $this->getEntityManager()->persist($directory);
        if ($autoFlush)
        {
            $this->flush();
        }
    }

    /**
     * @param Directory $directory
     * @param bool $autoFlush
     * @throws DirectoryNotEmptyException
     */
    public function safeDelete(Directory $directory, $autoFlush = true)
    {
        if ($this->directoryIsEmpty($directory->getId())) {
            $this->delete($directory, $autoFlush);
        }
    }

    /**
     * @param Directory $directory
     * @param bool|true $autoFlush
     */
    public function delete(Directory $directory, $autoFlush = true)
    {
        $this->getEntityManager()->remove($directory);
        if ($autoFlush)
        {
            $this->flush();
        }
    }

    /**
     *  Flushes the EntityManager
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }

}