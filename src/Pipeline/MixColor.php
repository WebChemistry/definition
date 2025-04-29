<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Pipeline;

use WebChemistry\Definition\Pipeline\Input\ContrastCondition;
use WebChemistry\Definition\Pipeline\Input\MixColorInput;
use WebChemistry\Definition\UI\Color;

/**
 * @extends ColorPipeline<MixColorInput>
 */
final class MixColor extends ColorPipeline
{

	private ?Color $blend = null;

	protected function setBlend(Color $blend): self
	{
		$this->blend = $blend;

		return $this;
	}

	protected function createInput(Color $color): object
	{
		return new MixColorInput($this->blend ?? $color->getBlackOrWhite());
	}

	public function setDynamicContrast(ContrastCondition $contrastCondition, float $step, float $minWeight = 5, float $maxWeight = 95): self
	{
		return $this->pipeline(function (Color $color, MixColorInput $input) use ($contrastCondition, $step, $minWeight, $maxWeight): Color {
			$contrastColor = $input->contrastColor ?? $color;
			$weight = min(max($input->weight, $minWeight), $maxWeight);

			$firstValue = $color->mix($input->blend, $weight)->getContrast($contrastColor);
			$secondValue = $color->mix($input->blend, $weight + $step)->getContrast($contrastColor);

			if (!$contrastCondition->isCorrectWay($firstValue, $secondValue)) {
				$step = $step * -1;
				$boundary = $minWeight;
			} else {
				$boundary = $maxWeight;
			}

			$input->weight = $this->calculateWeight($color, $input, $contrastCondition, $step, $boundary);

			return $color;
		});
	}

	public function setWeightByContrast(
		ContrastCondition $contrastCondition,
		float $step = -1,
		float $boundary = 5,
	): self
	{
		return $this->pipeline(function (Color $color, MixColorInput $input) use ($contrastCondition, $step, $boundary): Color {
			$input->weight = $this->calculateWeight($color, $input, $contrastCondition, $step, $boundary);

			return $color;
		});
	}

	private function calculateWeight(
		Color $color,
		MixColorInput $input,
		ContrastCondition $contrastCondition,
		float $step,
		float $boundary,
	): float
	{
		$weight = $input->weight;
		$contrastColor = $input->contrastColor ?? $color;

		do {
			$new = $color->mix($input->blend, $weight);

			$contrast = $new->getContrast($contrastColor);

			$weight += $step;

			if ($step < 0 && $weight < $boundary) {
				$weight = $boundary;

				break;
			}

			if ($step > 0 && $weight > $boundary) {
				$weight = $boundary;

				break;
			}
		} while (!$contrastCondition->isValid($contrast));

		return $weight;
	}

	protected function finalize(Color $color, object $input): Color
	{
		return $color->mix($input->blend, $input->weight);
	}

}
