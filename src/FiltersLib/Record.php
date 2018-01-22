<?php
namespace FiltersLib;


use FiltersLib\Base\IData;

use Objection\LiteSetup;
use Objection\LiteObject;


/**
 * @property string $Id
 * @property string $Created
 * @property string $Touched
 * @property string $Hash
 * @property IData $Payload
 * @property IData|null $Metadata
 */
class Record extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Id'		=> LiteSetup::createString(),
			'Created'	=> LiteSetup::createString(),
			'Touched'	=> LiteSetup::createString(),
			'Hash'		=> LiteSetup::createString(),
			'Payload'	=> LiteSetup::createInstanceOf(IData::class),
			'Metadata'	=> LiteSetup::createInstanceOf(IData::class)
		];
	}
	
	
	public function hasMetadata(): bool
	{
		return (bool)$this->Metadata;
	}
	
	public function fromArray($source, $ignoreGetOnly = true)
	{
		if (array_key_exists('Payload', $source))
		{
			$source['Payload'] = new Data($source['Payload']);
		}
		
		if (isset($source['Metadata']))
		{
			$source['Metadata'] = new Data($source['Metadata']);
		}
		
		return parent::fromArray($source, $ignoreGetOnly);
	}
	
	public function toArray(array $filter = [], array $exclude = [])
	{
		$result = parent::toArray($filter, $exclude);
		
		$result['Payload'] = $this->Payload->asString();
		
		if ($this->hasMetadata())
		{
			$result['Metadata'] = $this->Metadata->asString();
		}
		
		return $result;
	}
}