<?php
# @Author: crababy
# @Date:   2018-06-25T11:09:45+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-25T11:09:51+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace Application\Apis\Event;

use SwooleFm\Core\AbstractInterface\EventInterface;
use SwooleFm\Core\Http\Response;
use SwooleFm\Core\Event\Event;
use SwooleFm\Config\Config;
use Illuminate\Database\Capsule\Manager as Db;

class CustomerEvent implements EventInterface {

  public static function timer_test($params) {
    //echo 'this is Timer test Method' . date('Y-m-d H:i:s') . PHP_EOL;
    //print_r($params);
  }

  public static function _init() : void {
    // 初始化数据库
    $dbConf = Config::getConf('Db.master');
    $Db = new Db;
    // 创建链接
    $Db->addConnection($dbConf);
    // 设置全局静态可访问
    $Db->setAsGlobal();
    // 启动Eloquent
    $Db->bootEloquent();
  }

  public static function _onWorkerStart(\swoole_server $server, $worker_id) : void {
  }

  public static function beforRequest(\swoole_http_request $request, \swoole_http_response $response) : void {

    //print_r($request);
    //echo 'this is Customer Event beforRequest' . PHP_EOL;
  }

  public static function afterResponse(\swoole_http_request $request, \swoole_http_response $response) : void {
  }
}
