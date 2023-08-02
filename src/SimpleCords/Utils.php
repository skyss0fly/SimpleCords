<?php

declare(strict_types=1);

namespace SimpleCords;

use function array_map;
use function fmod;
use function implode;
use function number_format;

class Utils{

	public static function getCompassDirection(float $deg) : string{
		$deg = fmod($deg, 360);
		if($deg < 0){
			$deg += 360;
		}

		if(22.5 <= $deg and $deg < 67.5){
			return "Southwest";
		}elseif(67.5 <= $deg and $deg < 112.5){
			return "West";
		}elseif(112.5 <= $deg and $deg < 157.5){
			return "Northwest";
		}elseif(157.5 <= $deg and $deg < 202.5){
			return "North";
		}elseif(202.5 <= $deg and $deg < 247.5){
			return "Northeast";
		}elseif(247.5 <= $deg and $deg < 292.5){
			return "East";
		}elseif(292.5 <= $deg and $deg < 337.5){
			return "Southeast";
		}else{
			return "South";
		}
	}

	public static function getFormattedCoords(int $precision, float ...$coords) : string{
		return implode(", ", array_map(fn(float $c) => number_format($c, $precision), $coords));
	}
}
