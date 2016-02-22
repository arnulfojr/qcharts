<?php

namespace QCharts\CoreBundle\Exception;


class NoTableNamesException extends InstanceNotFoundException
{
	public function __construct($message, $code = 0, $previous = null)
    {
		parent::__construct($message, $code, $previous);
	}
}