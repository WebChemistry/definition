<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Pipeline\Input;

final readonly class ContrastCondition
{

	public function __construct(
		public ?float $min = null,
		public ?float $max = null,
	)
	{
		if ($this->min === null && $this->max === null) {
			throw new \InvalidArgumentException('At least one of min or max must be set.');
		}
	}

	public function isValid(float $contrast): bool
	{
		if ($this->min !== null && $contrast < $this->min) {
			return false;
		}

		if ($this->max !== null && $contrast > $this->max) {
			return false;
		}

		return true;
	}

	public function isCorrectWay(float $firstStep, float $secondStep): bool
	{
		$diff = $firstStep - $secondStep;

		if ($this->max !== null && $this->min !== null) {
			if ($firstStep > $this->min && $firstStep < $this->max && $secondStep > $this->min && $secondStep < $this->max) {
				return true;
			}

			if ($diff > 0) {
				return $firstStep > $this->max;
			}

			return $firstStep < $this->min;
		}

		if ($this->min !== null) {
			return $diff < 0;
		}

		return $diff > 0;
	}

}
