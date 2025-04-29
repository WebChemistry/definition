<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Definition;

use WebChemistry\Definition\UI\ColorType;

final readonly class ColorModifier
{

	public function __construct(
		public ColorType $lightColor,
		public ColorType $darkColor,
	)
	{
	}

}
