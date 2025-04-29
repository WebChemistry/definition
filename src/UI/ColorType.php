<?php declare(strict_types = 1);

namespace WebChemistry\Definition\UI;

interface ColorType
{

	public function lighten(int $amount): self;

	public function darken(int $amount): self;

	public function __toString(): string;

	public function toHex(): string;

}
