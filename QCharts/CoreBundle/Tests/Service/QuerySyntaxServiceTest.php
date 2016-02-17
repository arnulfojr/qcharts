<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 12/10/15
 * Time: 9:49 AM
 */

namespace QCharts\CoreBundle\Tests;

use QCharts\CoreBundle\Service\QuerySyntaxService;

class QuerySyntaxServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider validQueryProvider
     * @param $value
     * @param $expected
     * @return mixed
     */
    public function testRemoveSemicolon($value, $expected)
    {
        $service = new QuerySyntaxService();
        $this->assertEquals($expected, $service->removeSemicolon($value));
        return $service->removeSemicolon($value);
    }

//    /**
//     * @depends testRemoveSemicolon
//     * @param $query
//     */
//    public function testAddLimit($query)
//    {
//        $service = new QuerySyntaxService();
//        $limit = QuerySyntaxService::ROW_LIMIT;
//        $expected = "({$query}) LIMIT {$limit};";
//        $this->assertEquals($expected, $service->addLimit($query));
//    }


    /**
     * @dataProvider validQueryProviderTwo
     * @param $query
     * @throws \Exception
     */
    public function testAddLimit($query)
    {
        $service = new QuerySyntaxService();
        //var_dump($service->addLimit($query));
    }

    /**
     * @expectedException \Exception
     * @dataProvider invalidQueryProvider
     * @param $query
     * @throws \Exception
     */
    public function testHasOnlyOneSemicolon($query)
    {
        $ser = new QuerySyntaxService();
        $this->assertFalse($ser->hasOnlyOneSemicolon($query));
    }

    /**
     * @dataProvider querySpacesProvider
     * @param $query
     */
    public function testRemoveSpaces($query)
    {
        $queryService = new QuerySyntaxService();

        $this->assertEquals(
            "(select birthYear, count(playerId) from Master where birthYear is not null and birthYear > 1950 and birthYear <= 2000 group by birthYear order by birthYear) LIMIT 1000",
            $queryService->removeLineFeeds($query));
    }


    public function querySpacesProvider()
    {
        return array(
            array('(select birthYear, count(playerId) from Master where birthYear is not null and birthYear > 1950 and birthYear <= 2000 group by birthYear order by birthYear) LIMIT 1000'),
        );
    }

    public function invalidQueryProvider()
    {
        return [
            ["select yearid, count(*) from salaries; inner join teams on smthng group by yearid;"],
            ["select * from Salaries; Select * from Salaries;"]
        ];
    }

    public function validQueryProviderTwo()
    {
        return [
            [
                "select birthYear as 'Birth Year', count(playerId) as 'Count' from Master where Master.birthYear is not null and Master.birthYear > 1960 and Master.birthYear <= 2014 group by Master.birthYear order by Master.birthYear limit 10"
            ],
            [
                "select * from Salaries; Select * from Salaries",
            ]
        ];
    }

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