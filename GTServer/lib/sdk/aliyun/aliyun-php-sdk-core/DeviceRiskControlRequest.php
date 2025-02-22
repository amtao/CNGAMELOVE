<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
class DeviceRiskControlRequest extends RpcAcsRequest
{
	function  __construct()
	{
		parent::__construct("jaq", "2017-11-29", "DeviceRiskControl");
		$this->setMethod("POST");
	}

	private  $sign;

	private  $callerName;

	private  $rawData;

	public function getSign() {
		return $this->sign;
	}

	public function setSign($sign) {
		$this->sign = $sign;
		$this->queryParameters["Sign"]=$sign;
	}

	public function getCallerName() {
		return $this->callerName;
	}

	public function setCallerName($callerName) {
		$this->callerName = $callerName;
		$this->queryParameters["CallerName"]=$callerName;
	}

	public function getRawData() {
		return $this->rawData;
	}

	public function setRawData($rawData) {
		$this->rawData = $rawData;
		$this->queryParameters["RawData"]=$rawData;
	}
	
}