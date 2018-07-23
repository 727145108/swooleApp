<?php
# @Author: crababy
# @Date:   2018-06-25T17:28:16+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-25T17:28:22+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace Application\Apis\Controller;

use SwooleFm\Core\Http\AbstractInterface\Controller;
use SwooleFm\Core\Task\TaskManager;
use SwooleFm\Core\Event\Timer;
use GuzzleHttp\Client;

/**
 * index
 * message
 */
class Index extends Controller {

  public function index() {
    $task = new \Application\Apis\Task\Index();
    $task->setData(['symbol' => '000001']);
    TaskManager::async($task);

    /*

    $stock = new \Application\Apis\Task\SharesList();
    $stock->setData(['symbol' => '040025']);
    TaskManager::async($stock);

    $stock = new \Application\Apis\Task\Stock();
    $stock->setData(['symbol' => '040025']);
    TaskManager::async($stock);

    */
    throw new \Exception('1231', 2006);
  }
}
