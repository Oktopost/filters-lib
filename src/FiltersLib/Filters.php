<?php
namespace FiltersLib;


use FiltersLib\Base\IData;
use FiltersLib\Base\IDAODecorator;
use FiltersLib\Base\DAO\IFiltersDAO;
use FiltersLib\Utils\ToString;
use FiltersLib\Config\FilterConfig;

use Objection\Mapper;
use Objection\LiteObject;

use Squid\MySql;


class Filters
{
	/** @var FilterConfig */
	private $config;
	
	
	private function dao(): IFiltersDAO
	{
		return $this->config->getDao();
	}
	
	
	/**
	 * @param MySql|IFiltersDAO|array|null $subject
	 * @param string|null $tableName
	 */
	public function __construct($subject = null, ?string $tableName = null)
	{
		$this->config = new FilterConfig();
		
		if ($subject)
		{
			$this->config->setup($subject, $tableName);
		}
	}
	
	
	/**
	 * @param MySql|IFiltersDAO|IDAODecorator|array $subject
	 * @param string|null $tableName
	 * @return static
	 */
	public function setup($subject, ?string $tableName = null)
	{
		$this->config->setup($subject, $tableName);
		return $this;
	}
	
	public function get($payload, $meta = null): Record
	{
		$record = $this->dao()->getByData(ToString::convert($payload), ToString::convert($meta));
		return $record;
	}
	
	public function getId($payload, $meta = null): string
	{
		return $this->get($payload, $meta)->Id;
	}
	
	public function getByHash(string $hash): ?Record
	{
		return $this->dao()->getByHash($hash);
	}
	
	/**
	 * @param string $id
	 * @param string|Mapper $opt
	 * @return \Objection\LiteObject|null
	 */
	public function getObject(string $id, $opt): ?LiteObject
	{
		$result = $this->dao()->getById($id);
		return $result ? $result->Payload->asLiteObject($opt) : null;		
	}
	
	public function getString(string $id): ?string
	{
		$result = $this->dao()->getById($id);
		return $result ? $result->Payload->asString() : null;
	}
	
	public function getData(string $id): ?IData
	{
		$result = $this->dao()->getById($id);
		return $result ? $result->Payload : null;
	}
	
	public function getRecord(string $id): ?Record
	{
		return $this->dao()->getById($id);
	}
}