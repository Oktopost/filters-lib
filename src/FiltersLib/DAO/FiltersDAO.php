<?php
namespace FiltersLib\DAO;


use FiltersLib\Record;
use FiltersLib\Base\DAO\IFiltersDAO;
use FiltersLib\Utils\IdGenerator;
use FiltersLib\Utils\HashGenerator;

use Squid\MySql;


class FiltersDAO implements IFiltersDAO
{
	/** @var MySql\IMySqlConnector */
	private $connector;
	
	/** @var string */
	private $tableName;
	
	
	private function getByHash(string $hash): array
	{
		return $this->connector
			->select()
			->from($this->tableName)
			->byField('Hash', $hash)
			->query();
	}
	
	
	private function getLockName(string $data): string
	{
		return 'FiltersLib.Lock.' . $this->tableName . '.' . $data;
	}
	
	private function unsafeInsert(Record $record): void
	{
		$idExists = true;
		
		for ($i = 0; ($i < 3 && $idExists); $i++)
		{
			$record->Id = IdGenerator::generateId($record->Hash);
			
			$idExists = $this->connector->select()
				->from($this->tableName)
				->byId($record->Id)
				->queryExists();
		}
		
		if ($idExists)
		{
			throw new \Exception('Failed to generate Id for hash ' . $record->Hash);
		}
		
		$this->connector
			->insert()
			->into($this->tableName)
			->values($record->toRawData())
			->executeDml();
	}
	
	private function create(string $hash, string $payload, ?string $meta = null): Record
	{
		$record = new Record();
		
		$record->Payload	= $payload;
		$record->Metadata	= $meta;
		$record->Hash		= $hash;
		
		$lockName = $this->getLockName($hash);
		
		$lock = $this->connector->lock();
		
		if (!$lock->lock($lockName))
			throw new \Exception('Error while trying to lock ' . $lockName);
		
		try
		{
			return $this->unsafeInsert($record);
		}
		finally
		{
			$lock->unlock($lockName);
		}
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
	
	private function loadRecord(?array $raw = null): Record
	{
		if (!$raw)
			return null;
		
		$this->touch($raw['Id']);
		
		return Record::createFromRawData($raw);
	}
	
	
	public function getById(string $id): ?Record
	{
		$result = $this->connector
			->select()
			->from($this->tableName)
			->byField('Id', $id)
			->queryRow(true);
		
		return $this->loadRecord($result);
	}
	
	public function getByData(string $payload, ?string $meta = null): Record
	{
		$hash = HashGenerator::generate($payload . $meta);
		$records = $this->getByHash($hash);
		$match = null;
		
		foreach ($records as $record)
		{
			if ($record['Payload'] == $payload && $record['Metadata'] === $meta)
			{
				$match = $record;
				break;
			}
		}
		
		if (!$match)
		{
			return $this->create($hash, $payload, $meta);
		}
		
		return $this->loadRecord($match);
	}
}