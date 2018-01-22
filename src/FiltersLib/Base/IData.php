<?php
namespace FiltersLib\Base;


use Objection\Mapper;
use Objection\LiteObject;


interface IData
{
	public function asString(): ?string;
	
	/**
	 * @param string|Mapper $mapper
	 * @return null|LiteObject
	 */
	public function asLiteObject($mapper): ?LiteObject;
	
	public function asStdClass(): ?\stdClass;
	public function asArray(): ?array;
	public function isEmpty(): bool;
}