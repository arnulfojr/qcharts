<?php

namespace QCharts\CoreBundle\Tests\Service;

use QCharts\CoreBundle\Repository\QueryRepository;
use QCharts\CoreBundle\Service\QuerySyntaxService;
use QCharts\CoreBundle\Service\QueryValidatorService;
use Prophecy\Prophet;
use SebastianBergmann\RecursionContext\Exception;

class QueryValidatorServiceTest extends \PHPUnit_Framework_TestCase
{
	/** @var  Prophet */
	private $prophet;
	
	/**
	 * {@inheritDoc}
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp()
	{
		$this->prophet = new Prophet();
	}
	
	/**
	 * {@inheritDoc}
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	public function tearDown()
	{
		$this->prophet->checkPredictions();
	}

    /**
     * @dataProvider tableNameProvider
     * @param array $list
     * @param $expectedString
     */
	public function testGetTableNameString(array $list, $expectedString)
	{
		$repo = $this->getQueryRequestRepository();
        $repo = $repo->reveal();
        $syxSer = $this->getQuerySyntaxServiceMock();
        $syxSer = $syxSer->reveal();
        /** @var QueryRepository $repo */
        $cvMock = $this->getChartValidationMock();
        $cv = $cvMock->reveal();
		$service = new QueryValidatorService($repo, $syxSer, $cv);
		$this->assertEquals($expectedString, $service->getTableNameStringForRegex($list));
	}

    /**
     * @dataProvider queryProvider
     * @param $queryString
     * @throws \Exception
     */
	public function testQueryIsReadOnly($queryString)
    {
		$qrRepo = $this->getQueryRequestRepository();
		$syxSer = $this->getQuerySyntaxServiceMock();
        $qrRepo = $qrRepo->reveal();
        $syxSer = $syxSer->reveal();
        /** @var QueryRepository $qrRepo */
        $cvMock = $this->getChartValidationMock();
        $cv = $cvMock->reveal();
        /** @var QuerySyntaxService $qrService */
		$qrService = new QueryValidatorService($qrRepo, $syxSer, $cv);
		$this->assertTrue($qrService->queryIsReadOnly($queryString));
	}

    /**
     * @param $qrString
     * @param $tableNames
     * @throws \Exception
     * @dataProvider queryTableNamesProvider
     * @expectedException Exception
     */
	public function testQueryContainsAValidTableWithTableNames($qrString, $tableNames)
	{
		$qrRepo = $this->getQueryRequestRepository();
		$sxSerMock = $this->getQuerySyntaxServiceMock();
        $sxSer = $sxSerMock->reveal();
        $qrRepo = $qrRepo->reveal();
        $cvMock = $this->getChartValidationMock();
        $cv = $cvMock->reveal();
		$qrService = new QueryValidatorService($qrRepo, $sxSer, $cv);
		$this->assertTrue($qrService->queryContainsAValidTableWithTableNames($qrString, $tableNames));
	}

    /**
     * @dataProvider testIsAValidQueryProvider
     * @expectedException \Exception
     * @param $qrString
     * @param $tableNames
     */
	public function testIsAValidQuery($qrString, $tableNames)
    {
		$qrRepo = $this->getQueryRequestRepository();
		$qrRepo = $qrRepo->reveal();
        $sxMock = $this->getQuerySyntaxServiceMock();
        $sx = $sxMock->reveal();
        $cvMock = $this->getChartValidationMock();
        $cv = $cvMock->reveal();
		$qrService = new QueryValidatorService($qrRepo, $sx, $cv);
		$this->assertTrue($qrService->isValidQueryWithTableNames($qrString, $tableNames));
	}


    /**
     * @dataProvider isDurationValidGoodTest
     * @expectedException \Exception
     * @param $duration
     * @throws \Exception
     */
    public function testIsDurationValid($duration)
    {
        $repo = $this->getQueryRequestRepository();
        $repo = $repo->reveal();
        $syMock = $this->getQuerySyntaxServiceMock();
        $sy = $syMock->reveal();
        $cvMock = $this->getChartValidationMock();
        $cv = $cvMock->reveal();
        $service = new QueryValidatorService($repo, $sy, $cv);

        $this->assertEquals(10, $service->isDurationValid($duration));
    }

