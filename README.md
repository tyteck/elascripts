# Elascripts

## Introduction
This is a set of scripts that allow user to get informations from elasticsearch server.

This work as a cli command (no gui).

## Guessing the index to use
In order to be used more easily, the cli will try to guess what is the index to use. 
By example if there is only one non system index on server it will query this one.

If the guessing fail, it will ask user what is the index to be used and it wil list all non-system indices.

## Features
All these scripts are :
1. guessing the index to be used then .
1. display the number of documents in it.


### Count
Count will only retrieve the number of documents in one index

### Match 
Match is a whole word search utility.
It will :
1. ask user what is he looking for then 
1. display results (column id and title) @todo set the colum to retrieve in a .env file

### Prefix
Prefix is a search utility that will search for any document title beginning with typed word.
It will :
1. ask user what is he looking for then 
1. display results (column id and title) @todo set the colum to retrieve in a .env file


If you already have kibana properly installed this script is not realy useful :)

