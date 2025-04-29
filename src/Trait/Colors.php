<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Trait;

use WebChemistry\Definition\Definition\ColorDefinition;
use WebChemistry\Definition\UI\Color;
use WebChemistry\Definition\UI\ColorType;

/**
 * @internal
 */
trait Colors
{

	private function getColor(ColorDefinition|ColorType|string $color, bool $light = true): ColorType
	{
		if ($color instanceof ColorType) {
			return $color;
		}

		if ($color instanceof ColorDefinition) {
			return $light ? $color->light : $color->dark;
		}

		return Color::from($color);
	}

	private function getChangeableColor(ColorDefinition|ColorType|string $color, bool $light = true): Color
	{
		if ($color instanceof Color) {
			return $color;
		}

		if ($color instanceof ColorDefinition) {
			$color = $light ? $color->light : $color->dark;

			if (!$color instanceof Color) {
				throw new \InvalidArgumentException(sprintf('Color "%s" is not changeable.', $color));
			}

			return $color;
		}

		if ($color instanceof ColorType) {
			throw new \InvalidArgumentException(sprintf('Color "%s" is not changeable.', $color));
		}

		return Color::from($color);
	}

}
