<?php
/**
 * Test the ajax endpoints the two news_admin lists' actions now call
 *
 * @link https://www.egroupware.org
 * @package news_admin
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 */

namespace EGroupware\NewsAdmin;

use EGroupware\Api;
use EGroupware\Api\LoggedInTest;

require_once realpath(__DIR__ . '/../../api/tests/LoggedInTest.php');

/**
 * news_admin has two lists in two classes, and both had an action that submitted:
 * news_admin_gui (the news entries) its Delete, news_admin_ui (the categories) its Delete and
 * Update RSS feed. Each class now has its own ajax_action(), which is why the news list has to
 * name its menuaction explicitly - the "<app>.<app>_ui.ajax_action" convention the client falls
 * back to would send it to the category list's endpoint.
 *
 * It also covers a bug the conversion surfaced: news_admin_ui::action()'s "select all" branch
 * called $this->get_rows(), and that class has no get_rows at all - only get_cats() - so
 * selecting all categories fatalled instead of selecting anything.
 *
 * PASS CRITERIA
 * The entries really went away (read back through the bo), and each response carries an
 * egw.refresh call naming news_admin.
 */
class AjaxActionTest extends LoggedInTest
{
	/** @var int[] news_ids created by this test */
	protected $news_ids = [];
	/** @var int[] cat_ids created by this test */
	protected $cat_ids = [];

	protected function setUp() : void
	{
		Api\Json\Response::get()->initResponseArray();
	}

	protected function tearDown() : void
	{
		$gui = new \news_admin_gui();
		foreach($this->news_ids as $id)
		{
			$gui->delete(['news_id' => $id]);
		}
		$this->news_ids = [];

		$cats = new Api\Categories('', 'news_admin');
		foreach($this->cat_ids as $cat_id)
		{
			$cats->delete($cat_id, false, true);
		}
		$this->cat_ids = [];
	}

	/**
	 * A real eTemplate request id, the way the browser sends one along - the endpoints refuse
	 * without it, see Nextmatch::validateExecId().  Writing to the request is what persists it.
	 */
	protected function execId() : string
	{
		$request = \EGroupware\Api\Etemplate\Request::read();
		$id = $request->id();
		$request->content = ['nm' => []];
		unset($request);
		return $id;
	}

	/**
	 * The egw.refresh call the response should carry, or null
	 */
	protected function refreshCall() : ?array
	{
		$response = Api\Json\Response::get();
		$prop = (new \ReflectionClass($response))->getProperty('responseArray');
		$prop->setAccessible(true);
		foreach((array)$prop->getValue($response) as $chunk)
		{
			$chunk = (array)$chunk;
			if (($chunk['type'] ?? null) === 'apply' && (($chunk['data']['func'] ?? null) === 'egw.refresh'))
			{
				return (array)$chunk['data']['parms'];
			}
		}
		return null;
	}

	protected function makeCategory(string $name) : int
	{
		$cats = new Api\Categories('', 'news_admin');
		return $this->cat_ids[] = $cats->add([
			'name'   => $name,
			'descr'  => 'created by news_admin/tests/AjaxActionTest.php',
			'parent' => 0,
			'access' => 'public',
		]);
	}

	protected function makeNews(string $headline, int $cat_id) : int
	{
		$gui = new \news_admin_gui();
		$gui->init();
		$gui->data = [
			'news_headline'    => $headline,
			'news_content'     => 'created by news_admin/tests/AjaxActionTest.php',
			'news_date'        => time(),
			'news_begin'       => time(),
			'news_submittedby' => $GLOBALS['egw_info']['user']['account_id'],
			'cat_id'           => $cat_id,
		];
		// $ignore_acl: the fixture category is brand new, so the test user has no ADD right on
		// it yet - and save() answers true (its error value) rather than 0 when it refuses
		$this->assertSame(0, $gui->save(null, true), 'could not create the test news entry');

		return $this->news_ids[] = $gui->data['news_id'];
	}

	protected function newsExists($id) : bool
	{
		return (bool)(new \news_admin_gui())->read(['news_id' => $id]);
	}

	protected function categoryExists($cat_id) : bool
	{
		return (bool)Api\Categories::read($cat_id);
	}

