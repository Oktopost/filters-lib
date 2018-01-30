<?php
namespace FiltersLib\Utils;


class IdGenerator
{
	public static function generateId(): string
	{
		$milliseconds = (int)round(microtime(true) * 1000);
		$random = mt_rand(100000, 1000000) . mt_rand(100000, 1000000);
		$result = base_convert($milliseconds . $random, 10, 36);
		
		return substr($result, strlen($result) - 12);
	}
}