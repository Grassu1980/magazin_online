@echo off
echo ================================
echo Initializare proiect Laravel pentru GitHub
echo ================================

REM 1. Creeaza fisier .gitignore pentru Laravel
echo Creare .gitignore...
(
echo /vendor
echo /node_modules
echo /public/storage
echo /storage/*.key
echo /storage/logs/*
echo /storage/framework/*
echo .env
echo .env.*
echo /bootstrap/cache/*.php
echo .idea
echo .vscode
) > .gitignore

REM 2. Initializare Git
echo Initializare Git...
git init

REM 3. Adaugare fisiere
echo Adaug fisierele in Git...
git add .

REM 4. Primul commit
echo Creez primul commit...
git commit -m "Initial Laravel project"

REM 5. Adaug remote GitHub
echo Adaug remote GitHub...
git remote add origin https://github.com/Grassu1980/magazin_online.git

REM 6. Setez branch principal
git branch -M main

REM 7. Push catre GitHub
echo Trimit fisierele catre GitHub...
git push -u origin main

echo ================================
echo GATA! Proiectul este acum pe GitHub.
echo ================================
pause