    /**
     * @dataProvider regexAndPatternProvider
     * @param $stringArray
     * @param $pattern
     */
    public function testStringHasRegex($stringArray, $pattern)
    {
        $repoMock = $this->getQueryRequestRepository();
        $repo = $repoMock->reveal();
        $syntaxMock = $this->getQuerySyntaxServiceMock();
        $syntax = $syntaxMock->reveal();
        $cvMock = $this->getChartValidationMock();
        $cv = $cvMock->reveal();
        $service = new QueryValidatorService($repo, $syntax, $cv);
        $this->assertTrue($service->stringHasInstancesFromRegex($stringArray, $pattern));
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
	protected function getQueryRequestRepository()
    {
		return $this->prophesize('QCharts\CoreBundle\Repository\QueryRepository');
	}

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
	protected function getSerializationMock()
    {
		return $this->prophesize('QCharts\CoreBundle\Service\SerializationService');
	}

	/**
	 * @return \Prophecy\Prophecy\ObjectProphecy
	 */
	protected function getChartValidationMock()
	{
		return $this->prophesize('QCharts\CoreBundle\Service\ChartValidation');
	}

    protected function getQuerySyntaxServiceMock()
    {
        return $this->prophesize('QCharts\CoreBundle\Service\QuerySyntaxService');
    }

    public function regexAndPatternProvider()
    {
        return [
            [
                ["this is a test for sopme text that contains a join on on it!"], "/(join on)/"
            ],
            [
                ["This is not crazy but I need some from help over here"], "/ from (help|not)/"
            ]
        ];
    }

	public function queryProvider ()
    {
		return [
				['SELECT yearID, count(*) FROM salaries GROUP BY yearID;'],
				["SELECT date_trunc('month', rated_at):: date AS month, gender, COUNT(*)
			FROM query INNER JOIN users ON ratings.user_id = users.id GROUP BY month, gender ORDER BY month, gender"],
		];
	}
	
	public function invalidQueryProvider()
    {
		return [
				["INSERT INTO Customers (CustomerName, ContactName, Address, City, PostalCode, Country) VALUES ('Cardinal','Tom B. Erichsen','Skagen 21','Stavanger','4006','Norway');"],
		];
	}
	
	public function invalidQueryTableNamesProvider()
    {
		return [
				["INSERT INTO Customers (CustomerName, ContactName, Address, City, PostalCode, Country) VALUES ('Cardinal','Tom B. Erichsen','Skagen 21','Stavanger','4006','Norway');", ['salaries', 'teams', 'pitching']],
				["SELECT date_trunc('month', rated_at):: date AS month, gender, COUNT(*) FROM query INNER JOIN users ON ratings.user_id = users.id GROUP BY month, gender ORDER BY month, gender", ['salaries', 'teams', 'pitching']],
		];
	}

	public function tableNameProvider()
	{
		return [
			[
				['Masterd', 'Pitiching', 'Muster', 'Master'],
				'\bMasterd\b|\bPitiching\b|\bMuster\b|\bMaster\b'
			], [
				['users', 'temp', 'all'],
				'\busers\b|\btemp\b|\ball\b'
			]
		];
	}

	public function queryTableNamesProvider()
    {
		return [
			[
				'SELECT yearID, count(*) FROM salaries INNER JOIN teams ON smthng GROUP BY yearID;',
				['salaries', 'team', 'pitching']
			],
			[
				'select birthYear, count(playerId) from Master where birthYear is not null and birthYear > 1950 and birthYear <= 2000 group by birthYearorder by birthYear',
				['pitching', 'teams', 'Masters']
			]
		];
	}

	public function testIsAValidQueryProvider()
	{
		return [
			[
				'SELECT yearID, count(*) FROM salaries INNER JOIN teams ON smthng GROUP BY yearID;',
				['salaries', 'teams', 'pitching']
			],
			[
				'select birthYear, count(*) from Master where birthYear is not null and birthYear > 1950 and birthYear <= 2000 group by birthYearorder by birthYear',
				['pitching', 'teams', 'Master']
			]
		];
	}

	public function isDurationValidGoodTest()
	{
		return [
			[9.9990],
			[9.990],
		];
	}

}