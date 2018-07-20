<?php
# @Author: crababy
# @Date:   2018-06-22T10:48:42+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-22T10:49:12+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

use SwooleFm\Core\SwooleManager;
use SwooleFm\Core\Event\EventRegister;
use SwooleFm\Core\Event\Event;

define('SWOOLERM_ROOT', realpath(getcwd()));

foreach ([SWOOLERM_ROOT . '/../../vendor/autoload.php'] as $file) {
  if (file_exists($file)) {
    require_once $file;
    break;
  }
}

Event::on('_init', 'Application\Apis\Event\CustomerEvent::_init');
Event::on('_onWorkerStart', 'Application\Apis\Event\CustomerEvent::_onWorkerStart');
Event::on('beforRequest', 'Application\Apis\Event\CustomerEvent::beforRequest');
Event::on('afterResponse', 'Application\Apis\Event\CustomerEvent::afterResponse');

envCheck();
commandHandler();
