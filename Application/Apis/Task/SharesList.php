<?php
# @Author: crababy
# @Date:   2018-06-26T11:08:23+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-07-23T16:54:58+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace Application\Apis\Task;

use GuzzleHttp\Client;
use SwooleFm\Core\Event\Timer;
use Application\Apis\Models\Fund;
use SwooleFm\Core\Tool\Logger;
use SwooleFm\Core\Task\TaskManager;

class SharesList extends \SwooleFm\Core\Task\AbstractInterface\TaskInterface {

  public function run() {
    $_data = $this->getData();
    if(isset($_data['symbol']) && is_string($_data['symbol'])) {
      $fundInfo = Fund::where('fund_code', $_data['symbol'])->first();
      if(!$fundInfo) {
        return false;
      }
      echo $fundInfo->fund_name . $fundInfo->fund_code . "\n";
      //获取基金名称 初始信息等
      $client =  new Client([
        'base_uri' => 'http://finance.sina.com.cn/',
        'timeout'  => 10.0,
      ]);
      Logger::info('开始获取' . $fundInfo['fund_name'] . '['.$fundInfo['fund_code'].'] 持股代码及最新价格&&涨跌幅');
      //http://finance.sina.com.cn/fund/quotes/040025/bc.shtml
      $response = $client->request('GET', 'fund/quotes/'.$fundInfo['fund_code'].'/bc.shtml');
      $body = $response->getBody()->getContents();
      $html_dom = new \HtmlParser\ParserDom($body);
      try {
        $stock = $html_dom->find('table#fund_sdzc_table', 0);
        if($stock) {
          $codelist = $stock->getAttr('codelist');
          $codeArr = explode(',', $codelist);

          $response = $client->request('GET', 'http://hq.sinajs.cn/?list='.$codelist);
          $body = $response->getBody()->getContents();
          $body = iconv('GBK', 'utf-8', $body);
          $arrList = explode("\n", $body);
          foreach ($arrList as $key => $item) {
            if(!empty($item)) {
              list($code, $info) = explode('=', $item);
              $strArr = explode('_', $code);
              $infoArr = explode(',', substr($info, 1, -2));
              $shares_name = str_replace(" ", "", $infoArr[0]);
              $fundInfo->fundStock()->updateOrCreate(['fund_id' => $fundInfo->id, 'shares_name' => $shares_name],[
                'shares_code'   => $strArr[2]
              ]);
            }
          }
        } else {
          echo '无持仓数据....';
          Logger::info( $fundInfo['fund_name'] . '['.$fundInfo['fund_code'].']此基金无持仓数据');
        }
      } catch (\Exception $e) {
        echo $e->getMessage() . "\n";
      }
    }
    Logger::info('持股代码及最新价格&&涨跌幅获取完成');
    return "\tSUCCESS\n";
  }


  public function finish() {
    print_r($this->getResult());
  }

}
