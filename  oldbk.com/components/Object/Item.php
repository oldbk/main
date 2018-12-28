<?php
/**
 * Created by PhpStorm.
 * User: me
 * Date: 13.12.16
 * Time: 23:37
 */

namespace components\Object;


use components\Model\Cshop;
use components\Model\Eshop;
use components\Model\Shop;
use components\Model\Magic;

class Item
{
	/** @var Shop|Eshop|Cshop */
	public $item;
	public $img_big = '';
	public $magic = 0;
	public $incmagic = 0;

	private $unlim_items = array(6,8,10,12,13,14,15,23,25,33,34,38,39,40,41,42,43,44,45,52,57,58,60,61,68,69,76,80,87,90,94,95,110,130,144,145,147,257);
	private $runs_5lvl_param = array(
		"6000" =>array("ab_mf"=>0,"ab_bron"=>0,"ab_uron"=>1),
		"6001" =>array("ab_mf"=>0,"ab_bron"=>3,"ab_uron"=>0),
		"6002" =>array("ab_mf"=>1,"ab_bron"=>0,"ab_uron"=>0),
		"6003" =>array("ab_mf"=>0,"ab_bron"=>0,"ab_uron"=>1),
		"6004" =>array("ab_mf"=>0,"ab_bron"=>3,"ab_uron"=>0),
		"6005" =>array("ab_mf"=>1,"ab_bron"=>0,"ab_uron"=>0),
		"6006" =>array("ab_mf"=>0,"ab_bron"=>0,"ab_uron"=>1),
		"6007" =>array("ab_mf"=>0,"ab_bron"=>3,"ab_uron"=>0),
		"6008" =>array("ab_mf"=>1,"ab_bron"=>0,"ab_uron"=>0),
		"6009" =>array("ab_mf"=>0,"ab_bron"=>0,"ab_uron"=>1),
		"6010" =>array("ab_mf"=>0,"ab_bron"=>3,"ab_uron"=>0),
		"6011" =>array("ab_mf"=>1,"ab_bron"=>0,"ab_uron"=>0),
		"6012" =>array("ab_mf"=>0,"ab_bron"=>0,"ab_uron"=>1),
		"6013" =>array("ab_mf"=>0,"ab_bron"=>3,"ab_uron"=>0),
		"6014" =>array("ab_mf"=>1,"ab_bron"=>0,"ab_uron"=>0),
		"6015" =>array("ab_mf"=>0,"ab_bron"=>0,"ab_uron"=>1),
		"6016" =>array("ab_mf"=>0,"ab_bron"=>3,"ab_uron"=>0),
		"6017" =>array("ab_mf"=>1,"ab_bron"=>0,"ab_uron"=>0),
	);

	public function __construct($attributes = array())
	{
		if($attributes) {
			$this->fillData($attributes);
		}
	}


	public function fillData(Array $item) {
		$this->item = Shop::createFromArray($item);

		if ($this->item->magic > 0) {
			$this->magic = (Magic::find('id = ?', array($this->item->magic)))->asModel();
		} elseif ($this->item->includemagic > 0) {
			$this->incmagic = (Magic::find('id = ?', array($this->item->includemagic)))->asModel();
		}
	}

	public function isArt()
	{
		return ($this->item->ab_uron || $this->item->ab_bron || $this->item->ab_mf) && $this->item->type != 30;
	}

	public function isUnlim()
	{
		return in_array($this->item->id, $this->unlim_items);
	}

	public function getEmptyRune()
	{
		return $this->runs_5lvl_param[$this->item->id];
	}

	public function getElkaBuketBonus()
	{
		$bonus = 0;
		if (($this->item->id >= 55510301 && $this->item->id <= 55510311) || ($this->item->id >= 55510328 && $this->item->id <= 55510333)) {
			$bonus = 1;
		} elseif ($this->item->id == 55510350 || ($this->item->id >= 410021 && $this->item->id <= 410026) || ($this->item->id >= 410130 && $this->item->id <= 410135) || ($this->item->id >= 410001 && $this->item->id <= 410008)) {
			$bonus = 2;
		} elseif ($this->item->id == 55510351) {
			$bonus = 3;
		} elseif ($this->item->id == 55510352) {
			$bonus = 10;
		} elseif ($this->item->id == 410027) {
			$bonus = 5;
		} elseif ($this->item->id == 410028) {
			$bonus = 10;
		}
		return $bonus;
	}

	private $gold_price = array(
		55510350 => 150,
		55510351 => 400,
		55510352 => 5000,
	);
	public function getGold()
	{
		return isset($this->gold_price[$this->item->id]) ? $this->gold_price[$this->item->id] : 0;
	}

	public static function get_rkm_bonus_by_magic($idmag) {
		$rkm=0;
		$rkms_conf=array();
	
		//Грифона
		$rkms_conf[135]=1;  //'Малый свиток «Вой Грифона»';	
		$rkms_conf[136]=5; //'Совершенный свиток «Вой Грифона»';						
		$rkms_conf[137]=3; //'Большой свиток «Вой Грифона»';						
		$rkms_conf[138]=2; //'Средний свиток «Вой Грифона»';				

		//абилки
		$rkms_conf[5007153]=1;	//Вой Грифона					
		$rkms_conf[5017153]=3;	//Вой Грифона

		//Арес
		$rkms_conf[155]=1; //'Малый свиток «Гнев Ареса»';	
		$rkms_conf[156]=5; //'Совершенный свиток «Гнев Ареса»';	
		$rkms_conf[157]=3; //'Большой свиток «Гнев Ареса»';
		$rkms_conf[158]=2; //'Средний свиток «Гнев Ареса»';

		//абилки
		$rkms_conf[5007152]=1; //'Гнев Ареса';	
		$rkms_conf[5017152]=3; //'Гнев Ареса';						
				
		//химера
		$rkms_conf[925]=1; //'Малый свиток «Обман Химеры»';	
		$rkms_conf[926]=5; //'Совершенный свиток «Обман Химеры»';
		$rkms_conf[927]=3; //'Большой свиток «Обман Химеры»';
		$rkms_conf[928]=2; //'Средний свиток «Обман Химеры»';
	
		//абилки
		$rkms_conf[5007154]=1; //'Обман Химеры';
		$rkms_conf[5017154]=3; //'Обман Химеры';
		
		//гидра
		$rkms_conf[935]=1; //'Малый свиток «Укус Гидры»';	
		$rkms_conf[936]=5; //'Совершенный свиток «Укус Гидры»';	
		$rkms_conf[937]=3; //'Большой свиток «Укус Гидры»';	
		$rkms_conf[938]=2; //'Средний свиток «Укус Гидры»';
	
		//абилки
		$rkms_conf[5007155]=1; //'Укус Гидры';
		$rkms_conf[5017155]=3; //'Укус Гидры';
		
		if (isset($rkms_conf[$idmag])) {
			$rkm=$rkms_conf[$idmag];
		}
				
		return $rkm;				
	}

}