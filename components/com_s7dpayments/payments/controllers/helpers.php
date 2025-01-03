<?php


defined('_JEXEC') or die;

class helpers extends s7dPayments {

	public static function existir($valor,$outroValor) {
			return isset($valor) && $valor ? $valor : $outroValor;
		}
}