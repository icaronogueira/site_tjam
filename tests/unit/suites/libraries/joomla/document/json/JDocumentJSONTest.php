<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JDocumentJson
 */
class JDocumentJsonTest extends TestCase
{
	/**
	 * @var  JDocumentJson
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		JFactory::$application = $this->getMockWeb();

		$this->object = new JDocumentJson;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * @testdox  Test the default return for render
	 */
	public function testTheDefaultReturnForRender()
	{
		$this->assertEmpty($this->object->render());
	}

	/**
	 * @testdox  Test the default return for getName
	 */
	public function testTheDefaultReturnForGetName()
	{
		$this->assertSame('joomla', $this->object->getName());
	}

	/**
	 * @testdox  Test that setName returns an instance of $this
	 */
	public function testEnsureSetNameReturnsThisObject()
	{
		$this->assertSame($this->object, $this->object->setName('CMS'));
	}
}
