<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Export;

use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;
use WebChemistry\Definition\Record;

final class PhpExport extends Exporter
{

	public function __construct(
		private readonly string $file,
		private readonly string $className,
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
		$file = new PhpFile();
		$file->setStrictTypes();

		[$namespaceName, $className] = $this->extractNamespaceAndClassName($this->className);

		if ($namespaceName !== null) {
			$namespace = $file->addNamespace($namespaceName);
			$class = $namespace->addClass($className);
		} else {
			$class = $file->addClass($className);
		}

		$class->setFinal();

		foreach ($records as $record) {
			$type = match (gettype($record->value)) {
				'string' => 'string',
				'integer' => 'int',
				'double' => 'float',
				'boolean' => 'bool', // @phpstan-ignore match.alwaysTrue
				default => 'mixed',
			};

			$class->addConstant($record->key->camelCase(true), $record->value)
				->setType($type)
				->setPublic();
		}

		$printer = new Printer();

		return $printer->printFile($file);
	}

	/**
	 * @return array{?string, string}
	 */
	protected function extractNamespaceAndClassName(string $fullName): array
	{
		$pos = strrpos($fullName, '\\');

		if ($pos === false) {
			return [null, $fullName];
		}

		$namespace = substr($fullName, 0, $pos);
		$className = substr($fullName, $pos + 1);

		return [$namespace, $className];
	}

}
