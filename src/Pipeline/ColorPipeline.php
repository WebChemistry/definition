<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Pipeline;

use WebChemistry\Definition\UI\Color;

/**
 * @template TInput of object
 */
abstract class ColorPipeline
{

	/** @var list<(callable(Color, TInput): Color)|ColorPipeline<object>> */
	private array $pipelines = [];

	final public function __construct()
	{
	}

	/**
	 * @param TInput $input
	 */
	protected function startup(Color $color, object $input): void
	{
		// nothing
	}

	/**
	 * @param TInput $input
	 */
	protected function finalize(Color $color, object $input): Color
	{
		return $color;
	}

	/**
	 * @return TInput
	 */
	abstract protected function createInput(Color $color): object;

	/**
	 * @return static<TInput>
	 */
	final public static function create(): static
	{
		/** @var static<TInput> */
		return new static();
	}

	/**
	 * @param (callable(Color, TInput): Color)|ColorPipeline<object> $pipeline
	 */
	public function pipeline(ColorPipeline|callable $pipeline): static
	{
		$this->pipelines[] = $pipeline;

		return $this;
	}

	/**
	 * @param TInput|null $input
	 */
	public function execute(Color $color, ?object $input = null): Color
	{
		$input ??= $this->createInput($color);

		$this->startup($color, $input);

		foreach ($this->pipelines as $pipeline) {
			if ($pipeline instanceof ColorPipeline) {
				$color = $pipeline->execute($color);
			} elseif (is_callable($pipeline)) {
				$color = $pipeline($color, $input);
			}
		}

		return $this->finalize($color, $input);
	}

}
