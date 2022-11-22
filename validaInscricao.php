#!/bin/php
<?php
$alunosInscritos = fopen('csv/Inscricoes.csv', 'r');
$alunosAtivos = fopen('csv/ListaAlunos_CSTSI.csv', 'r');
$alunosValidos = [];
$limiteAlunos = 7;

if ($alunosInscritos) {
	while (($inscrito = fgetcsv($alunosInscritos, 9999, ','))) {
		while ($alunosAtivos && ($ativo = fgetcsv($alunosAtivos, 9999, ','))) {
			if ($inscrito[1] == $ativo[2])
				$alunosValidos["#$inscrito[1]"] = $ativo[1];
		}
		rewind($alunosAtivos);
	}
	fclose($alunosAtivos);
	fclose($alunosInscritos);
}

array_shift($alunosValidos);
echo "Válidos:\n";
print_r($alunosValidos);

uksort($alunosValidos,fn($k1,$k2)=>$k1>$k2?1:($k1< $k2?-1:0));
$alunosConfirmados = array_slice($alunosValidos,0,$limiteAlunos,true);

echo "Confirmados:\n";
print_r($alunosConfirmados);