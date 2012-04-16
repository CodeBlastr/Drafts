<?php
/* Draft Test cases generated on: 2012-04-13 20:24:03 : 1334348643*/
App::uses('Draft', 'Drafts.Model');

/**
 * Draft Test Case
 *
 */
class DraftTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.Drafts.draft',
		);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->Draft = ClassRegistry::init('Draft');
	}
	
	
/**
 *
 */
	public function testDraft() {
		$result = $this->Draft->find('first');
		$this->assertEqual('4f899b63-037c-405c-bd16-211000000000', $result['Draft']['id']); // test that the drafts table exists
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Draft);

		parent::tearDown();
	}

}
