<?php
# @Author: crababy
# @Date:   2018-07-23T09:42:18+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-07-23T09:42:34+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace Application\Apis\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class FundHold extends Eloquent {

  protected $table = 'fund_hold';

  /**
   * 可以被批量赋值的属性。
   *
   * @var array
   */
  protected $fillable = ['fund_id', 'date', 'jgcybl', 'grcybl', 'ygcybl'];

  /**
   * 该模型是否被自动维护时间戳
   *
   * @var bool
   */
  public $timestamps = false;

}
