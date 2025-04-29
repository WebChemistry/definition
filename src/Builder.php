<?php declare(strict_types = 1);

namespace WebChemistry\Definition;

use Nette\Utils\FileSystem;
use WebChemistry\Definition\Definition\Definition;
use WebChemistry\Definition\Environment\Theme;
use WebChemistry\Definition\Export\Exporter;

final class Builder
{

	/** @var Exporter[] */
	private array $exports = [];

	private ?string $prefix = null;

	private function __construct()
	{
	}

	public function withPrefix(string $prefix): void
	{
		$this->prefix = $prefix;
	}

	/**
	 * @template T of Exporter
	 * @param T $exporter
	 * @return T
	 */
	public function exportAs(Exporter $exporter): Exporter
	{
		if ($prefix = $this->prefix) {
			$exporter->withKeyPrefix($prefix);
		}

		$this->exports[] = $exporter;

		return $exporter;
	}

	/**
	 * @param list<Record> $records
	 */
	private function run(array $records): void
	{
		foreach ($this->exports as $exporter) {
			$str = $exporter->export($records);

			FileSystem::write($exporter->getFile(), $str);
		}
	}

	/**
	 * @return array{self, callable(list<Record>): void}
	 */
	public static function create(): array
	{
		$self = new self();

		return [$self, $self->run(...)];
	}

}
