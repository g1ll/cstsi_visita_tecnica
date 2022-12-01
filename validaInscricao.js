#!/bin/node
const fs = require("fs/promises");
const csvAlunosAtivos = "csv/ListaAlunos_CSTSI.csv"
const csvInscritos = 'csv/InscricoesMelhorEnvio_20221130_1723.csv'
let inscritos = []
let ativos = []
let validos = []
let limitAlunos = 35

const csvToArray = (data,sep=',') => {
	let array = []
	let lines = data.split("\n")
	lines.forEach((line, index) => {
		if (index > 0)
			array.push(line.split(sep))
	})
	return array
}

fs.readFile(csvAlunosAtivos, 'utf8')
	.then(data => {
		ativos = csvToArray(data);
		fs.readFile(csvInscritos, 'utf8')
			.then(data => {
				inscritos = csvToArray(data,",")
				inscritos.forEach((inscrito) => {
					ativos.forEach(aluno => {
						if (aluno[2] == inscrito[1])
							validos.push({
								'matricula': aluno[2],
								'nome': aluno[1]
							})
					})
				})
				console.log('Válidos:')
				console.table(validos)
				validos.sort((a, b) => a.matricula > b.matricula ? 1 : (a.matricula < b.matricula) ? -1 : 0)
				confirmados = validos.slice(0, limitAlunos)
				console.log('Confirmados:')
				console.table(confirmados)
			})
			.catch(e => console.error(e.message))
	})
	.catch(e => console.error(e.message))