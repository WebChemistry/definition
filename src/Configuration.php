<?php declare(strict_types = 1);

namespace WebChemistry\Definition;

use WebChemistry\Definition\Definition\Definition;
use WebChemistry\Definition\Definition\DefinitionList;
use WebChemistry\Definition\Environment\Definitions;
use WebChemistry\Definition\Environment\Theme;

final class Configuration
{

	/** @var list<array{callable(Theme $theme): array<string, mixed>, GenerationConfig}> */
	private array $theme = [];

	/** @var list<array{callable(Definitions $defs): array<string, mixed>, GenerationConfig}> */
	private array $misc = [];

	private function __construct()
	{
	}

	/**
	 * @param string[] $tags
	 */
	public function config(?string $prefix = null, ?string $category = null, array $tags = []): GenerationConfig
	{
		return new GenerationConfig($prefix, $category, $tags);
	}

	public function defineTheme(callable $fn, ?GenerationConfig $config = null): self
	{
		$this->theme[] = [$fn, $config ?? $this->config()];

		return $this;
	}

	public function defineMisc(callable $fn, ?GenerationConfig $config = null): self
	{
		$this->misc[] = [$fn, $config ?? $this->config()];

		return $this;
	}

	/**
	 * @return list<Record>
	 */
	private function run(): array
	{
		$theme = new Theme();
		$defs = new Definitions();

		/** @var list<Record> $records */
		$records = [];

		foreach ($this->theme as [$fn, $config]) {
			$vals = $fn($theme);

			foreach ($this->getRecords($vals, $config) as $record) {
				$records[] = $record;
			}
		}

		foreach ($this->misc as [$fn, $config]) {
			$vals = $fn($defs);

			foreach ($this->getRecords($vals, $config) as $record) {
				$records[] = $record;
			}
		}

		return $records;
	}

	/**
	 * @return array{Configuration, callable(): list<Record>}
	 */
	public static function create(): array
	{
		$self = new self();

		return [$self, $self->run(...)];
	}

	/**
	 * @param array<string, mixed> $values
	 * @return array<string, Definition>
	 */
	private function filterDefinitions(array $values): array
	{
		$ret = [];

		foreach ($values as $name => $value) {
			if (str_starts_with($name, '_')) {
				continue;
			}

			if ($value instanceof Definition) {
				$ret[$name] = $value;
			}
		}

		return $ret;
	}

	/**
	 * @param array<string, mixed> $values
	 * @return iterable<int, Record>
	 */
	private function getRecords(array $values, GenerationConfig $config): iterable
	{
		$definitions = new DefinitionList($this->filterDefinitions($values));

		foreach ($definitions->list as $name => $def) {
			foreach ($def->getRecords($name, $definitions) as $record) {
				if ($config->prefix !== null) {
					$record = new Record($record->key->withPrefix($config->prefix), $record->value);
				}

				if ($config->category && !$record->category) {
					$record = $record->withCategory($config->category);
				}

				if ($config->tags) {
					$record = $record->withTags(array_merge($config->tags, $record->tags));
				}

				yield $record;
			}
		}
	}

}
