<?php
namespace FiltersLib\DAO;


use FiltersLib\Record;
use FiltersLib\Base\DAO\IFiltersDAO;

use Squid\MySql;


class FiltersDAO implements IFiltersDAO
{
	/** @var MySql\IMySqlConnector */
	private $connector;
	
	/** @var string */
	private $tableName;
	
	
	private function generateHash(string $data): string
	{
		return base_convert(md5($data), 16, 36);
	}
	
	private function generateId(string $hash): string
	{
		$milliseconds = (int)round(microtime(true) * 1000);
		$string = $hash . base_convert($milliseconds, 10, 36) . mt_rand(100000, 1000000);
		
		$result = substr($string, 0, 35);
		
		return $result;
	}
	
	private function getLockName(string $data): string
	{
		return 'FiltersLib.Lock.' . $this->tableName . '.' . $data;
	}
	
	private function create(string $hash, string $payload, ?string $meta = null): Record
	{
		$result = null;
		
		$newRecord = (new Record())->fromArray([
			'Payload'	=> $payload,
			'Metadata'	=> $meta,
			'Hash'		=> $hash,
		]);
		
		$newRecord->Id = $this->generateId($hash);
		
		$data = $newRecord->toArray();
		
		unset($data['Created']);
		unset($data['Touched']);
		
		$this->connector->lock()->safe(
			function() 
				use ($data)
			{
				$this->connector->insert()->into($this->tableName)->values($data)->executeDml();
			}, 
			$this->getLockName($hash));
		
		return $newRecord;
	}
	
	private function touch(string $id): void
	{
		$this->connector->update()->table($this->tableName)
			->setExp('Touched', 'NOW()')
			->byField('Id', $id)
			->executeDml();
	}
	
	
	/**
	 * @param Mysql|MySql\IMySqlConnector $conn
	 * @param string $tableName
	 */
	public function __construct($conn, string $tableName)
	{
		if ($conn instanceof MySql\IMySqlConnector)
		{
			$this->connector = $conn;
		}
		else if ($conn instanceof MySql)
		{
			$this->connector = $conn->getConnector();
		}
		else
		{
			throw new \Exception('Connector is not valid');
		}
		
		$this->tableName = $tableName;
	}
	
	
	public function getById(string $id): ?Record
	{
		$result = $this->connector
			->select()
			->from($this->tableName)
			->byField('Id', $id)
			->queryRow(true);
		
		if ($result)
		{
			$this->touch($id);
			return (new Record())->fromArray($result);
		}
		
		return null;
	}
	
	public function getByHash(string $hash): ?Record
	{
		$result = $this->connector
			->select()
			->from($this->tableName)
			->byField('Hash', $hash)
			->queryRow(true);
		
		if (!$result)
		{
			return null;
		}

		$this->touch($result['Id']);
		return (new Record())->fromArray($result);
	}
	
	public function getByData(string $payload, ?string $meta = null): Record
	{
		$hash = $this->generateHash($payload . $meta);
		return $this->getByHash($hash) ?: $this->create($hash, $payload, $meta);
	}
}