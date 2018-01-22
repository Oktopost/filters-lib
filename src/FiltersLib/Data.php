<?php
namespace FiltersLib;


use FiltersLib\Base\IData;

use Objection\Mapper;
use Objection\LiteObject;


class Data implements IData
{
	/** @var string  */
	private $data;
	
	
	public function __construct(string $data)
	{
		$this->data = $data;
	}
	
	
	public function asString(): ?string
	{
		return $this->data;
	}
	
	/**
	 * @param string|Mapper $mapper
	 * @return null|LiteObject
	 */
	public function asLiteObject($mapper): ?LiteObject
	{
		if (is_string($mapper))
		{
			$mapper = Mapper::createFor($mapper);
		}
		
		return $mapper->getObject($this->data);
	}
	
	public function asStdClass(): ?\stdClass
	{
		return json_decode($this->data);
	}
	
	public function asArray(): ?array
	{
		return json_decode($this->data, true);
	}
	
	public function isEmpty(): bool
	{
		return (bool)$this->data;
	}
}