<?php

namespace QCharts\CoreBundle\Tests\Validation\Validator;

use \PHPUnit_Framework_TestCase;
use QCharts\CoreBundle\Validation\Validator\ReadOnlyValidator;

class ClassNames
{
    const READ_ONLY_VALIDATOR = 'QCharts\CoreBundle\Validation\Validator\ReadOnlyValidator';
}

class ReadOnlyValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param $query
     * @dataProvider selectQueryProvider
     */
    public function testValidateTrue($query)
    {
        $readOnlyValidator = new ReadOnlyValidator();
        $readOnlyValidator->setObject($query);
        $this->assertTrue($readOnlyValidator->validate());
    }

    /** @noinspection PhpUndefinedNamespaceInspection */
    /**
     * @param $query
     * @dataProvider alterQueryProvider
     * @expectedException QCharts\CoreBundle\Exception\ValidationFailedException
     */
    public function testValidateFalse($query)
    {
        $readOnlyValidator = new ReadOnlyValidator();
        $readOnlyValidator->setObject($query);
        $this->assertFalse($readOnlyValidator->validate());
    }

    /**
     * @return array
     */
    public function selectQueryProvider()
    {
        return [
            [
                /** @lang MySQL */
                'SELECT yearID, count(*) FROM salaries INNER JOIN teams ON smthng GROUP BY yearID;'
            ],
            [
                /** @lang MySQL */
                'SELECT yearID, count(*) FROM salaries GROUP BY yearID;'
            ],
            [
                'SELECT date_trunc(\'month\', rated_at)::date AS month, gender, COUNT(*) FROM query
INNER JOIN users ON ratings.user_id = users.id GROUP BY month, gender ORDER BY month, gender'
            ]
        ];
    }

    /**
     * @return array
     */
    public function alterQueryProvider()
    {
        return [
            [
                /** @lang MySQL */
                "INSERT INTO Customers (CustomerName, ContactName, Address, City, PostalCode, Country) VALUES ('Cardinal','Tom B. Erichsen','Skagen 21','Stavanger','4006','Norway');"
            ],
            [
                /** @lang MySQL */
                "INSERT INTO Customers (CustomerName, ContactName, Address, City, PostalCode, Country) VALUES ('Cardinal','Tom B. Erichsen','Skagen 21','Stavanger','4006','Norway');"
            ]
        ];
    }
    
}