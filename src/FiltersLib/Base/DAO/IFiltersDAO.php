<?php
namespace FiltersLib\Base\DAO;


use FiltersLib\Record;


interface IFiltersDAO
{
	public function getById(string $id): ?Record;
	public function getByHash(string $hash): ?Record;
	public function getByData(string $payload, ?string $meta = null): Record;
}