<?php
# @Author: crababy
# @Date:   2018-06-26T11:08:23+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-07-25T09:17:27+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace Application\Apis\Task;

use GuzzleHttp\Client;
use SwooleFm\Core\Event\Timer;
use Application\Apis\Models\Fund;
use SwooleFm\Core\Tool\Logger;
use SwooleFm\Core\Task\TaskManager;

class Hold extends \SwooleFm\Core\Task\AbstractInterface\TaskInterface {

  public function run() {
    $_data = $this->getData();
    $bool = false;
    if(isset($_data['symbol']) && is_string($_data['symbol'])) {
      $fundInfo = Fund::where('fund_code', $_data['symbol'])->first();
      if($fundInfo) {
        //$this->collInfo($fundInfo);
        $client =  new Client([
          'base_uri' => 'http://fund.eastmoney.com/',
          'timeout'  => 10.0,
        ]);
        $response = $client->request('GET', 'pingzhongdata/' . $fundInfo->fund_code .'.js?v=' . date('Ymdhis'));
        $body = $response->getBody()->getContents();
        $_data = explode("\n", $body);
        $list = array();
        foreach ($_data as $key => $item) {
          if(substr($item, 0, 3) === 'var') {
            list(,$info) = explode("var", $item);
            list($k, $v) = explode("=", $info);
            $list[trim($k)] = substr(trim($v),0,-1);
          }
        }
        $fundInfo->syl_1n = str_replace("\"", "", str_replace("\"", "", $list['syl_1n']));
        $fundInfo->syl_6y = str_replace("\"", "", str_replace("\"", "", $list['syl_6y']));
        $fundInfo->syl_3y = str_replace("\"", "", str_replace("\"", "", $list['syl_3y']));
        $fundInfo->syl_1y = str_replace("\"", "", str_replace("\"", "", $list['syl_1y']));
        $fundInfo->save();

        //持有人结构
        $holderArr = json_decode($list['Data_holderStructure']);
        foreach ($holderArr->categories as $idx => $value) {
          $jgcy = $holderArr->series[0];
          $grcy = $holderArr->series[1];
          $nbcy = $holderArr->series[2];
          $fundInfo->holds()->updateOrCreate(['fund_id' => $fundInfo->id, 'date' => $value], [
            'jgcybl'       => $jgcy->data[$idx],  //机构投资者持有占总份额比
            'grcybl'       => $grcy->data[$idx],  //个人投资者持有占总份额比
            'ygcybl'       => $nbcy->data[$idx],  //公司员工持有比例
          ]);
        }
        //print_r($this->parse_js($body));
      }
    }
    return ['symbol' => $fundInfo->fund_code, 'status' => $bool];
  }


  public function finish() {
    $result = $this->getResult();
  }



  private function parse_js($string){
    $pregString="/(var )?([a-zA-Z_0-9]+)(\['([a-zA-Z_0-9]+)'\])?=([^;]*);/";
    preg_match_all($pregString,$string,$JsArrayPre);
    $num=count($JsArrayPre['0']);
    for($i=0;$i<$num;$i++){
  		if(isset($JsArray[$JsArrayPre['5'][$i]]))//为迭代赋值
  			$JsArrayPre['5'][$i]=$JsArray[$JsArrayPre['5'][$i]];

  		if($JsArrayPre['5'][$i]=="{}"||$JsArrayPre['5'][$i]=="[]")//定义数组
  			$JsArrayPre['5'][$i]=array();

  		if($JsArrayPre['4'][$i])//数组迭代
  			$JsArray[$JsArrayPre['2'][$i]][$JsArrayPre['4'][$i]]=$JsArrayPre['5'][$i];
  		else
  			$JsArray[$JsArrayPre['2'][$i]]=$JsArrayPre['5'][$i];
    }
    return $JsArray;
  }


  private function collInfo($fundInfo, $date = '') {
    //获取基金名称 初始信息等
    //http://fund.eastmoney.com/pingzhongdata/040025.js?v=20180725093622
    Logger::info('开始获取' . $fundInfo->fund_code . '基金持有人信息...');
    echo $date . "\n";
    $client =  new Client([
      'base_uri' => 'http://stock.finance.sina.com.cn/',
      'timeout'  => 10.0,
    ]);
    $response = $client->request('GET', 'fundInfo/api/openapi.php/FundPageInfoService.tabcyrjg?callback=&symbol='.$fundInfo->fund_code.'&date='.$date.'&_=' . time());
    $body = $response->getBody()->getContents();
    $body = json_decode($body);
    if(isset($body->result->status->code) && $body->result->status->code == 0) {
      $result = $body->result->data;
      if(empty($date)) {
        foreach ($result->CYRDate as $key => $val) {
          $this->collInfo($fundInfo, $val->REPORTDATE);
        }
      } else {
        $fundInfo->holds()->updateOrCreate(['fund_id' => $fundInfo->id, 'date' => $date], [
          'cyrhs'        => $result->CYRInfo->cyrhs, // 持有人户数
          'cyrfe'        => $result->CYRInfo->cyrfe, //平均每户持有人基金份额
          'jgcyfe'       => $result->CYRInfo->jgcyfe, //机构投资者持有份额
          'jgcybl'       => $result->CYRInfo->jgcybl,  //机构投资者持有占总份额比
          'grcyfe'       => $result->CYRInfo->grcyfe,  //个人投资者持有份额
          'grcybl'       => $result->CYRInfo->grcybl,  //个人投资者持有占总份额比
          'ygcyfe'       => $result->CYRInfo->ygcyfe,  //公司员工持有份
          'ygcybl'       => $result->CYRInfo->ygcybl,  //公司员工持有比例
          'glrcyfe'       => $result->CYRInfo->glrcyfe,  //管理人持有份额
          'glrcybl'       => $result->CYRInfo->glrcybl,  //管理人持有比例
        ]);
      }
    }
  }

}
