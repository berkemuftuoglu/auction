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

# Navigate to the directory
cd $DIRECTORY

# Loop through each SQL file in the directory and execute it
for sql_file in *.sql; do
    echo "Executing $sql_file..."
    mysql --socket=$SOCKET_PATH -h $DB_HOST -u $DB_USER $DB_NAME < $sql_file
    if [ $? -eq 0 ]; then
        echo "$sql_file executed successfully!"
    else
        echo "Error executing $sql_file!"
    fi
done

# Optionally, unset the MYSQL_PWD variable at the end for security
unset MYSQL_PWD
