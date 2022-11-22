#!/bin/python3.8
# import pandas as pd
from pandas import read_csv, DataFrame

limiteAlunos = 7
alunosAtivos = read_csv('csv/ListaAlunos_CSTSI.csv')
alunosInscritos = read_csv('csv/Inscricoes.csv')
nomes = []
matriculas = []
aluno = DataFrame({})

for index, row in alunosInscritos.iterrows():
	valido="Invalido"
	aluno = alunosAtivos.loc[alunosAtivos['Matrícula']==str(row['Matrícula'])]
	if(aluno.size>0):
		valido="Valido"
		nomes.append(aluno['Nome'].item())
		matriculas.append(aluno['Matrícula'].item())
	print(f"{row['Matrícula']}->{row['Nome Completo']} :{valido}")

alunosValidos = DataFrame({'Nome':nomes,'Matrícula':matriculas})
alunosConfirmados = alunosValidos.sort_values('Matrícula').head(limiteAlunos)

print(f"Alunos Validos: {alunosValidos.shape[0]} de {alunosInscritos.shape[0]}")
print(f"\nAlunos Confirmados:{alunosConfirmados.shape[0]} de {alunosValidos.shape[0]}\n",alunosConfirmados)