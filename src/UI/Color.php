<?php declare(strict_types = 1);

namespace WebChemistry\Definition\UI;

use InvalidArgumentException;

final readonly class Color implements ColorType
{

	protected function __construct(
		private int $red,
		private int $green,
		private int $blue,
		private int $alpha = 100,
	)
	{
	}

	public function lighten(int $amount): Color
	{
		return new self(...$this->adjustBrightness($amount / 100));
	}

	public function darken(int $amount): Color
	{
		return new self(...$this->adjustBrightness(-($amount / 100)));
	}

	public function invert(): Color
	{
		return new self(
			255 - $this->red,
			255 - $this->green,
			255 - $this->blue,
			$this->alpha,
		);
	}

	public function getDistance(Color $color): float
	{
		$red = $this->red - $color->red;
		$green = $this->green - $color->green;
		$blue = $this->blue - $color->blue;

		return sqrt($red * $red + $green * $green + $blue * $blue);
	}

	public function isSimilar(Color $color, float $threshold): bool
	{
		return $this->getDistance($color) < $threshold;
	}

	public function mix(Color $color, float $weight = 50): Color
	{
		$weight = max(0, min(100, $weight));

		$red = (int) ($this->red * (1 - $weight / 100) + $color->red * ($weight / 100));
		$green = (int) ($this->green * (1 - $weight / 100) + $color->green * ($weight / 100));
		$blue = (int) ($this->blue * (1 - $weight / 100) + $color->blue * ($weight / 100));
		$alpha = (int) ($this->alpha * (1 - $weight / 100) + $color->alpha * ($weight / 100));

		return new self($red, $green, $blue, $alpha);
	}

	public function getContrast(Color $color): float
	{
		$l1 = $this->getRelativeLuminance();
		$l2 = $color->getRelativeLuminance();

		if ($l1 > $l2) {
			$contrast = ($l1 + 0.05) / ($l2 + 0.05);
		} else {
			$contrast = ($l2 + 0.05) / ($l1 + 0.05);
		}

		return $contrast;
	}

	public function isLight(): bool
	{
		$brightness = ($this->red * 299 + $this->green * 587 + $this->blue * 114) / 1000;

		return $brightness > 128;
	}

	/**
	 * @param float $adjustPercent A number between -1 and 1. E.g. 0.3 = 30% lighter; -0.4 = 40% darker.
	 * @return array{int, int, int, int}
	 */
	private function adjustBrightness(float $adjustPercent): array
	{
		$adjust = function (float $adjustPercent, int $color): int {
			$adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
			$adjustAmount = ceil($adjustableLimit * $adjustPercent);

			return (int) ($color + $adjustAmount);
		};

		return [
			$adjust($adjustPercent, $this->red),
			$adjust($adjustPercent, $this->green),
			$adjust($adjustPercent, $this->blue),
			$this->alpha,
		];
	}

	/**
	 * @return array{int, int, int}
	 */
	public function getHueSaturationLightness(): array
	{
		// Normalize RGB values
		$red = $this->red / 255;
		$green = $this->green / 255;
		$blue = $this->blue / 255;

		// Find the maximum and minimum values of RGB
		$max = max($red, $green, $blue);
		$min = min($red, $green, $blue);

		// Calculate the lightness
		$lightness = ($max + $min) / 2;

		// Check for pure gray color
		if ($max === $min) {
			$hue = $saturation = 0; // Hue and saturation are 0 for gray
		} else {
			// Calculate the saturation
			if ($lightness < 0.5) {
				$saturation = ($max - $min) / ($max + $min);
			} else {
				$saturation = ($max - $min) / (2 - $max - $min);
			}

			// Calculate the hue
			if ($max == $red) {
				$hue = ($green - $blue) / ($max - $min);
			} elseif ($max == $green) {
				$hue = 2 + ($blue - $red) / ($max - $min);
			} else {
				$hue = 4 + ($red - $green) / ($max - $min);
			}

			$hue *= 60; // Convert hue to degrees
			if ($hue < 0) {
				$hue += 360; // Ensure hue is within [0,360] range
			}
		}

		return [(int) round($hue), (int) round($saturation * 100), (int) round($lightness * 100)];
	}

	public function getBlackOrWhite(): Color
	{
		return $this->isLight() ? Color::from('#000') : Color::from('#fff');
	}

	public function __toString(): string
	{
		return $this->toHex();
	}

	public function toHex(): string
	{
		return sprintf('#%02x%02x%02x', $this->red, $this->green, $this->blue);
	}

	public static function fromHex(string $hex): Color
	{
		$hex = ltrim($hex, '#');

		if (strlen($hex) == 3) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		$alpha = 100;

		if (strlen($hex) === 8) { // alpha
			$alpha = (int) hexdec(substr($hex, 6, 2));
			$hex = substr($hex, 0, 6);
		}

		[$red, $green, $blue] = array_map(
			fn (string $hex) => (int) hexdec($hex),
			str_split($hex, 2),
		);

		return new self($red, $green, $blue, $alpha);
	}

	public static function from(string $color): Color
	{
		if (str_starts_with($color, '#')) {
			return self::fromHex($color);
		}

		if (str_starts_with($color, 'rgb')) {
			return self::fromRgb($color);
		}

		if (str_starts_with($color, 'hsl')) {
			return self::fromHsl($color);
		}

		throw new InvalidArgumentException(sprintf('Invalid color format "%s"', $color));
	}

	public static function fromHsl(string $color): Color
	{
		$color = str_replace(['hsl(', ')'], '', $color);

		[$hue, $saturation, $lightness] = array_map(
			function (string $color): int {
				$color = trim($color);

				if (!is_numeric($color)) {
					throw new InvalidArgumentException('Invalid color format');
				}

				return (int) $color;
			},
			explode(',', $color));

		$lightness /= 100;
		$saturation /= 100;

		if ($saturation === 0) {
			$red = $green = $blue = $lightness * 255;
		} else {
			$chroma = (1 - abs(2 * $lightness - 1)) * $saturation;
			$hue = $hue / 60;
			$intermediate = $chroma * (1 - abs(fmod($hue, 2) - 1));
			$red = $green = $blue = 0;

			if ($hue >= 0 && $hue < 1) {
				$red = $chroma;
				$green = $intermediate;
			} elseif ($hue >= 1 && $hue < 2) {
				$red = $intermediate;
				$green = $chroma;
			} elseif ($hue >= 2 && $hue < 3) {
				$green = $chroma;
				$blue = $intermediate;
			} elseif ($hue >= 3 && $hue < 4) {
				$green = $intermediate;
				$blue = $chroma;
			} elseif ($hue >= 4 && $hue < 5) {
				$red = $intermediate;
				$blue = $chroma;
			} elseif ($hue >= 5 && $hue < 6) {
				$red = $chroma;
				$blue = $intermediate;
			}

			$lightness -= $chroma / 2;
			$red = ($red + $lightness) * 255;
			$green = ($green + $lightness) * 255;
			$blue = ($blue + $lightness) * 255;
		}

		return new self($red, $green, $blue);
	}

	public static function fromRgb(string $color): Color
	{
		$color = str_replace(['rgb(', 'rgba(', ')'], '', $color);
		$parts = explode(',', $color);

		[$red, $green, $blue] = array_map(
			function (string $color): int {
				$color = trim($color);

				if (!is_numeric($color)) {
					throw new InvalidArgumentException('Invalid color format');
				}

				return (int) $color;
			},
			$parts,
		);

		if (count($parts) === 4) {
			$raw = (float) trim($parts[3]);
			$alpha = (int) ($raw * 100);
		} else {
			$alpha = 100;
		}

		return new self($red, $green, $blue, $alpha);
	}

	private function getRelativeLuminance(): float
	{
		$calc = static function (int $channel): float {
			$channel /= 255;

			if ($channel <= 0.03928) {
				return $channel / 12.92;
			}

			return pow(($channel + 0.055) / 1.055, 2.4);
		};

		return 0.2126 * $calc($this->red) + 0.7152 * $calc($this->green) + 0.0722 * $calc($this->blue);
	}

	/**
	 * @param callable(float $contrast): bool $contrastCondition
	 */
	public static function findWeightForMixing(
		Color $color,
		Color $blend,
		float $weight,
		callable $contrastCondition,
		float $step = -1,
		float $weightBoundary = 5,
	): float
	{
		do {
			$new = $color->mix($blend, $weight);

			$contrast = $new->getContrast($color);

			$weight += $step;

			if ($step < 0 && $weight < $weightBoundary) {
				$weight = $weightBoundary;

				break;
			}

			if ($step > 0 && $weight > $weightBoundary) {
				$weight = $weightBoundary;

				break;
			}
		} while ($contrastCondition($contrast));

		return $weight;
	}

	public static function contrastCondition(?float $min = null, ?float $max = null): callable
	{
		return static function (float $contrast) use ($min, $max): bool {
			if ($min === null && $max === null) {
				return true;
			}

			if ($min !== null && $contrast < $min) {
				return true;
			}

			if ($max !== null && $contrast > $max) {
				return true;
			}

			return false;
		};
	}

}
