<?php

namespace QCharts\CoreBundle\Tests\Service;

use \PHPUnit_Framework_TestCase as Unit_TestCase;
use QCharts\CoreBundle\Entity\ChartConfig;
use QCharts\CoreBundle\Entity\Query;
use QCharts\CoreBundle\Entity\QueryRequest;
use QCharts\CoreBundle\Entity\User\QChartsSubjectInterface;
use QCharts\CoreBundle\Repository\DynamicRepository;
use QCharts\CoreBundle\Repository\QueryRepository;
use QCharts\CoreBundle\Service\QueryService;
use QCharts\CoreBundle\Service\QueryValidatorService;
use Symfony\Component\Form\Form;
use \SqlFormatter;

class ClassNames
{
    const SERVICE_NAMESPACE = 'QCharts\CoreBundle\Service';

    const LIMITS_SERVICE = 'QCharts\CoreBundle\Service\LimitsService';

    const QUERY_SERVICE = 'QCharts\CoreBundle\Service\QueryService';

    const QUERY_VALIDATOR_SERVICE = 'QCharts\CoreBundle\Service\QueryValidatorService';

    const QUERY_REQUEST = 'QCharts\CoreBundle\Entity\QueryRequest';

    const QUERY = 'QCharts\CoreBundle\Entity\Query';

    const CHART_CONFIG = 'QCharts\CoreBundle\Entity\ChartConfig';

    const QUERY_REPOSITORY = 'QCharts\CoreBundle\Repository\QueryRepository';

    const DYNAMIC_REPOSITORY = 'QCharts\CoreBundle\Repository\DynamicRepository';

    const QUERY_SYNTAX_SERVICE = 'QCharts\CoreBundle\Service\QuerySyntaxService';

    const QCHARTS_USER_INTERFACE = 'QCharts\CoreBundle\Entity\User\QChartsSubjectInterface';

    const SYMFONY_FORM = 'Symfony\Component\Form\Form';
}

class QueryServiceTest extends Unit_TestCase
{

    /**
     * Tests a query of a valid Query Id
     */
    public function testGetQueryRequestById()
    {
        //Get Mocks
        $qrRepositoryMock = $this->prophesize(ClassNames::QUERY_REPOSITORY);
        $dynamicRepoMock = $this->prophesize(ClassNames::DYNAMIC_REPOSITORY);
        $queryValidatorMock = $this->prophesize(ClassNames::QUERY_VALIDATOR_SERVICE);
        $queryRequestMock = $this->prophesize(ClassNames::QUERY_REQUEST);

        //parameters:
        $queryRequestId = 40;

        //Config
        /** @var QueryRequest $queryRequest */
        $queryRequest = $queryRequestMock->reveal();

        $qrRepositoryMock
            ->find($queryRequestId)
            ->willReturn($queryRequest)
            ->shouldBeCalled();
        /** @var QueryRepository $qrRepository */
        $qrRepository = $qrRepositoryMock->reveal();

        /** @var DynamicRepository $dynamicRepo */
        $dynamicRepo = $dynamicRepoMock->reveal();
        /** @var QueryValidatorService $queryValidator */
        $queryValidator = $queryValidatorMock->reveal();

        //let's role!
        $queryService = new QueryService($qrRepository, $dynamicRepo, $queryValidator);
        $returned = $queryService->getQueryRequestById($queryRequestId);
        $this->assertEquals($queryRequest, $returned);
	}

    /** @noinspection PhpUndefinedNamespaceInspection */
    /**
     * Tests a call to an invalid QueryRequest Id
     * @expectedException QCharts\CoreBundle\Exception\InstanceNotFoundException
     */
	public function testGetQueryRequestByIdException()
	{
        //Mocks:
        $qrRepositoryMock = $this->prophesize(ClassNames::QUERY_REPOSITORY);
        $dynamicRepoMock = $this->prophesize(ClassNames::DYNAMIC_REPOSITORY);
        $queryValidatorMock = $this->prophesize(ClassNames::QUERY_VALIDATOR_SERVICE);
        $queryRequestMock = $this->prophesize(ClassNames::QUERY_REQUEST);

        //config
        $queryRequestId = 40;

        $qrRepositoryMock
            ->find($queryRequestId)
            ->willReturn(null)
            ->shouldBeCalled();

        //reveal objects:
        /** @var QueryRequest $queryRequest */
        $queryRequest = $queryRequestMock->reveal();
        /** @var QueryRepository $qrRepository */
        $qrRepository = $qrRepositoryMock->reveal();
        /** @var DynamicRepository $dynamicRepo */
        $dynamicRepo = $dynamicRepoMock->reveal();
        /** @var QueryValidatorService $queryValidator */
        $queryValidator = $queryValidatorMock->reveal();

        //setUp service!
        $queryService = new QueryService(
            $qrRepository,
            $dynamicRepo,
            $queryValidator
        );

        $queryService->getQueryRequestById($queryRequestId);
	}

