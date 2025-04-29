<?php declare(strict_types = 1);

namespace WebChemistry\Definition;

use WebChemistry\Definition\Exception\InvalidFileException;

final class Startup
{

	/**
	 * @throws InvalidFileException
	 */
	public function run(string $file): void
	{
		[$configuration, $startConfiguration] = Configuration::create();
		[$builder, $startBuilder] = Builder::create();

		$callable = require $file;

		if (!is_callable($callable)) {
			throw new InvalidFileException("Input file '$file' must return callable.");
		}

		$callable($configuration, $builder);

		$records = $startConfiguration();
		$startBuilder($records);
	}

	/**
	 * @param non-empty-list<string> $arguments
	 */
	public static function fromConsole(array $arguments): void
	{
		$script = $arguments[0];
		$file = $arguments[1] ?? null;

		if (!is_string($file) || !$file) {
			echo "Usage: $script <input>\n";

			exit(1);
		}

		if (!is_file($file)) {
			echo "Input file $file not found.\n";

			exit(1);
		}

		if (!str_ends_with($file, '.php')) {
			echo "Input file '$file' must be PHP file.\n";

			exit(1);
		}

		try {
			(new Startup())->run($file);
		} catch (InvalidFileException $e) {
			echo $e->getMessage();

			exit(1);
		}
	}

}
