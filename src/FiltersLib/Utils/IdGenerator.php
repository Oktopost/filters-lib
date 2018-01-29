<?php
namespace FiltersLib\Utils;


class IdGenerator
{
	public static function generateId(string $hash): string
	{
		$milliseconds = (int)round(microtime(true) * 1000);
		$string = md5($hash) . base_convert($milliseconds, 10, 36) . mt_rand(100000, 1000000);
		
		$result = substr($string, 0, 35);
		
		return $result;
	}
}