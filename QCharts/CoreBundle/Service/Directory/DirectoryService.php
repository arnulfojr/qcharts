<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/26/16
 * Time: 11:30 AM
 */

namespace QCharts\CoreBundle\Service\Directory;


use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;
use QCharts\CoreBundle\Entity\Directory;
use QCharts\CoreBundle\Exception\DatabaseException;
use QCharts\CoreBundle\Exception\DirectoryNotEmptyException;
use QCharts\CoreBundle\Exception\NotFoundException;
use QCharts\CoreBundle\Exception\OverlappingException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Repository\DirectoryRepository;
use QCharts\CoreBundle\Service\ServiceInterface\SerializationServiceInterface;
use Symfony\Component\Form\FormInterface;

class DirectoryService
{
    /** @var DirectoryRepository $repository */
    private $repository;
    /** @var SerializationServiceInterface $serializer */
    private $serializer;

    /**
     * DirectoryService constructor.
     * @param EntityRepository $repository
     * @param SerializationServiceInterface $serializationServiceInterface
     */
    public function __construct(
        EntityRepository $repository,
        SerializationServiceInterface $serializationServiceInterface
    ) {
        $this->repository = $repository;
        $this->serializer = $serializationServiceInterface;
    }

    /**
     * @param FormInterface $form
     * @throws DBALException
     * @throws \Exception
     */
    public function add(FormInterface $form)
    {
        /** @var Directory $directory */
        $directory = $form->getData();
        try
        {
            $this->repository->safeSave($directory);
        }
        catch (TypeNotValidException $e)
        {
            throw $e;
        }
        catch (DBALException $e)
        {
            throw $e;
        }

    }

    /**
     * @param FormInterface $form
     * @return int
     * @throws NotFoundException
     * @throws OverlappingException
     */
    public function edit(FormInterface $form)
    {
        try
        {
            /** @var Directory $dir */
            $dir = $form->getData();
            $this->repository->safeSave($dir);
            $parentId = ($dir->getParent()) ? $dir->getParent()->getId() : null;

            return $parentId;
        }
        catch(OverlappingException $e)
        {
            throw $e;
        }
    }

    /**
     * @param $directoryId
     * @return int|null
     * @throws DatabaseException
     * @throws DirectoryNotEmptyException
     * @throws NotFoundException
     */
    public function delete($directoryId)
    {
        try
        {
            $this->repository->directoryExists($directoryId);
            /** @var Directory $dir */
            $dir = $this->repository->find($directoryId);
            $parent = $dir->getParent();
            $parentId = ($parent) ? $parent->getId() : null;
            $this->repository->safeDelete($dir);
            return $parentId;
        }
        catch (NotFoundException $e)
        {
            throw $e;
        }
        catch (DirectoryNotEmptyException $e)
        {
            throw $e;
        }
        catch (DBALException $e)
        {
            $text = "Error encountered in the database: {$e->getMessage()}";
            throw new DatabaseException($text, $e->getCode(), $e);
        }
    }

    /**
     * @param $directoryId
     * @return bool
     */
    public function isDirectoryIdValid($directoryId)
    {
        $dir = $this->repository->find($directoryId);
        return !is_null($dir);
    }

    /**
     * @param $dirId
     * @return Directory
     */
    public function getDirectory($dirId)
    {
        return $this->repository->find($dirId);
    }

    /**
     * @param null $rootDirectoryId
     * @return array|mixed
     * @throws TypeNotValidException
     */
    public function getDirectories($rootDirectoryId = null)
    {
        $directories = $this->repository->getArchivesUnder($rootDirectoryId);

        $directories = [
            "directories" => $directories,
            "root" => (!is_null($rootDirectoryId)) ? $this->repository->find($rootDirectoryId) : null
        ];

        return $directories;
    }

}