	/**
	 * The news list: the endpoint has to reach the delete loop and really delete.
	 */
	public function testNewsDeleteRemovesTheEntry()
	{
		$cat_id = $this->makeCategory('AjaxActionTest news cat '.bin2hex(random_bytes(3)));
		$id = $this->makeNews('AjaxActionTest delete', $cat_id);
		$this->assertTrue($this->newsExists($id), 'fixture was not created');

		(new \news_admin_gui())->ajax_action($this->execId(), 'delete', [$id]);

		$this->assertFalse($this->newsExists($id), 'delete must remove the news entry');
		$parms = $this->refreshCall();
		$this->assertNotNull($parms, 'the endpoint must answer with egw.refresh');
		$this->assertEquals($id, $parms[2]);
		$this->assertSame('delete', $parms[3]);
		$this->assertSame('news_admin', $parms[1]);
		$this->assertSame('news_admin', $parms[4], 'never the msg-only-push-refresh sentinel');
	}

	/**
	 * The category list: Delete takes the category and the news in it.
	 */
	public function testCategoryDeleteRemovesTheCategory()
	{
		$cat_id = $this->makeCategory('AjaxActionTest cat '.bin2hex(random_bytes(3)));
		$this->assertTrue($this->categoryExists($cat_id), 'fixture was not created');

		// delete_cat() needs admin_cat(): either the admin app, or an explicit write right on
		// the category, which a brand new one does not have
		$this->asAdmin(function() use ($cat_id) {
			(new \news_admin_ui())->ajax_action($this->execId(), 'delete', [$cat_id]);
		});

		$this->assertFalse($this->categoryExists($cat_id), 'delete must remove the category');
		$parms = $this->refreshCall();
		$this->assertEquals($cat_id, $parms[2]);
		$this->assertSame('delete', $parms[3]);
	}

	/**
	 * Without a valid exec id neither endpoint may do anything.
	 */
	public function testABogusExecIdDeletesNothing()
	{
		$cat_id = $this->makeCategory('AjaxActionTest bogus '.bin2hex(random_bytes(3)));
		$id = $this->makeNews('AjaxActionTest bogus exec id', $cat_id);

		$this->asAdmin(function() use ($id, $cat_id) {
			(new \news_admin_gui())->ajax_action('news_admin_nobody_not-real', 'delete', [$id]);
			(new \news_admin_ui())->ajax_action('news_admin_nobody_not-real', 'delete', [$cat_id]);
		});

		$this->assertTrue($this->newsExists($id), 'a rejected request must not run the action');
		$this->assertTrue($this->categoryExists($cat_id), 'nor on the category list');
		$this->assertNull($this->refreshCall(), 'and must not answer with egw.refresh either');
	}

	/**
	 * "Select all" on the category list used to call $this->get_rows(), which news_admin_ui does
	 * not have - a fatal, not a no-op. It has to expand through get_cats() instead.
	 */
	public function testSelectAllOnCategoriesDoesNotFatal()
	{
		$cat_id = $this->makeCategory('AjaxActionTest selectall '.bin2hex(random_bytes(3)));

		// what the list caches for its own session key
		Api\Cache::setSession('news_admin', 'cats', [
			'start' => 0, 'num_rows' => 25, 'search' => '', 'col_filter' => [],
			'order' => 'cat_name', 'sort' => 'ASC',
		]);

		$success = $failed = $action_msg = null;
		$msg = '';
		// 'update' rather than 'delete': this is about the expansion not fatalling, and an RSS
		// update of a category with no feed is a no-op, where a delete would take every category
		// on the instance with it
		(new \news_admin_ui())->action('update', [], true, $success, $failed, $action_msg, 'cats', $msg);

		$this->assertIsInt($success, 'select all must expand without fatalling');
		$this->assertTrue($this->categoryExists($cat_id), 'and must not have deleted anything');
	}

	/**
	 * Updating a feed rewrites the news behind a category rather than the category row, so it
	 * always asks for a reload.
	 */
	public function testUpdateAsksForAFullReload()
	{
		$cat_id = $this->makeCategory('AjaxActionTest update '.bin2hex(random_bytes(3)));

		(new \news_admin_ui())->ajax_action($this->execId(), 'update', [$cat_id]);

		$parms = $this->refreshCall();
		$this->assertNotNull($parms);
		$this->assertNull($parms[2], 'no single id');
		$this->assertNull($parms[3], 'and no type, so egw.refresh reloads the list');
	}
}
