<?php
# @Author: crababy
# @Date:   2018-06-22T10:57:47+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-22T10:57:52+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License


return array(
  'SERVER_NAME'   => 'SwooleFm_Api',
  'MAIN_SERVER'   => array(
    'HOST'          => '0.0.0.0',
    'PORT'          => '9501',
    'SERVER_TYPE'   => 'WEB_SERVER',
    'SOCK_TYPE'     => SWOOLE_SOCK_TCP,
    'RUN_MODEL'     => SWOOLE_PROCESS,  //单线程模式:SWOOLE_BASE   进程模式:SWOOLE_PROCESS
    'OPTIONS'       => array(
      'task_worker_num'   => 1,
      'task_max_request'  => 1000,
      'max_request'       => 0,
      'worker_num'        => 2,
    ),
  ),
  'Debug'         => true,
  'ControllerNameSpace' => "\\Application\\Apis\\Controller\\",
);
