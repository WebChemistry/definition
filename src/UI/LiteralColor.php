<?php declare(strict_types = 1);

namespace WebChemistry\Definition\UI;

use RuntimeException;

final readonly class LiteralColor implements ColorType
{

	public function __construct(
		private string $value,
	)
	{
	}

	public function lighten(int $amount): ColorType
	{
		$this->error(__METHOD__);
	}

	public function darken(int $amount): ColorType
	{
		$this->error(__METHOD__);
	}

	public function __toString(): string
	{
		return $this->value;
	}

	public function toHex(): string
	{
		$this->error(__METHOD__);
	}

	private function error(string $method): never
	{
		throw new RuntimeException(sprintf('Method %s is not supported for literal colors', $method));
	}

}
