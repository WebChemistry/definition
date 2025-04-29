<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Environment;

use WebChemistry\Definition\Definition\ComputedDefinition;
use WebChemistry\Definition\Definition\Definition;
use WebChemistry\Definition\Definition\DefinitionList;
use WebChemistry\Definition\Definition\ValueDefinition;

readonly class Definitions
{

	public function val(string|int|float|bool $value): ValueDefinition
	{
		return new ValueDefinition($value);
	}

	/**
	 * @template T of Definition
	 * @param T|ComputedDefinition<T> $definition
	 * @param callable(T, DefinitionList, string): T $fn
	 * @return ComputedDefinition<T>
	 */
	public function computed(Definition $definition, callable $fn): ComputedDefinition
	{
		return new ComputedDefinition($definition, $fn);
	}

}
