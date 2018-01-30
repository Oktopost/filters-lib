<?php
namespace FiltersLib\Utils;


class HashGenerator
{
	public static function generate(string $data): string
	{
		return md5($data);
	}
}