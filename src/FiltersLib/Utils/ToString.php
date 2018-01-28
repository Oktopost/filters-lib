<?php
namespace FiltersLib\Utils;


use FiltersLib\Base\IData;

use Objection\Mapper;
use Objection\LiteObject;


class ToString
{
	/**
	 * @param string|LiteObject|IData|array|\stdClass|null $data
	 * @return string
	 */
	public static function convert($data): ?string
	{
		if (is_string($data))
		{
			return $data;
		}
		else if ($data instanceof LiteObject)
		{
			return Mapper::getJsonFor($data);
		}
		else if ($data instanceof IData)
		{
			return $data->asString();
		}
		else if (is_scalar($data) || is_array($data) || is_object($data))
		{
			return json_encode($data);
		}
		else if (is_null($data))
		{
			return null;
		}
		else
		{
			throw new \Exception('Unsupported data type');
		}
	}
}