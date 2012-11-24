<?php
/* Draftable Test cases generated on: 2012-04-13 19:19:39 : 1334344779*/
App::uses('DraftableBehavior', 'Drafts.Model/Behavior');


if (!class_exists('Article')) {
	class DraftArticle extends CakeTestModel {
	/**
	 *
	 */
		public $callbackData = array();

	/**
	 *
	 */
		public $actsAs = array(
			'Drafts.Draftable' => array(
				'triggerField' => 'rename_draft',
				));
	/**
	 *
	 */
		public $useTable = 'draft_articles';

	/**
	 *
	 */
		public $name = 'Article';
	/**
	 *
	 */
		public $alias = 'Article';
	}
}


/**
 * DraftableBehavior Test Case
 *
 */
class DraftableBehaviorTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.Condition',
		'plugin.Drafts.Draft',
		'plugin.Drafts.DraftArticle',
		);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->Draftable = new DraftableBehavior();
		$this->Model = Classregistry::init('DraftArticle');
		$this->Draft = Classregistry::init('Drafts.Draft');
	}

/**
 * tearDown method
 *
 * @return void
 */
    public function tearDown() {
		unset($this->Draftable);
		unset($this->Model);
		unset($this->Draft);
		ClassRegistry::flush();

		parent::tearDown();
	}
	

/**
 * Test behavior instance
 *
 * @return void
 */
	public function testBehaviorInstance() {
		$this->assertTrue(is_a($this->Model->Behaviors->Draftable, 'DraftableBehavior'));
	}
	
/**
 * Test finding records and updating results
 */ 
	public function testFinding() {	
		$result = $this->Model->find('first', array('conditions' => array('Article.id' => '4f889729-c2fc-4c8a-ba36-1a14124e0d46'))); // has revisions but we're not asking for revisions
		$this->assertEqual('Three Revision Article', trim($result['Article']['title']));
		
		$this->Model->Behaviors->attach('Drafts.Draftable', array('returnVersion' => 1)); // now we start asking for revisions
		
		$result = $this->Model->find('first', array('conditions' => array('Article.id' => '4f668729-c2fc-4c8a-ba36-1a14124e0d46'))); // no revisions
		$this->assertEqual('Zero Revision Article', trim($result['Article']['title']));
		
		
		$result = $this->Model->find('first', array('conditions' => array('Article.id' => '4f88970e-b438-4b01-8740-1a14124e0d46'))); // one revision
		$this->assertEqual('Older Version of One Revision Article', trim($result['Article']['title']));
		
				
		$result = $this->Model->find('first', array('conditions' => array('Article.id' => '4f889729-c2fc-4c8a-ba36-1a14124e0d46'))); // multiple revisions get the earliest
		$this->assertEqual('Older Version of Second Article', trim($result['Article']['title']));
		
		$this->Model->Behaviors->attach('Drafts.Draftable', array('returnVersion' => 2)); // now we start asking for older revisions
		$result = $this->Model->find('first', array('conditions' => array('Article.id' => '4f889729-c2fc-4c8a-ba36-1a14124e0d46'))); // multiple revisions and we want the 2nd one of 3
		$this->assertEqual('Older Older Version of Second Article', trim($result['Article']['title']));
		
		$this->Model->Behaviors->attach('Drafts.Draftable', array('returnVersion' => 3)); // now we start asking for older revisions
		$result = $this->Model->find('first', array('conditions' => array('Article.id' => '4f889729-c2fc-4c8a-ba36-1a14124e0d46'))); // multiple revisions and we want the 3rd one of 3
		$this->assertEqual('Oldest Version of Second Article', trim($result['Article']['title']));
		
		$this->Model->Behaviors->attach('Drafts.Draftable', array('returnVersion' => 5)); // now we start asking for older revisions that don't exist (shoult return the oldest version)
		$result = $this->Model->find('first', array('conditions' => array('Article.id' => '4f889729-c2fc-4c8a-ba36-1a14124e0d46'))); // asking for an older version than there is, so it should return the oldest
		$this->assertEqual('Oldest Version of Second Article', trim($result['Article']['title']));
		
	}
	
	
