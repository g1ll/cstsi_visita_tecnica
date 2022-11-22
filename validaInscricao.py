#!/bin/python3.8
from csv import DictReader

csvInscritos = 'csv/Inscricoes.csv'
csvAtivos = 'csv/ListaAlunos_CSTSI.csv'

alunosValidos = []
limitAlunos = 7

with open(csvInscritos, newline='') as csvfileInscritos:
    dictInscritos = DictReader(csvfileInscritos, delimiter=',')
    for inscrito in dictInscritos:
        with open(csvAtivos, newline='') as csvfile:
            dictAtivos = DictReader(csvfile, delimiter=',')
            for ativo in dictAtivos:
                if (inscrito['Matrícula'] == ativo['Matrícula']):
                    alunosValidos.append({
                        "Matrícula": ativo.get('Matrícula'),
                        "Nome": ativo.get('Nome')
                    })
if(alunosValidos):
	print('Válidos:')
	for aluno in alunosValidos:
		print(aluno)
	alunosValidos = sorted(alunosValidos, key=lambda d: d['Matrícula'])
	print('\nConfirmados')
	confirmados = alunosValidos[0:limitAlunos]
	for aluno in confirmados:
		print(aluno)