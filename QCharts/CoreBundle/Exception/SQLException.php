<?php

namespace QCharts\CoreBundle\Exception;

class SQLException extends DatabaseException
{
	
	public function __construct($message, $code = 0, $previous = null)
    {
		parent::__construct($message, $code, $previous);
	}
	
	public function getConstant()
	{
		return "SQLEXCEPTION";
	}
	
}