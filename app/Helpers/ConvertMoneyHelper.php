<?php

if (! function_exists('moneyUS')) {
	function moneyUS($money)
	{
		$money = str_replace('R$', '', $money);
        $money = str_replace('.', '', $money);
        $money = str_replace(',', '.', $money);

        $money = trim($money);

        return $money;
	}
}

if (! function_exists('moneyBR')) {
	function moneyBR($money)
	{
		$money = number_format($money, 2, ',', '.');

        return $money;
	}
}