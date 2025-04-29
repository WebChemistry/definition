<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Definition;

use InvalidArgumentException;

final readonly class DefinitionList
{

	/**
	 * @param array<string, Definition> $list
	 */
	public function __construct(
		public array $list,
	)
	{
	}

	public function get(string $name): Definition
	{
		if (!isset($this->list[$name])) {
			throw new InvalidArgumentException(sprintf('Definition variable "%s" is not defined.', $name));
		}

		return $this->list[$name];
	}

	/**
	 * @template T of Definition
	 * @param class-string<T> $class
	 * @return T
	 */
	public function getInstanceOf(string $class, string $name): Definition
	{
		$definition = $this->get($name);

		if (!$definition instanceof $class) {
			throw new InvalidArgumentException(sprintf('Definition variable "%s" is not instance of "%s", "%s" given.', $name, $class, $definition::class));
		}

		return $definition;
	}

}
