<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Pipeline;

use WebChemistry\Definition\UI\Color;

final class LightenColor extends DarkenLightenColor
{

	protected function compute(Color $color, int $amount): Color
	{
		return $color->lighten($amount);
	}

}
