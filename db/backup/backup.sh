#!/bin/bash

# Backup Script for
# istoría

user=root
pass=12345678

data=`date +"%m-%d-%Y-%H-%M"`
nome=backup-$data.sql
nome_imagens=imagens-$data
backup=$HOME/Backup/istoria

echo "Salvando backup para $nome"

mysqldump -u$user -p$pass istoria > $backup/$nome

echo "Criando diretório $nome_imagens para imagens"

mkdir "$backup/$nome_imagens"

cp -r images/* $backup/$nome_imagens
