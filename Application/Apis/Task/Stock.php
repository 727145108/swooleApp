<?php
# @Author: crababy
# @Date:   2018-06-26T11:08:23+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-07-23T15:02:37+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace Application\Apis\Task;

use GuzzleHttp\Client;
use SwooleFm\Core\Event\Timer;
use Application\Apis\Models\Fund;
use SwooleFm\Core\Tool\Logger;
use SwooleFm\Core\Task\TaskManager;

class Stock extends \SwooleFm\Core\Task\AbstractInterface\TaskInterface {

  public function run() {
    $_data = $this->getData();
    $bool = false;
    if(isset($_data['symbol']) && is_string($_data['symbol'])) {
      $fundInfo = Fund::where('fund_code', $_data['symbol'])->first();
      if($fundInfo) {
        echo $fundInfo->fund_name . $fundInfo->fund_code . "\n";
        if(in_array($fundInfo->fund_type, array('股票型', '混合型', '指数型', 'ETF联接', 'LOF', 'FOF'))) {
          //获取基金名称 初始信息等
          $client =  new Client([
            'base_uri' => 'http://fund.eastmoney.com/',
            'timeout'  => 10.0,
          ]);
          Logger::info('开始获取' . $fundInfo['fund_name'] . '['.$fundInfo['fund_code'].'] 十大重仓持股比例...');
          $response = $client->request('GET', $_data['symbol'].'.html');
          $body = $response->getBody()->getContents();
          $html_dom = new \HtmlParser\ParserDom($body);
          try {
            $stock = $html_dom->find('table.ui-table-hover', 0);
            if($stock) {
              $chigu = $stock->find('tr');
              foreach ($chigu as $key => $row) {
                $cgblname = $row->find('td', 0);
                $cgbl = $row->find('td', 1);
                if($cgblname && $cgbl) {
                  $fundInfo->fundStock()->updateOrCreate(['shares_name' => trim($cgblname->getPlainText())],[
                    'shares_code'   => '',
                    'hold_scale'    => floatval($cgbl->getPlainText())
                  ]);
                }
              }

              /*
              $codelist = $stock->getAttr('codelist');
              $codeArr = explode(',', $codelist);

              $response = $client->request('GET', 'http://hq.sinajs.cn/?list='.$codelist);
              $body = $response->getBody()->getContents();
              $body = iconv('GBK', 'utf-8', $body);
              $arrList = explode("\n", $body);
              foreach ($arrList as $key => $item) {
                $str_info = substr($item, strpos($item, "\"") + 1, -2);
                $strArr = explode(',', $str_info);
                //print_r($strArr);
              }
              */
            }
            $bool = true;
          } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
          }
        }
      }
    }
    Logger::info('十大重仓持股比例获取完成');
    return ['symbol' => $fundInfo->fund_code, 'status' => $bool];
  }


  public function finish() {
    $result = $this->getResult();
    if($result['status']) {
      Logger::info("添加异步任务获取基金代码及最新价格数据");
      $stock = new \Application\Apis\Task\SharesList();
      $stock->setData(['symbol' => $result['symbol']]);
      TaskManager::async($stock);
    }
  }

}
