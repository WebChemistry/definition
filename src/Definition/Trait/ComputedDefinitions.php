<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Definition\Trait;

use WebChemistry\Definition\Definition\DefinitionList;

/**
 * @template T
 * @internal
 */
trait ComputedDefinitions
{

	/** @var list<callable(T, DefinitionList, string): T> */
	private array $computed = [];

	/**
	 * @param callable(T, DefinitionList, string): T ...$fns
	 */
	public function computed(callable ...$fns): static
	{
		foreach ($fns as $fn) {
			$this->computed[] = $fn;
		}

		return $this;
	}

	/**
	 * @param T $value
	 * @return T
	 */
	private function compute(mixed $value, DefinitionList $definitions, string $name): mixed
	{
		foreach ($this->computed as $fn) {
			$value = $fn($value, $definitions, $name);
		}

		return $value;
	}

}
