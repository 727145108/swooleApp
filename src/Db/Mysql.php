<?php
# @Author: crababy
# @Date:   2018-07-10T10:36:35+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-07-10T10:36:41+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace SwooleFm\Db;

use SwooleFm\Core\AbstractInterface\Singleton;

class Mysql {

  use Singleton;

  public $connection;

  /**
   * [__construct 初始化]
   */
  private function __construct($options) {
    $this->connection = new \swoole_mysql;
    $this->connection->connect($options, function(\swoole_mysql $mysql, $result) {
      if(false === $result) {
        echo "Mysql Connect Error : {$mysql->connect_error} \n";
      } else {
        echo "Mysql Connect success \n";
      }
      return $result;
    });
  }

  /**
   * [disconnect 关闭链接]
   * @return [type] [description]
   */
  public function disconnect() {
    $this->connection->close();
  }

  public function query($sql, $callback) {
    $this->connection->query($sql, $callback);
  }



}
