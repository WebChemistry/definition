<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Pipeline\Input;

use WebChemistry\Definition\UI\Color;

final class DarkenLightenInput
{

	public function __construct(
		public int $amount = 5,
		public ?Color $contrastColor = null,
	)
	{
	}

}
