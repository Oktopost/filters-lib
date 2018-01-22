<?php
namespace FiltersLib\Base;


use FiltersLib\Base\DAO\IFiltersDAO;


interface IDAODecorator extends IFiltersDAO
{
	public function setChild(IFiltersDAO $child): void;
}