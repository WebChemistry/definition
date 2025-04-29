<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Environment;

use WebChemistry\Definition\Definition\ColorDefinition;
use WebChemistry\Definition\Trait\Colors;
use WebChemistry\Definition\UI\LiteralColor;

final readonly class Theme extends Definitions
{

	use Colors;

	public function color(
		ColorDefinition|string $lightColor,
		ColorDefinition|string|null $darkColor = null,
	): ColorDefinition
	{
		$darkColor ??= $lightColor;

		return new ColorDefinition($this->getColor($lightColor), $this->getColor($darkColor, false));
	}

	public function literalColor(
		string $lightColor,
		string|null $darkColor = null,
	): ColorDefinition
	{
		$darkColor ??= $lightColor;

		return new ColorDefinition(new LiteralColor($lightColor), new LiteralColor($darkColor));
	}

}
