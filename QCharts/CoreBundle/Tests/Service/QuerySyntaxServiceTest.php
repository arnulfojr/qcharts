<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 12/10/15
 * Time: 9:49 AM
 */

namespace QCharts\CoreBundle\Tests;

use QCharts\CoreBundle\Service\LimitsService;
use QCharts\CoreBundle\Service\QuerySyntaxService;
use QCharts\CoreBundle\Tests\Service\ClassNames;


class QuerySyntaxServiceTest extends \PHPUnit_Framework_TestCase
{

    const ROW_LIMIT = 1000;
    const OFFSET = 0;

    /**
     * @dataProvider validQueryProvider
     * @param $value
     * @param $expected
     * @return mixed
     */
    public function testRemoveSemicolon($value, $expected)
    {
        //Mocks
        $limitsServiceMock = $this->prophesize(ClassNames::LIMITS_SERVICE);

        /** @var LimitsService $limitsService */
        $limitsService = $limitsServiceMock->reveal();

        $service = new QuerySyntaxService($limitsService);

        $this->assertEquals($expected, $service->removeSemicolon($value));

        return $service->removeSemicolon($value);
    }

    /**
     * @param $query
     * @param $expectedQuery
     * @dataProvider validQueryProviderTwo
     */
    public function testAddLimit($query, $expectedQuery)
    {
        //Mocks
        $limitsServiceMock = $this->prophesize(ClassNames::LIMITS_SERVICE);

        //config
        $limits = [
            "row"=>QuerySyntaxServiceTest::ROW_LIMIT
        ];

        $limitsServiceMock
            ->getLimits()
            ->willReturn($limits);

        /** @var LimitsService $limitsService */
        $limitsService = $limitsServiceMock->reveal();

        $querySyntaxService = new QuerySyntaxService($limitsService);

        $limitedQuery = $querySyntaxService->addLimit($query);

        $this->assertEquals($expectedQuery, $limitedQuery);
    }

    /** @noinspection PhpUndefinedNamespaceInspection */
    /**
     * @param $query
     * @dataProvider queriesWithLimit
     * @expectedException QCharts\CoreBundle\Exception\ValidationFailedException
     */
    public function testAddLimitException($query)
    {
        //Mocks
        $limitServiceMock = $this->prophesize(ClassNames::LIMITS_SERVICE);

        //config
        $limits = [
            "row"=>1 //make it pop the exception :)
        ];

        $limitServiceMock
            ->getLimits()
            ->willReturn($limits);

        /** @var LimitsService $limitService */
        $limitService = $limitServiceMock->reveal();

        $querySyntaxService = new QuerySyntaxService($limitService);

        $this->assertFalse($querySyntaxService->addLimit($query));
    }
    
    /**
     * @dataProvider querySpacesProvider
     * @param $query
     */
    public function testRemoveSpaces($query)
    {
        //Mocks
        $limitsServiceMock = $this->prophesize(ClassNames::LIMITS_SERVICE);

        //reveal
        /** @var LimitsService $limitsService */
        $limitsService = $limitsServiceMock->reveal();

        $querySyntaxService = new QuerySyntaxService($limitsService);

        $this->assertEquals(
            "(select birthYear, count(playerId) from Master where birthYear is not null and birthYear > 1950 and birthYear <= 2000 group by birthYear order by birthYear) LIMIT 1000",
            $querySyntaxService->removeLineFeeds($query));
    }

    /**
     * @param $query
     * @param $expectedLimit
     * @dataProvider compareLimitInQueryProvider
     */
    public function testGetLimitFromQuery($query, $expectedLimit)
    {
        $limitServiceMock = $this->prophesize(ClassNames::LIMITS_SERVICE);


        /** @var LimitsService $limitService */
        $limitService = $limitServiceMock->reveal();

        $querySyntaxService = new QuerySyntaxService($limitService);

        $this->assertEquals($expectedLimit, $querySyntaxService->getLimitFromQuery($query));
    }

    /**
     * @param $query
     * @dataProvider queriesWithLimit
     */
    public function testHasLimitTrue($query)
    {
        $limitServiceMock = $this->prophesize(ClassNames::LIMITS_SERVICE);

        /** @var LimitsService $limitService */
        $limitService = $limitServiceMock->reveal();

        $querySyntaxService = new QuerySyntaxService($limitService);

        $this->assertTrue($querySyntaxService->hasLimit($query));
    }

    /**
     * @param $query
     * @dataProvider validQueryProvider
     */
    public function testHasLimitFalse($query)
    {
        $limitServiceMock = $this->prophesize(ClassNames::LIMITS_SERVICE);

        /** @var LimitsService $limitService */
        $limitService = $limitServiceMock->reveal();

        $querySyntaxService = new QuerySyntaxService($limitService);

        $this->assertFalse($querySyntaxService->hasLimit($query));
    }


    /**
     * @return array
     */
    public function querySpacesProvider()
    {
        return array(
            array('(select birthYear, count(playerId) from Master where birthYear is not null and birthYear > 1950 and birthYear <= 2000 group by birthYear order by birthYear) LIMIT 1000'),
        );
    }

    /**
     * @return array
     */
    public function invalidQueryProvider()
    {
        return [
            ["select yearid, count(*) from salaries; inner join teams on smthng group by yearid;"],
            ["select * from Salaries; Select * from Salaries;"]
        ];
    }

    /**
     * @return array
     */
    public function queriesWithLimit()
    {
        return [
            [
                "select birthYear as 'Birth Year', count(playerId) as 'Count' from Master where Master.birthYear is not null and Master.birthYear > 1960 and Master.birthYear <= 2014 group by Master.birthYear order by Master.birthYear limit 10"
            ],
            [
                "select * from Salaries; Select * from Salaries limit 1000"
            ]
        ];
    }

    /**
     * @return array
     */
    public function compareLimitInQueryProvider()
    {
        return [
            [
                "select birthYear as 'Birth Year', count(playerId) as 'Count' from Master where Master.birthYear is not null and Master.birthYear > 1960 and Master.birthYear <= 2014 group by Master.birthYear order by Master.birthYear limit 10",
                10
            ],
            [
                "select * from Salaries; Select * from Salaries limit 1000",
                1000
            ]
        ];
    }

    /**
     * @return array
     */
    public function validQueryProviderTwo()
    {
        $row_limit = QuerySyntaxServiceTest::ROW_LIMIT;
        $offset = QuerySyntaxServiceTest::OFFSET;

        return [
            [
                "select birthYear as 'Birth Year', count(playerId) as 'Count' from Master where Master.birthYear is not null and Master.birthYear > 1960 and Master.birthYear <= 2014 group by Master.birthYear order by Master.birthYear limit 10",
                "select birthYear as 'Birth Year', count(playerId) as 'Count' from Master where Master.birthYear is not null and Master.birthYear > 1960 and Master.birthYear <= 2014 group by Master.birthYear order by Master.birthYear limit 10"
            ],
            [
                "select * from Salaries; Select * from Salaries",
                "(select * from Salaries; Select * from Salaries) LIMIT {$row_limit} OFFSET {$offset};"
            ]
        ];
    }

    /**
     * @return array
     */
    public function validQueryProvider()
    {
        return [
            [
                "select yearid, count(*) from salaries inner join teams on smthng group by yearid;",
                "select yearid, count(*) from salaries inner join teams on smthng group by yearid"
            ],
            [
                "select * from Salaries; Select * from Salaries;",
                "select * from Salaries; Select * from Salaries"
            ]
        ];
    }
}