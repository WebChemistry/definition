<?php declare(strict_types = 1);

namespace WebChemistry\Definition;

final readonly class Record
{

	/**
	 * @param string[] $tags
	 */
	public function __construct(
		public Key $key,
		public string|int|float|bool $value,
		public ?string $category = null,
		public array $tags = [],
	)
	{
	}

	public function withCategory(string $category): self
	{
		return new self($this->key, $this->value, $category);
	}

	/**
	 * @param string[] $tags
	 */
	public function withTags(array $tags): self
	{
		return new self($this->key, $this->value, $this->category, $tags);
	}

}
