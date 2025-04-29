<?php declare(strict_types = 1);

namespace WebChemistry\Definition;

use Symfony\Component\String\ByteString;

final readonly class Key
{

	public function __construct(
		public string $name,
		public ?string $modifier = null,
		public ?string $variant = null,
		public ?string $prefix = null,
	)
	{
	}

	public function snake(): string
	{
		$convert = static fn (string $str): string => (new ByteString($str))->snake()->toString();

		$key = $convert(implode('_', $this->getParts()));

		return $this->addModifier($key, $convert);
	}

	public function kebab(): string
	{
		$convert = static fn (string $str): string => (new ByteString($str))->kebab()->toString();

		$key = $convert(implode('-', $this->getParts()));

		return $this->addModifier($key, $convert);
	}

	public function camelCase(bool $firstUpper = false): string
	{
		$convert = static function (string $str) use ($firstUpper): string {
			$byteString = new ByteString($str);
			$byteString = $byteString->camel();

			if ($firstUpper) {
				$byteString = $byteString->title();
			}

			return $byteString->toString();
		};

		return $this->addModifier($convert(implode('-', $this->getParts())), $convert);
	}

	/**
	 * @param callable(string): string $convert
	 */
	private function addModifier(string $key, callable $convert): string
	{
		if ($this->modifier !== null) {
			$key .= '__' . $convert($this->modifier);
		}

		return $key;
	}

	/**
	 * @return string[]
	 */
	private function getParts(): array
	{
		$parts = [];

		if ($this->prefix !== null) {
			$parts[] = $this->prefix;
		}

		if ($this->variant !== null) {
			$parts[] = $this->variant;
		}

		$parts[] = $this->name;

		return $parts;
	}

	public function withoutVariant(): Key
	{
		return new self($this->name, $this->modifier, null, $this->prefix);
	}

	public function withPrefix(string $prefix, bool $rewrite = false): self
	{
		if (!$rewrite && $this->prefix !== null) {
			throw new \LogicException('Key already has prefix.');
		}

		return new self($this->name, $this->modifier, $this->variant, $prefix);
	}

}
