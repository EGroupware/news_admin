<?php
/**
 * EGroupware News admin: refreshing single rows of the list of news does not replace its stored state
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
 * The list of news stores the query of every request in the session, and restores it when the list is
 * opened again. A refresh of single rows (after a push, or a save in a popup) is a query for just
 * those rows: the filters of the list plus col_filter[news_id], with csv_export='refresh'. Stored,
 * the list showed only the refreshed rows the next time it was opened.
 */
class RefreshKeepsListStateTest extends LoggedInTest
{
	const STORED = ['filter' => 'stored state of the list', 'col_filter' => []];

	protected $before;

	protected function setUp() : void
	{
		parent::setUp();
		$this->before = Api\Cache::getSession('news_admin', 'index');
		Api\Cache::setSession('news_admin', 'index', self::STORED);
	}

	protected function tearDown() : void
	{
		Api\Cache::setSession('news_admin', 'index', $this->before);
		parent::tearDown();
	}

	protected function getRows(array $query) : void
	{
		$query += ['start' => 0, 'num_rows' => 1, 'order' => 'news_date', 'sort' => 'ASC', 'filter' => '', 'search' => '',
			'col_filter' => []];
		$rows = $readonlys = [];
		(new \news_admin_gui())->get_rrows($query, $rows, $readonlys);
	}

	public function testRefreshOfSingleRowsIsNotStored()
	{
		$this->getRows(['csv_export' => 'refresh', 'col_filter' => ['news_id' => [0]]]);

		$this->assertSame(self::STORED, Api\Cache::getSession('news_admin', 'index'));
	}

	public function testQueryOfTheListIsStored()
	{
		$this->getRows(['col_filter' => ['news_id' => '']]);

		$this->assertNotSame(self::STORED, Api\Cache::getSession('news_admin', 'index'));
		$this->assertSame('news_date', Api\Cache::getSession('news_admin', 'index')['order'] ?? null);
	}
}
