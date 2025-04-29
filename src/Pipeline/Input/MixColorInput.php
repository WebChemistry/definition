<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Pipeline\Input;

use WebChemistry\Definition\UI\Color;

final class MixColorInput
{

	public function __construct(
		public Color $blend,
		public float $weight = 50,
		public ?Color $contrastColor = null,
	)
	{
	}

}
