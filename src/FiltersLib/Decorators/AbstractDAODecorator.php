<?php
namespace FiltersLib\Decorators;


use FiltersLib\Base\IDAODecorator;
use FiltersLib\Base\DAO\IFiltersDAO;

use FiltersLib\Record;


abstract class AbstractDAODecorator implements IDAODecorator
{
	/** @var IFiltersDAO */
	private $child;
	
	
	protected function getChild(): ?IFiltersDAO
	{
		return $this->child;
	}
	
	
	public function setChild(IFiltersDAO $child): void
	{
		$this->child = $child;
	}
	
	public function getById(string $id): ?Record
	{
		return $this->child->getById($id);
	}
	
	public function getByHash(string $hash): ?Record
	{
		return $this->child->getByHash($hash);
	}
	
	public function getByData(string $payload, ?string $meta = null): Record
	{
		return $this->child->getByData($payload, $meta);
	}
}