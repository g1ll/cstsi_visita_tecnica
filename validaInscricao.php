#!/bin/php
<?php
$alunosInscritos = fopen('csv/InscricoesMelhorEnvio_20221130_1723.csv', 'r');
$alunosAtivos = fopen('csv/ListaAlunos_CSTSI.csv', 'r');
$csvConfirmadosFileName = "csv/confirmados_cosie_" . date('Ymd_His') . ".csv";
$alunosValidos = [];
$alunosInvalidos = [];
$limiteAlunos = 35;
$separadorCsv = ',';

function formatInscritos($inscrito)
{
	$data = date_create_from_format("m/d/Y H:i:s", $inscrito[0]);
	$timestamp = $data ? date_timestamp_get($data) : 9999999999;
	return [
		"data_hora" => $timestamp,
		"nome" 		=> $inscrito[2],
		"rg" 		=> $inscrito[3],
		"cpf" 		=> $inscrito[4],
		"e_menor" 	=> strtolower($inscrito[5]),
		"atestado" 	=> substr($inscrito[6], 0, 1),
		"semestre" 	=> substr(strval($inscrito[1]), 0, 5)
	];
}

if ($alunosInscritos && $alunosAtivos) {
	fgets($alunosInscritos);
	while (($inscrito = fgetcsv(
		$alunosInscritos,
		10000,
		$separadorCsv
	))) {
		$notFounded = true;
		fgets($alunosAtivos);
		$matricula = strval($inscrito[1]);
		while ($alunosAtivos && ($ativo = fgetcsv(
			$alunosAtivos,
			10000,
			$separadorCsv
		))) {
			if ($inscrito[1] == $ativo[2]) {
				$notFounded = false;
				$alunosValidos[$matricula] = formatInscritos($inscrito);
			}
		}
		if ($notFounded) {
			$alunosInvalidos[$matricula] = formatInscritos($inscrito);
		}
		rewind($alunosAtivos);
	}
}

if (count($alunosInvalidos) > 0) {
	foreach ($alunosInvalidos as $matricula => $aluno) {
		rewind($alunosAtivos);
		while ($alunosAtivos && ($ativo = fgetcsv(
			$alunosAtivos,
			9999,
			$separadorCsv
		))) {
			if (strtolower($aluno["nome"]) == strtolower($ativo[1])) {
				$alunosValidos[$matricula] = $aluno;
				unset($alunosInvalidos[$matricula]);
			}
		}
	}
}

fclose($alunosAtivos);
fclose($alunosInscritos);

uksort($alunosValidos, function ($k1, $k2) use ($alunosValidos) {
	$sk1 = substr($k1, 0, 5);
	$sk2 = substr($k2, 0, 5);
	if ($sk1 > $sk2) return 1;
	if ($sk1 < $sk2) return -1;
	if ($alunosValidos[$k1]["data_hora"] > $alunosValidos[$k2]["data_hora"]) return 1;
	if ($alunosValidos[$k1]["data_hora"] < $alunosValidos[$k2]["data_hora"]) return -1;
	return 0;
});
$alunosConfirmados = array_slice($alunosValidos, 0, $limiteAlunos, true);

$csvFileName = "csv/tmp.csv";
$csvFileTemp = fopen($csvFileName, 'w+');
$csvFileConfirmados = fopen($csvConfirmadosFileName, 'w');
if ($csvFileTemp && $csvFileConfirmados) {
	fputcsv($csvFileTemp, [
		// '"semestre"',
		// '"matricula"',
		// '"data-hora"',
		'"nome"',
		'"rg"',
		'"cpf"',
		'"menor"',
		'"atestado"'
	], $separadorCsv);
	foreach ($alunosConfirmados as $matricula => $alunos) {
		// array_unshift($alunos, $matricula);
		// array_unshift($alunos, array_pop($alunos));
		array_shift($alunos);
		array_pop($alunos);
		$alunos = array_map(fn ($value) => "\"$value\"", $alunos);
		fputcsv(
			$csvFileTemp,
			$alunos,
			$separadorCsv
		);
	}

	rewind($csvFileTemp);
	while (($line = fgets($csvFileTemp, 10000)))
		fwrite($csvFileConfirmados, str_replace('"""', '"', $line), strlen($line));

	fclose($csvFileTemp);
	fclose($csvFileConfirmados);
	unlink($csvFileName);

	echo "Válidos:\n";
	print_r($alunosValidos);

	echo "Confirmados:\n";
	print_r($alunosConfirmados);

	echo "Invalidos:\n";
	print_r($alunosInvalidos);

	echo "Qtd. alunos inválidos: " . count($alunosInvalidos) . "\n";
	echo "Qtd. alunos válidos: " . count($alunosValidos) . "\n";
	echo "Qtd. alunos confirmados: " . count($alunosConfirmados) . "\n";
} else {
	echo "Erro ao escrever CSV de confirmacao...";
}
