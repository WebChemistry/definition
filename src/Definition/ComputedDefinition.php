<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Definition;

/**
 * @template T of Definition
 */
final readonly class ComputedDefinition implements Definition
{

	/** @var callable(T, DefinitionList, string): T */
	private mixed $fn;

	/**
	 * @param T|ComputedDefinition<T> $definition
	 * @param callable(T, DefinitionList, string): T $fn
	 */
	public function __construct(
		private Definition $definition,
		callable $fn,
	)
	{
		$this->fn = $fn;
	}

	/**
	 * @return T
	 */
	public function compute(string $name, DefinitionList $definitions): Definition
	{
		$def = $this->definition;

		if ($def instanceof ComputedDefinition) {
			$def = $def->compute($name, $definitions);
		}

		return ($this->fn)($def, $definitions, $name); // @phpstan-ignore callable.nonCallable
	}

	public function getRecords(string $name, DefinitionList $definitions): iterable
	{
		yield from $this->compute($name, $definitions)->getRecords($name, $definitions);
	}

}
