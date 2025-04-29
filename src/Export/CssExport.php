<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Export;

use WebChemistry\Definition\Record;

final class CssExport extends Exporter
{

	public function __construct(
		private readonly string $file,
		private readonly string $selector = ':root',
	)
	{
	}

	public function getFile(): string
	{
		return $this->file;
	}

	/**
	 * @param list<Record> $records
	 */
	protected function generate(array $records): string
	{
		$str = sprintf("%s {\n", $this->selector);

		foreach ($records as $record) {
			$str .= sprintf("\t--%s: %s;\n", $this->checkKey($record->key->kebab()), $record->value);
		}

		$str .= "}\n";

		return $str;
	}

}
