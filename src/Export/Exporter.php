<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Export;

use RuntimeException;
use WebChemistry\Definition\Key;
use WebChemistry\Definition\Record;

abstract class Exporter
{

	/** @var list<callable(Record $record): bool> */
	protected array $recordFilters = [];

	/** @var list<callable(Record $record): Record> */
	protected array $recordMappers = [];

	/** @var array<string, bool> */
	private array $keys = [];

	/**
	 * @param string|string[] $variants
	 */
	public function variantEqualsTo(string|array $variants): self
	{
		$variants = is_array($variants) ? $variants : [$variants];

		$this->recordFilters[] = static fn (Record $record): bool => in_array($record->key->variant, $variants, true);

		return $this;
	}

	/**
	 * @param string|string[] $categories
	 */
	public function categoryEqualsTo(string|array $categories): self
	{
		$categories = is_array($categories) ? $categories : [$categories];

		$this->recordFilters[] = static fn (Record $record): bool => in_array($record->category, $categories, true);

		return $this;
	}

	public function omitVariantInKey(): self
	{
		$this->recordMappers[] = static function (Record $record): Record {
			return new Record($record->key->withoutVariant(), $record->value);
		};

		return $this;
	}

	abstract public function getFile(): string;

	/**
	 * @param list<Record> $records
	 */
	final public function export(array $records): string
	{
		$records = iterator_to_array($this->filter($records), false);

		return $this->generate($records);
	}

	/**
	 * @param list<Record> $records
	 */
	abstract protected function generate(array $records): string;

	/**
	 * @param list<Record> $records
	 * @return iterable<Record>
	 */
	private function filter(array $records): iterable
	{
		foreach ($records as $record) {
			foreach ($this->recordFilters as $filter) {
				if (!$filter($record)) {
					continue 2;
				}
			}

			foreach ($this->recordMappers as $mapper) {
				$record = $mapper($record);
			}

			yield $record;
		}
	}

	public function withKeyPrefix(string $prefix): self
	{
		$this->recordMappers[] = static function (Record $record) use ($prefix): Record {
			return new Record($record->key->withPrefix($prefix), $record->value);
		};

		return $this;
	}

	protected function checkKey(string $key): string
	{
		if (isset($this->keys[$key])) {
			throw new RuntimeException(sprintf('Key "%s" is already defined.', $key));
		}

		$this->keys[$key] = true;

		return $key;
	}

	/**
	 * @param list<Record> $records
	 * @param callable(Key): string $keyToString
	 * @param (callable(scalar): scalar)|null $onValue
	 * @return callable(string $template): string
	 */
	protected function createTemplateForRecords(array $records, callable $keyToString, ?callable $onValue = null): callable
	{
		return function (string $template) use ($records, $keyToString, $onValue): string {
			$str = '';

			foreach ($records as $record) {
				$key = $this->checkKey($keyToString($record->key));
				$value = $record->value;

				if ($onValue) {
					$value = $onValue($value);
				}

				$str .= strtr($template, [
					'{{key}}' => $key,
					'{{value}}' => $value,
				]);
			}

			return $str;
		};
	}

	protected function likeJson(mixed $value): string
	{
		$ret = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

		if ($ret === false) {
			throw new RuntimeException('Failed to encode value to JSON: ' . json_last_error_msg());
		}

		return $ret;
	}

}
