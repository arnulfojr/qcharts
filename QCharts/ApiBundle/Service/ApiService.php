<?php

namespace QCharts\ApiBundle\Service;

class ApiService
{

	private $queryService;
	
	public function __construct($service) {
		$this->queryService = $service;
	}
	
}