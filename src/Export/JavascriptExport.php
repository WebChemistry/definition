<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Export;

final class JavascriptExport extends Exporter
{

	/**
	 * @param 'module'|'commonjs'|'typescript' $type
	 */
	public function __construct(
		private readonly string $file,
		private readonly ?string $name = null,
		private readonly string $type = 'module',
	)
	{
	}

	public function getFile(): string
	{
		return $this->file;
	}

	protected function generate(array $records): string
	{
		$str = '';

		if ($this->type === 'commonjs') {
			$str .= 'module.exports = {';
		} else if ($this->name) {
			$str .= sprintf('export const %s = {', $this->name);
		} else {
			$str .= 'export default {';
		}

		$str .= "\n";

		foreach ($records as $record) {
			$str .= sprintf("\t%s: %s,\n", $this->checkKey($record->key->camelCase(true)), $this->likeJson($record->value));
		}

		$str .= "}";

		if ($this->type === 'typescript') {
			$str .= ' as const';
		}

		$str .= ";\n";

		return $str;
	}

}
