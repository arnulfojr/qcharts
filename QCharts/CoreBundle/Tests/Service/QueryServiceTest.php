<?php

namespace QCharts\CoreBundle\Tests\Service;

use QCharts\CoreBundle\Service\QueryService;
use Prophecy\Prophet;

class QueryServiceTest extends \PHPUnit_Framework_TestCase
{

	private $prophet;

	public function testGetQueryRequestById()
	{
		//test for getting the QueryRequest
		//mock the queryRequest
		$qrProphecy = $this->getQueryRequestMock();
        $qr = $qrProphecy->reveal();
		$qrRepository = $this->getQueryRequestRepository();
		$qrRepository->find(40)->willReturn($qr);
        $qrRepository = $qrRepository->reveal();

		$serMock = $this->getSerializationMock();
        $ser = $serMock->reveal();
		$queryValidator = $this->getQueryValidatorMock();
        $queryValidator = $queryValidator->reveal();
        $formEntityMock = $this->getFormEntityAdapter();
        $formEntity = $formEntityMock->reveal();

        $chartValMock = $this->getChartValidationMock();
        $chartVal = $chartValMock->reveal();

		$qrService = new QueryService($qrRepository, $ser, $queryValidator, $formEntity, $chartVal);

        $this->assertEquals($qr, $qrService->getQueryRequestById(40));
	} 

	/**
	* @expectedException Exception
	*/

	public function testGetQueryRequestByIdException()
	{
		$qr = $this
			->getQueryRequestMock();

		$qrRepo = $this->getQueryRequestRepository();
        $qrRepo->find()->willReturn(null);

		$serMock = $this->getSerializationMock();
		$queryValidatorMock = $this->getQueryValidatorMock();
        $formEntityAdapterMock = $this->getFormEntityAdapter();
        $formEntityAdapter = $formEntityAdapterMock->reveal();
        $queryValidator = $queryValidatorMock->reveal();

        $chartValMock = $this->getChartValidationMock();
        $chartVal = $chartValMock->reveal();

        $qrService = new QueryService($qrRepo->reveal(), $serMock->reveal(), $queryValidator, $formEntityAdapter, $chartVal);
		$this->assertEquals($qr, $qrService->getQueryRequestById(100));
	}


	/**
	*
	* @expectedException \Exception
	*/

	public function testDeleteException()
	{
		$queryRequest = $this->prophesize('CoreBundle\Entity\QueryRequest');
		$repository = $this->prophesize('\Doctrine\ORM\EntityRepository');
		$repository->delete($queryRequest)->willReturn(null);
		
		$serializationMock = $this->prophet->prophesize('CoreBundle\Service\SerializationService');
		$formEntMock = $this->getFormEntityAdapter();
        $formEntityAdapter = $formEntMock->reveal();

        $repository = $repository->reveal();

        $chartValMock = $this->getChartValidationMock();

		$qService = new QueryService(
				$repository,
				$serializationMock->reveal(),
				$this->getQueryValidatorMock()->reveal(),
                $formEntityAdapter,
                $chartValMock->reveal()
            );
		$this->assertEquals($qService->deleteQuery($queryRequest->reveal()), true);
	}

    protected function getQuerySyntaxMock()
    {
        return $this->prophesize('CoreBundle\Service\QuerySyntaxService');
    }

	protected function getQueryRequestMock()
    {
        $qr = $this->prophesize('CoreBundle\Service\QueryRequest');
        return $qr;
    }

    protected function getFormEntityAdapter()
    {
        return $this->prophesize('CoreBundle\Service\FormEntityAdapter');
    }

	protected function getSerializationMock()
	{
		return $this->prophesize('CoreBundle\Service\SerializationService');
	}
	
	protected function getQueryValidatorMock()
	{
		return $this->prophesize('CoreBundle\Service\QueryValidatorService');
	}
	
	protected function getQueryRequestRepository()
	{
		$qrRepo = $this->prophesize('CoreBundle\Repository\QueryRepository');
		return $qrRepo;
	}

	protected function getChartValidationMock()
	{
		$mock = $this->prophesize('CoreBundle\Service\ChartValidation');
		return $mock;
	}

	public function setUp()
	{
		$this->prophet = new Prophet();
	}
	
	public function tearDown()
	{
		$this->prophet->checkPredictions();
	}
	
}