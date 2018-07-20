<?php
# @Author: crababy
# @Date:   2018-06-26T11:08:23+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-06-26T11:08:27+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace Application\Apis\Task;

use GuzzleHttp\Client;

class Index extends \SwooleFm\Core\Task\AbstractInterface\TaskInterface {

  public function run() {
    //获取基金名称 初始信息等
    $client =  new Client([
      'base_uri' => 'http://stock.finance.sina.com.cn/',
      'timeout'  => 2.0,
    ]);
    $response = $client->request('GET', 'fundInfo/api/openapi.php/FundPageInfoService.tabjjgk?symbol=040025&format=json&callback=&_=1532070585522');
    $body = $response->getBody()->getContents();
    $body = json_decode($body);
    if(isset($body->result->status->code) && $body->result->status->code == 0) {
      $result = $body->result->data;
      print_r($result);
      $fund_name = $result->jjqc;      //jjqc=>基金全称 jjjc=>简称
      $fund_code = $result->symbol;   //基金代码
      $fund_data = $result->clrq;     //成立日期
      $fund_type = $result->Type2Name;  //基金类型

    } else {
      echo "获取数据信息失败\n";
    }

    echo 'this is task Method' . PHP_EOL;
    return 'SUCCESS';
  }


  public function finish() {
    echo 'this is finish Method' . PHP_EOL;
    print_r($this->getResult());
  }
}
