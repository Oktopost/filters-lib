<?php
namespace FiltersLib\Base\DAO;


use FiltersLib\Record;


interface IFiltersDAO
{
	public function getByData(string $payload, ?string $meta = null): Record;
	public function getById(string $id): ?Record;
}