<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Export;

use WebChemistry\Definition\Key;
use WebChemistry\Definition\Record;

final class ScssExport extends Exporter
{

	public function __construct(
		private readonly string $file,
		private readonly ?string $exportAsVariable = null,
		private readonly ?string $exportAsVariableList = null,
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
		$template = $this->createTemplateForRecords(
			$records,
			fn (Key $key) => $key->kebab(),
			fn (string|int|float|bool $value) => is_string($value) && str_contains($value, ',') ? $this->likeJson($value) : $value,
		);

		if ($name = $this->exportAsVariableList) {
			return
				"\$$name: (\n" .
				$template("\t--{{key}}\n") .
				");";
		}

		if ($name = $this->exportAsVariable) {
			return
				"\$$name: (\n" .
				$template("\t\"{{key}}\": {{value}},\n") .
				");";
		}

		return $template("\${{key}}: {{value}};\n");
	}

}
