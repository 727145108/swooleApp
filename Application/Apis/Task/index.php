<?php
# @Author: crababy
# @Date:   2018-06-26T11:08:23+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-26T11:08:27+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace Application\Apis\Task;

use GuzzleHttp\Client;
use SwooleFm\Core\Event\Timer;
use Application\Apis\Models\Fund;
use SwooleFm\Core\Tool\Logger;
use SwooleFm\Core\Task\TaskManager;

class Index extends \SwooleFm\Core\Task\AbstractInterface\TaskInterface {

  public function run() {
    $_data = $this->getData();
    $bool = false;
    $symbol = 0;
    if(isset($_data['symbol'])) {
      $_data['symbol'] = intval($_data['symbol']) + 1;
      $symbol = str_pad($_data['symbol'], 6, '0', STR_PAD_LEFT);
      try {
        $bool = $this->collMethod($symbol);
      } catch(\Exception $e) {
        echo $e->getMessage() . "\n";
      }
    }
    /*
    Timer::loop(5 * 1000, function (int $timer_id, $params = null) {
      $symbol = str_pad($this->symbol, 6, '0', STR_PAD_LEFT);
      try {
        $this->collMethod($symbol);
      } catch(\Exception $e) {
        echo $e->getMessage() . "\n";
      }
      $this->symbol ++;
    }, ['symbol' => $symbol]);
    */
    return ['symbol' => $symbol, 'status' => $bool];
  }


  public function finish() {
    $result = $this->getResult();
    if($result['status']) {
      Logger::info("添加异步任务获取基金持仓数据");
      $stock = new \Application\Apis\Task\Stock();
      $stock->setData(['symbol' => $result['symbol']]);
      TaskManager::async($stock);
    }
    Timer::delay(5 * 1000, function() use ($result) {
      $stock = new \Application\Apis\Task\Index();
      $stock->setData(['symbol' => $result['symbol']]);
      TaskManager::async($stock);
    });
  }

  private function collMethod($symbol) {
    //获取基金名称 初始信息等

    Logger::info('开始获取' . $symbol . '基金基本信息...');
    $client =  new Client([
      'base_uri' => 'http://stock.finance.sina.com.cn/',
      'timeout'  => 10.0,
    ]);
    $response = $client->request('GET', 'fundInfo/api/openapi.php/FundPageInfoService.tabjjgk?symbol='.$symbol.'&format=json&callback=&_=' . time());
    $body = $response->getBody()->getContents();
    $body = json_decode($body);
    if(isset($body->result->status->code) && $body->result->status->code == 0) {
      $result = $body->result->data;
      $ssrq = $result->ssrq;  //上市日期
      $xcr = $result->xcr; //续存期限
      $ssdd = $result->ssdd; //上市地点

      if(empty($result->symbol)) {
        Logger::info("基金代码:{$symbol} 获取数据信息失败\n");
        return false;
      }
      if(isset($result->ManagerName)) {
        $pattern="/<a [^>]*>(.[\x{4e00}-\x{9fa5}]+)<\/a>*/u";
        preg_match_all($pattern,$result->ManagerName,$matches);
        $manager = '';
        if(isset($matches[1])) {
          foreach ($matches[1] as $key => $item) {
            $manager .= $item . ',';
          }
          $manager = substr($manager, 0, -1);
        }
      }

      // 创建数据
      $fund = Fund::updateOrCreate(['fund_code' => $result->symbol], [
        'fund_name'   => $result->jjqc,      //jjqc=>基金全称 jjjc=>简称;
        'fund_type'   => $result->Type2Name,  //基金类型
        'fund_scale'  => $result->jjgm,  //基金规模 亿元
        'fund_data'   => $result->clrq,     //成立日期
        'fund_manager'=> isset($manager) ? $manager : '', //基金经理
        'fund_admin'  => $result->glr,      //基金管理人
        'fund_deposit'=> $result->tgr,   //基金托管人
        'fund_share'  => $result->jjfe, //基金份额 亿份
        'life_share'  => $result->jjltfe, //流通份额 亿份
        'share_data'  => $result->jjferq, //基金份额日期
      ]);
      $fund->fundIntro()->updateOrCreate(['fund_id' => $fund->id], [
        'tzmb'        => $result->tzmb, // 投资目标
        'tzfw'        => $result->tzfw, //投资范围
        'bjjz'        => $result->bjjz, //基金比较基准
        'fxsytz'      => $result->fxsytz,  //风险收益特征
        'fpyz'        => $result->fpyz,  //收益分配原则
      ]);
      Logger::info("基金名称: {$result->jjqc}基金代码:{$result->symbol} 获取数据信息成功");
      return true;
    } else {
      Logger::info("基金代码:{$result->symbol} 获取数据信息失败");
      return false;
    }
  }

}
