<?php
namespace FiltersLib\Config;


use FiltersLib\DAO\FiltersDAO;
use FiltersLib\Base\IDAODecorator;
use FiltersLib\Base\DAO\IFiltersDAO;

use Squid\MySql;


class FilterConfig
{
	/** @var IFiltersDAO */
	private $dao;
	
	
	/**
	 * @return mixed
	 */
	public function getDao()
	{
		if (!$this->dao)
			throw new \Exception('DAO is not set');
		
		return $this->dao;
	}
	
	
	/**
	 * @param MySql|IFiltersDAO|array $subject
	 * @param string|null $tableName
	 */
	public function setup($subject, ?string $tableName = null): void
	{
		if (($subject instanceof MySql || $subject instanceof MySql\IMySqlConnector)&& $tableName)
		{
			$this->dao = new FiltersDAO($subject, $tableName);
		}
		else if ($subject instanceof IFiltersDAO)
		{
			if ($subject instanceof IDAODecorator)
			{
				$subject->setChild($this->getDao());
			}
			
			$this->dao = $subject;
		}
		else if (is_array($subject) && $tableName)
		{
			$mysql = new MySql();
			$mysql->config()->addConfig($subject);
			
			$this->dao = new FiltersDAO($mysql, $tableName);
		}
		else if (is_string($subject) && class_exists($subject))
		{
			$this->setup(new $subject());
		}
		else
		{
			throw new \Exception('Failed to setup FiltersLib');
		}
	}
}