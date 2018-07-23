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

class Stock extends \SwooleFm\Core\Task\AbstractInterface\TaskInterface {

  public function run() {
    $_data = $this->getData();
    if(isset($_data['symbol']) && is_string($_data['symbol'])) {

      $fundInfo = Fund::where('fund_code', $_data['symbol'])->first();
      if(!$fundInfo) {
        return false;
      }

      //获取基金名称 初始信息等
      $client =  new Client([
        'base_uri' => 'http://fund.eastmoney.com/',
        'timeout'  => 10.0,
      ]);
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
              echo $cgblname->getPlainText() . '==>' . $cgbl->getPlainText() . " \n";
              $fundInfo->fundStock()->updateOrCreate(['shares_name' => $cgblname->getPlainText()],[
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
      } catch (\Exception $e) {
        echo $e->getMessage() . "\n";
      }
    }
    return "\tSUCCESS\n";
  }


  public function finish() {
    print_r($this->getResult());
  }

}
