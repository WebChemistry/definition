<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Definition;

use WebChemistry\Definition\Record;

interface Definition
{

	/**
	 * @return iterable<Record>
	 */
	public function getRecords(string $name, DefinitionList $definitions): iterable;

}
