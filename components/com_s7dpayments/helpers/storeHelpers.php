<?php 

function existir($valor,$outroValor) {
	return isset($valor) && $valor ? $valor : $outroValor;
}