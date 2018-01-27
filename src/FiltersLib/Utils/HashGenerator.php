<?php
namespace FiltersLib\Utils;


class HashGenerator
{
	private const BASE	= 62;
	private const CHARS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	
	private static function dec2any(int $num, int $length): string
	{
		$result = '';
		
		while ($num)
		{
			$result = self::CHARS[$num % self::BASE] . $result;
			$num = floor($num / self::BASE);
		}
		
		$resultLength = strlen($result);
		
		if ($resultLength < $length)
		{
			$missing = $length - $resultLength;
			$extra = self::generate($result, $missing);
			$result .= substr($extra, 0, $missing);
		}
		
		return $result;
	}
	
	
	public static function generate(string $data, ?int $length = 6): string
	{
		$numHash = unpack('V', md5($data, true));
		return self::dec2any($numHash[1], $length);
	}
}