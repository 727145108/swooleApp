<?php
# @Author: crababy
# @Date:   2018-07-23T09:42:18+08:00
# @Last modified by:   crababy
# @Last modified time: 2018-07-23T09:42:34+08:00
# @License: http://www.opensource.org/licenses/mit-license.php MIT License

namespace Application\Apis\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Fund extends Eloquent {

  protected $table = 'fund';

  /**
   * 可以被批量赋值的属性。
   *
   * @var array
   */
  protected $fillable = ['fund_name', 'fund_code', 'fund_type', 'fund_scale', 'fund_data', 'fund_manager', 'fund_admin', 'fund_deposit', 'fund_share', 'life_share', 'share_data'];

  /**
   * 该模型是否被自动维护时间戳
   *
   * @var bool
   */
  public $timestamps = false;

  public function fundIntro() {
    return $this->hasOne('Application\Apis\Models\FundIntro');
  }

  public function fundStock() {
    return $this->hasOne('Application\Apis\Models\FundStock');
  }

  public function holds() {
    return $this->hasMany('Application\Apis\Models\FundHold');
  }

}
