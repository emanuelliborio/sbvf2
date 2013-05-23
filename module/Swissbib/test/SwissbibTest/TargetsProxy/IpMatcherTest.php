<?php
namespace SwissbibTest\RecordDriver;

use SwissbibTest\TargetsProxy\TargetsProxyTestCase;

/**
 * [Description]
 *
 */
class IpMatcherTest extends TargetsProxyTestCase
{

	public function setUp()
	{
		$this->initialize('C:/xampp/htdocs/vufind/module/Swissbib/test/SwissbibTest/TargetsProxy/config_detect_ip.ini');
	}

	/**
	 * Test single IP address to NOT match
	 */
	public function testIpAddressFalse()
	{
		$proxyDetected = $this->targetsProxy->detectTarget('99.99.99.99', 'xxx.xxx.xx');

		$this->assertInternalType('bool', $proxyDetected);
		$this->assertFalse($proxyDetected);
	}

	/**
	 * Test single IP address match (exact)
	 */
	public function testIpAddressSingle()
	{
		$proxyDetected = $this->targetsProxy->detectTarget('120.0.0.1', 'unibas.swissbib.ch');

		$this->assertInternalType('bool', $proxyDetected);
		$this->assertTrue($proxyDetected);
		$this->assertEquals('Target_Ip_Single', $this->targetsProxy->getTargetKey());
		$this->assertEquals('apiKeyIpSingle', $this->targetsProxy->getTargetApiKey());
	}

	/**
	 * Test IP address wildcard match
	 */
	public function testIpAddressWildcard()
	{
		$proxyDetected = $this->targetsProxy->detectTarget('121.0.2.3', 'unibas.swissbib.ch');

		$this->assertInternalType('bool', $proxyDetected);
		$this->assertTrue($proxyDetected);
		$this->assertEquals('Target_Ip_Wildcard', $this->targetsProxy->getTargetKey());
		$this->assertEquals('apiKeyIpWildcard', $this->targetsProxy->getTargetApiKey());
	}

	/**
	 * Test IP address wildcard match
	 */
	public function testIpAddressSection()
	{
		$proxyDetected = $this->targetsProxy->detectTarget('0.0.5.5', 'unibas.swissbib.ch');

		$this->assertInternalType('bool', $proxyDetected);
		$this->assertTrue($proxyDetected);
		$this->assertEquals('Target_Ip_Section', $this->targetsProxy->getTargetKey());
		$this->assertEquals('apiKeyIpSection', $this->targetsProxy->getTargetApiKey());
	}





	/**
	 * Test single IP address match (exact) from comma separated list of patterns
	 */
	public function testIpAddressSingleCSV()
	{
		$proxyDetected = $this->targetsProxy->detectTarget('124.0.0.1', 'unibas.swissbib.ch');

		$this->assertInternalType('bool', $proxyDetected);
		$this->assertTrue($proxyDetected);
		$this->assertEquals('Target_Ip_Single_CSV', $this->targetsProxy->getTargetKey());
		$this->assertEquals('apiKeyIpSingleCSV', $this->targetsProxy->getTargetApiKey());
	}

}