/**
 * Test this behaviors interception of saving related models
 */
	public function testNewArticleSaves() {		
		$startCount = $this->Draft->find('count');
		$data['Article'] = array(
			'id' => '4f889729-c2fc-4c8a-ba36-1a14124e0d46',
			'title' => 'Older Version of Second Article',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'rename_draft' => 1, // save that draft that matches the latest draft to make sure nothing is saved
			); 
		$this->Model->create();
		$result = $this->Model->save($data);
		$midCount = $this->Draft->find('count');
		$this->assertEqual($startCount, $midCount); // shouldn't save because it matched the latest draft
		
		
		$data['Article'] = array(
			'id' => '4f889729-c2fc-4c8a-ba36-1a14124e0d46',
			'title' => 'Newer Version of Second Article',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'rename_draft' => 1, // save that draft that matches the latest draft to make sure nothing is saved
			); 
		$this->Model->create();
		$result = $this->Model->save($data);
		$endCount = $this->Draft->find('count');		
		$this->assertNotEqual($midCount, $endCount); // should be up one tick from start and mid, because we changed the title
		unset($result);
		unset($data);
		
		
		// test normal article save without a triggerField set
		$data['Article'] = array(
			'title' => 'Test Name',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			);
		$this->Model->create();
		$result = $this->Model->save($data);
		$this->assertTrue(!empty($result['Article']['id'])); // should be the same array as $data but with an id value
		unset($result);
		
		
		$data['Article']['rename_draft'] = 1; // save a draft, with a non default draft field name
		$result = $this->Model->save($data);
		
		$this->assertTrue(empty($result['Article']['id'])); // test that the save didn't go through, becasue draft was set
		unset($result);
	}
	
	
/**
 * Test this behaviors interception of saving related models
 */
	public function testExistingArticleSaves() {
		
		
		$data['Article'] = array(
			'id' => '4f88970e-b438-4b01-8740-1a14124e0d46',
			'title' => 'New Test Name',
			'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'rename_draft' => 1, // save a draft, with a non default draft field name
			); 
		$result = $this->Model->save($data);
		$this->assertEqual('One Revision Article', $result['Article']['title']); // test that the title is equal to the fixture data that has the same id as the sent data, meaning that the record was not updated to new value, and instead was kept the same while the incoming data was sent to the drafts table
		$draft = $this->Draft->find('first', array('conditions' => array('Draft.model' => 'Article', 'Draft.foreign_key' => '4f88970e-b438-4b01-8740-1a14124e0d46'), 'order' => 'Draft.created DESC'));
		$value = unserialize($draft['Draft']['value']);
		$this->assertEqual($data['Article']['title'], $value['Article']['title']); // test that there is a draft with the new values
		unset($result);
	}
	
	
	public function testRevising() {
		
		$save = $this->Model->saveRevision('Article', '4f889729-c2fc-4c8a-ba36-1a14124e0d46', '2012-04-01 20:24:03');
		$find = $this->Model->find('first', array('conditions' => array('Article.id' => '4f889729-c2fc-4c8a-ba36-1a14124e0d46')));
		$this->assertEqual('Older Version of Second Article', $find['Article']['title']); // test that the article has been updated to an older version
		
		$data['Article']['id'] = '4f889729-c2fc-4c8a-ba36-1a14124e0d46';
		$data['Article']['rename_draft'] = 'revise'; // save a draft, with a non default draft field name
		$data['Article']['revise_to_date'] = '2012-04-01 20:24:03';
		$data['Article']['title'] = 'Do Not Save This Version Anywhere';
		$result = $this->Model->save($data);
		$this->assertEqual('Older Version of Second Article', $result['Article']['title']);
	}
	
	
	public function testDeleting() {
		
		$delete = $this->Model->delete('4f889729-c2fc-4c8a-ba36-1a14124e0d46');
		$result = $this->Draft->find('all', array('conditions' => array('Draft.model' => 'Article', 'Draft.foreign_key' => '4f889729-c2fc-4c8a-ba36-1a14124e0d46')));		
		$this->assertTrue(empty($result[0])); // test that all drafts with that id are gone
	}

}
