<?php
namespace FiltersLib\Utils;


class HashGenerator
{
	private const BASE	= 62;
	private const CHARS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	
	private static function dec2any($num, int $length): string
	{
		$result = "";
		
		for ($t = floor(log10($num) / log10(self::BASE)); $t >= 0; $t--)
		{
			$a = floor($num / pow(self::BASE, $t));
			$result = $result . substr(self::CHARS, $a, 1);
			$num = $num - ($a * pow(self::BASE, $t));
		}
		
		$resultLength = strlen($result);
		
		if ($resultLength < $length)
		{
			$missing = $length - $resultLength;
			$extra = self::generate($result, $missing);
			$result .= substr($extra, 0, $missing);
		}
		else if ($resultLength > $length)
		{
			$result = substr($result, 0, $length);
		}
		
		return $result;
	}
	
	public static function generate(string $data, ?int $length = 5): string
	{
		$binHash = md5($data, true);
		$numHash = unpack('Q', $binHash);
		
		return self::dec2any($numHash[1] & 0x000FFFFF, $length);
	}
}