    /** @noinspection PhpUndefinedNamespaceInspection */
    /**
     * @expectedException QCharts\CoreBundle\Exception\InstanceNotFoundException
     */
    public function testDeleteException()
    {
        //Mocks:
        $qrRepositoryMock = $this->prophesize(ClassNames::QUERY_REPOSITORY);
        $dynamicRepoMock = $this->prophesize(ClassNames::DYNAMIC_REPOSITORY);
        $queryValidatorMock = $this->prophesize(ClassNames::QUERY_VALIDATOR_SERVICE);

        //config
        $queryRequestID = 40;

        $qrRepositoryMock
            ->find($queryRequestID)
            ->willReturn(null)
            ->shouldBeCalled();

        //reveal objects:
        /** @var QueryRepository $qrRepository */
        $qrRepository = $qrRepositoryMock->reveal();
        /** @var DynamicRepository $dynamicRepo */
        $dynamicRepo = $dynamicRepoMock->reveal();
        /** @var QueryValidatorService $queryValidator */
        $queryValidator = $queryValidatorMock->reveal();

        //Let's roll
        $queryService = new QueryService(
            $qrRepository,
            $dynamicRepo,
            $queryValidator
        );

        $queryService->delete($queryRequestID);
    }

    public function testOffLimitsAdd()
    {
        //Mocks:
        $qrRepositoryMock = $this->prophesize(ClassNames::QUERY_REPOSITORY);
        $dynamicRepoMock = $this->prophesize(ClassNames::DYNAMIC_REPOSITORY);
        $queryValidatorMock = $this->prophesize(ClassNames::QUERY_VALIDATOR_SERVICE);

        $formMock = $this->prophesize(ClassNames::SYMFONY_FORM);
        $userMock = $this->prophesize(ClassNames::QCHARTS_USER_INTERFACE);
        $queryRequestMock = $this->prophesize(ClassNames::QUERY_REQUEST);
        $queryMock = $this->prophesize(ClassNames::QUERY);
        $chartConfigMock = $this->prophesize(ClassNames::CHART_CONFIG);

        //reveal
        /** @var QueryRepository $qrRepository */
        $qrRepository = $qrRepositoryMock->reveal();
        /** @var DynamicRepository $dynamicRepo */
        $dynamicRepo = $dynamicRepoMock->reveal();
        /** @var QueryValidatorService $queryValidator */
        $queryValidator = $queryValidatorMock->reveal();
        /** @var Form $form */
        $form = $formMock->reveal();
        /** @var Query $query */
        $query = $queryMock->reveal();
        /** @var ChartConfig $chartConfig */
        $chartConfig = $chartConfigMock->reveal();
        /** @var QChartsSubjectInterface $user */
        $user = $userMock->reveal();
        /** @var QueryRequest $queryRequest */
        $queryRequest = $queryRequestMock->reveal();

        //config
        $queryString = 'SELECT * FROM Master;';
        $formattedQueryString = SqlFormatter::format($queryString);
        $chartTypeString = "line";
        $dbConnectionName = "default";
        $cronExpressionString = "*/5 * * * *";
        $rowLimit = 1500; //rows
        $timeLimit = 2; //seconds
        $offset = 0; //rows
        $config = [
            "time"=>$timeLimit,
            "rows"=>$rowLimit,
            "chartType"=>$chartTypeString,
            "offset"=>$offset,
            "connection"=>$dbConnectionName,
            "cronExpression"=>$cronExpressionString
        ];

        $formMock
            ->getData()
            ->willReturn($queryRequest)
            ->shouldBeCalled();

        $queryRequestMock
            ->getQuery()
            ->willReturn($query)
            ->shouldBeCalled();

        $queryRequestMock
            ->getCronExpression()
            ->willReturn($cronExpressionString);

        $queryRequestMock
            ->getConfig()
            ->willReturn($chartConfig)
            ->shouldBeCalled();

        $queryRequestMock
            ->setCreatedBy($user)
            ->shouldBeCalled();

        $queryRequestMock
            ->setModifiedLastBy($user)
            ->shouldBeCalled();

        $queryRequestMock
            ->setQuery($query)
            ->shouldBeCalled();

        $queryRequestMock
            ->setConfig($chartConfig)
            ->shouldBeCalled();

        $queryMock
            ->getQuery()
            ->willReturn($queryString);

        $queryMock
            ->setQueryHTML($formattedQueryString)
            ->shouldBeCalled();

        $queryMock
            ->getQueryHTML()
            ->willReturn($formattedQueryString);

        $chartConfigMock
            ->getExecutionLimit()
            ->willReturn($timeLimit);

        $chartConfigMock
            ->getQueryLimit()
            ->willReturn($rowLimit);

        $chartConfigMock
            ->getTypeOfChart()
            ->willReturn($chartTypeString);

        $chartConfigMock
            ->getOffset()
            ->willReturn($offset);

        $chartConfigMock
            ->getDatabaseConnection()
            ->willReturn($dbConnectionName);

        $queryValidatorMock
            ->isValidQuery($queryString, $dbConnectionName)
            ->willReturn(true);

        $queryValidatorMock
            ->validateQueryExecution($queryString, $config)
            ->willReturn(true);

        //Let's roll!
        $queryService = new QueryService(
            $qrRepository,
            $dynamicRepo,
            $queryValidator
        );

        $queryService->add($form, $user);

    }

}