<?php declare(strict_types = 1);

namespace WebChemistry\Definition\Case;

use Symfony\Component\String\ByteString;

/**
 * @internal
 */
final class KeyCase
{

	public static function kebabCase(string $key): string
	{
		$pos = strpos($key, '@'); // modifier
		$modifier = null;

		if ($pos !== false) {
			$key = substr($key, 0, $pos);
			$modifier = substr($key, $pos + 1);
		}

		$key = (new ByteString($key))->kebab()->toString();

		if ($modifier !== null) {
			$key .= '__' . $modifier;
		}

		return $key;
	}

}
