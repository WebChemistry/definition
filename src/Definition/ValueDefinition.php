<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Definition;

use WebChemistry\Definition\Key;
use WebChemistry\Definition\Record;

final readonly class ValueDefinition implements Definition
{

	public function __construct(
		private string|int|float|bool $value,
	)
	{
	}

	public function getRecords(string $name, DefinitionList $definitions): iterable
	{
		yield new Record(new Key($name), $this->value);
	}

}
