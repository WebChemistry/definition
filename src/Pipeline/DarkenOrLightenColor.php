<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Pipeline;

use WebChemistry\Definition\UI\Color;

final class DarkenOrLightenColor extends DarkenLightenColor
{

	private bool $darken = false;

	protected function startup(Color $color, object $input): void
	{
		$this->darken = $color->isLight();
	}

	protected function compute(Color $color, int $amount): Color
	{
		if ($this->darken) {
			return $color->darken($amount);
		} else {
			return $color->lighten($amount);
		}
	}

}
