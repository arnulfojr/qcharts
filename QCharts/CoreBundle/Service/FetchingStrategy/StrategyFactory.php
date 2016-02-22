<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/3/16
 * Time: 4:46 PM
 */

namespace QCharts\CoreBundle\Service\FetchingStrategy;


use QCharts\CoreBundle\Exception\Messages\ExceptionMessage;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Service\QueryValidatorService;
use QCharts\CoreBundle\Service\Snapshot\SnapshotService;

class StrategyFactory implements FetchingStrategyFactoryInterface
{
    /** @var QueryValidatorService $queryValidator */
    private $queryValidator;
    /** @var SnapshotService $snapshotService */
    private $snapshotService;

    /**
     * StrategyFactory constructor.
     * @param SnapshotService $snapshotService
     */
    public function __construct(SnapshotService $snapshotService = null)
    {
        $this->snapshotService = $snapshotService;

        if (!is_null($snapshotService))
        {
            $this->queryValidator = $this->snapshotService->getQueryValidator();
        }
    }

    /**
     * @return QueryValidatorService
     */
    public function getQueryValidator()
    {
        return $this->queryValidator;
    }

    /**
     * @param $queryValidator
     */
    public function setQueryValidator($queryValidator)
    {
        $this->queryValidator = $queryValidator;
    }

    /**
     * @return SnapshotService
     */
    public function getSnapshotService()
    {
        return $this->snapshotService;
    }

    /**
     * @param $snapshotService
     */
    public function setSnapshotService($snapshotService)
    {
        $this->snapshotService = $snapshotService;
    }

    /**
     * @return array
     */
    public static function getModes()
    {
        return ["Live", "Snapshot", "Time Machine"];
    }

    /**
     * @param $mode
     * @param null $snapshot
     * @return null|LiveStrategy|SnapshotStrategy
     * @throws TypeNotValidException
     */
    public function createStrategy($mode, $snapshot = null)
    {
        StrategyFactory::validateMode($mode);

        $strategy = null;

        switch ($mode)
        {
            case SnapshotService::LIVE_MODE:
                $strategy = new LiveStrategy();
                $strategy->setDependencies(["QueryValidator"=>$this->queryValidator]);
                break;
            case SnapshotService::CACHE_MODE:
            case SnapshotService::TIME_MACHINE_MODE:
                $strategy = new SnapshotStrategy();
                $strategy->setDependencies([
                    "SnapshotService"=>$this->snapshotService,
                    "snapshot" => $snapshot
                ]);
                break;
            default:
                break;
        }

        return $strategy;
    }

    /**
     * @param $mode
     * @return bool
     * @throws TypeNotValidException
     */
    public static function validateMode($mode)
    {
        $exists = array_key_exists($mode, StrategyFactory::getModes());
        if (!$exists)
        {
            $description = ExceptionMessage::TYPE_NOT_VALID("the mode given ({$mode}) was not valid");
            throw new TypeNotValidException($description, 500);
        }

        return true;
    }

}