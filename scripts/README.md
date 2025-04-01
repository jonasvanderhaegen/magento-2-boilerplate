#Readme for all scripts
These scripts are used for quick conversion of data.
Never run them in production!

## PersoLite CSV to DB values
This creates an output sql file which can be used to import new values for the perso lite module.
Create a file data.csv next to the command.
Set the delimiter and the and productIdentifier in the script.

```
php PersoLiteCsvToDBValues.php > output.sql
```
