<?php declare(strict_types = 1);

namespace WebChemistry\Definition;

final readonly class GenerationConfig
{

	/**
	 * @param string[] $tags
	 */
	public function __construct(
		public ?string $prefix = null,
		public ?string $category = null,
		public array $tags = [],
	)
	{
	}

}
