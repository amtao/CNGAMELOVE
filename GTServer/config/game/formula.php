<?php
class formula{
	public function linklink_times($acount){
		return ($acount+1)*(1+ceil(($acount+1)/5))*5;
	}

	public function linklink_right($prop){
		return ceil($prop/10);
	}

	public function prentice_prop_add($level,$love){
		return min(10,ceil(($level/10+1)*($love*0.0001+0.5)));
	}

	public function partner_prop($level,$zz_star,$zz_lv){
		return (9+$level)*$zz_star+$zz_lv*$zz_star*(floor($level/50)+1);
	}

	public function club_boss_gx($bosshp){
		return $bosshp/250;
	}

	public function gongdou_attk($zz_sum){
		return $zz_sum*4;
	}

	public function gongdou_hp($hero_prop){
		return $hero_prop;
	}

	public function tidy_chance_price($tidy_chance){
		return 5;
	}

	public function gongdou_cost($gongdou_cd_count){
		return 100*($gongdou_cd_count+1);
	}

	public function xianli_haogan($hero_damage){
		return max(1, min(round($hero_damage / 10), 1000));
	}

	public function kitchen_exp($kitchen_time){
		return $kitchen_time/30;
	}

	public function jingying_time($get_res){
		return min(ceil(($get_res)/(1000*(1+($get_res)/50000)))*60,1800);
	}

	public function city_lucky($get_res,$acount){
		return ceil(min($acount,15)*0.3*$get_res);
	}

	public function flower_cost($today_count){
		return pow(floor($today_count/10),2)*2000+$today_count*1000;
	}

	public function wife_meet_cost($wife_nengli,$wife_love){
		return min((ceil(((pow($wife_nengli,0.7)*0.55)+($wife_love*0.025))/5)*5),1000);
	}

	public function wife_chuyou_cost($wife_love){
		return min((ceil(pow($wife_love,0.585))*5)+10,3000);
	}

	public function tree_yb($yb_acount){
		return floor(($yb_acount-1)/10)*30+30;
	}

	public function tree_ms1($ms_acount){
		return floor(($ms_acount-1)/10)*30000+30000;
	}

	public function tree_ms2($ms_acount){
		return ceil((pow(floor(($ms_acount-1)/10),1.5)*15000+50000)/10000)*10000;
	}

}
