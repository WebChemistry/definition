<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Definition;

use WebChemistry\Definition\Definition\Trait\ComputedDefinitions;
use WebChemistry\Definition\Key;
use WebChemistry\Definition\Record;
use WebChemistry\Definition\Trait\Colors;
use WebChemistry\Definition\UI\Color;
use WebChemistry\Definition\UI\ColorType;

final class ColorDefinition implements Definition
{

	use Colors;
	/** @use ComputedDefinitions<ColorDefinition> */
	use ComputedDefinitions;

	/** @var array<string, ColorModifier> */
	private array $modifiers = [];

	public function __construct(
		public ColorType $light,
		public ColorType $dark,
	)
	{
	}

	public function withLightColor(ColorType $color): self
	{
		return new self($color, $this->dark);
	}

	public function withDarkColor(ColorType $color): self
	{
		return new self($this->light, $color);
	}

	public function getLightColor(): Color
	{
		if (!$this->light instanceof Color) {
			throw new \InvalidArgumentException(sprintf('Color "%s" is not changeable.', $this->light));
		}

		return $this->light;
	}

	public function getDarkColor(): Color
	{
		if (!$this->dark instanceof Color) {
			throw new \InvalidArgumentException(sprintf('Color "%s" is not changeable.', $this->dark));
		}

		return $this->dark;
	}

	public function lighten(int $amount): ColorDefinition
	{
		return new self($this->light->lighten($amount), $this->dark->lighten($amount));
	}

	public function darken(int $amount): ColorDefinition
	{
		return new self($this->light->darken($amount), $this->dark->darken($amount));
	}

	public function withHover(ColorDefinition|ColorType|string $lightColor, ColorDefinition|ColorType|string|null $darkColor = null): self
	{
		return $this->withModifier('hover', $lightColor, $darkColor);
	}

	public function withRing(ColorDefinition|ColorType|string $lightColor, ColorDefinition|ColorType|string|null $darkColor = null): self
	{
		return $this->withModifier('ring', $lightColor, $darkColor);
	}

	public function withActive(ColorDefinition|ColorType|string $lightColor, ColorDefinition|ColorType|string|null $darkColor = null): self
	{
		return $this->withModifier('active', $lightColor, $darkColor);
	}

	public function withBorder(ColorDefinition|ColorType|string $lightColor, ColorDefinition|ColorType|string|null $darkColor = null): self
	{
		return $this->withModifier('border', $lightColor, $darkColor);
	}

	public function withModifier(string $name, ColorDefinition|ColorType|string $lightColor, ColorDefinition|ColorType|string|null $darkColor = null): self
	{
		$darkColor ??= $lightColor;

		$this->modifiers[$name] = new ColorModifier($this->getColor($lightColor), $this->getColor($darkColor, false));

		return $this;
	}

	public function __clone(): void
	{
		$this->modifiers = [];
	}

	/**
	 * @return ColorModifier[]
	 */
	public function getModifiers(): array
	{
		return $this->modifiers;
	}

	public function getRecords(string $name, DefinitionList $definitions): iterable
	{
		$def = $this->compute($this, $definitions, $name);

		yield new Record(
			new Key($name, variant: 'light'),
			(string) $def->light,
		);
		yield new Record(
			new Key($name, variant: 'dark'),
			(string) $def->dark,
		);

		foreach ($def->modifiers as $modifierName => $modifier) {
			yield new Record(
				new Key($name, modifier: $modifierName, variant: 'light'),
				(string) $modifier->lightColor,
			);
			yield new Record(
				new Key($name, modifier: $modifierName, variant: 'dark'),
				(string) $modifier->darkColor,
			);
		}
	}

	public function swap(): ColorDefinition
	{
		return new self($this->dark, $this->light);
	}

}
