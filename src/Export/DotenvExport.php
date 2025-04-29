<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Export;

use WebChemistry\Definition\Key;

final class DotenvExport extends Exporter
{

	public function __construct(
		private readonly string $file,
		private readonly ?string $prefix = null,
	)
	{
	}

	public function getFile(): string
	{
		return $this->file;
	}

	protected function generate(array $records): string
	{
		$template = $this->createTemplateForRecords(
			$records,
			fn (Key $key): string => strtoupper($this->addPrefix($key)->snake()),
			fn (string|int|float|bool $value): string => $this->escapeValue($this->stringifyValue($value)),
		);

		return $template("{{key}}={{value}}\n");
	}

	private function stringifyValue(float|bool|int|string $value): string
	{
		if (is_bool($value)) {
			return $value ? '1' : '0';
		}

		return (string) $value;
	}

	private function addPrefix(Key $key): Key
	{
		if ($this->prefix === null) {
			return $key;
		}

		if ($key->prefix === null) {
			return $key->withPrefix($this->prefix);
		}

		return $key->withPrefix($this->prefix . '_' . $key->prefix, true);
	}

	private function escapeValue(string $value): string
	{
		if (preg_match('#^\w+$#', $value)) {
			return $value;
		}

		return var_export($value, true);
	}

}
