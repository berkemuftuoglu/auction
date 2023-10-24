#!/bin/bash

# Set environment variables for MySQL credentials
export DB_USER=${DB_USER:-"root"}
export DB_PASSWORD=${DB_PASSWORD:-"root"}
export MYSQL_PWD=$DB_PASSWORD  # This will avoid the password warning

# Default MAMP Variables
DIRECTORY="./sql"  # Relative path to the SQL files from the script's location
DB_HOST=${DB_HOST:-"localhost"}  # Default: localhost
DB_NAME=${DB_NAME:-"mysql"}  # Default MAMP DB: mysql
SOCKET_PATH="/Applications/MAMP/tmp/mysql/mysql.sock"  # MAMP MySQL socket path

# Check if user provided an argument for the SQL file
if [ "$#" -ne 1 ]; then
    echo "Please provide the name of the SQL file without the .sql extension (e.g., 'create')."
    exit 1
fi

SQL_FILE="$DIRECTORY/$1.sql"

# Check if the file exists
if [ ! -f "$SQL_FILE" ]; then
    echo "File $SQL_FILE not found!"
    exit 1
fi

# Execute the SQL file
echo "Executing $SQL_FILE..."
mysql --socket=$SOCKET_PATH -h $DB_HOST -u $DB_USER $DB_NAME < $SQL_FILE
if [ $? -eq 0 ]; then
    echo "$SQL_FILE executed successfully!"
else
    echo "Error executing $SQL_FILE!"
fi

# Optionally, unset the MYSQL_PWD variable at the end for security
unset MYSQL_PWD
