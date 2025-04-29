<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Pipeline;

use WebChemistry\Definition\Pipeline\Input\ContrastCondition;
use WebChemistry\Definition\Pipeline\Input\DarkenLightenInput;
use WebChemistry\Definition\UI\Color;

/**
 * @extends ColorPipeline<DarkenLightenInput>
 */
abstract class DarkenLightenColor extends ColorPipeline
{

	/**
	 * @return DarkenLightenInput
	 */
	protected function createInput(Color $color): object
	{
		return new DarkenLightenInput();
	}

	public function setAmountByContrast(
		ContrastCondition $contrastCondition,
		int $step = 4,
		int $boundary = 40,
	): static
	{
		return $this->pipeline(function (Color $color, DarkenLightenInput $input) use ($contrastCondition, $step, $boundary): Color {
			$amount = $input->amount;

			do {
				$new = $this->compute($color, $amount);

				$contrast = $new->getContrast($input->contrastColor ?? $color);

				$amount += $step;

				if ($amount > $boundary) {
					$amount = $boundary;

					break;
				}
			} while (!$contrastCondition->isValid($contrast));

			$input->amount = $amount;

			return $color;
		});
	}

	abstract protected function compute(Color $color, int $amount): Color;

	protected function finalize(Color $color, object $input): Color
	{
		return $this->compute($color, $input->amount);
	}

}
