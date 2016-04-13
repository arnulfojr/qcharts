<?php

namespace QCharts\CoreBundle\Repository;


class DatabaseQueries
{
    /**
     * SQL Query for showing the name of the databases in the connection
     */
    const SHOW_DATABASES = "SHOW DATABASES;";

    /**
     * SQL query to get the maximum execution duration of the query
     */
    const SHOW_SESSION_EXECUTION = "SHOW SESSION variables LIKE 'max_%%_time';";

    /**
     *
     * Returns the SET string
     *
     * @param string $variableName
     * @param int $value
     * @return string
     */
    static public function getSessionVariableUpdate($variableName, $value)
    {
        return "SET @@session.{$variableName} = {$value}";
    }


    /**
     * @param null|string $schemaName
     * @return string
     */
    static public function getSQLForTableNames($schemaName = null)
    {
        return "SELECT
				TABLE_NAME
				FROM INFORMATION_SCHEMA.TABLES
				WHERE
				TABLE_TYPE = 'BASE TABLE'
				AND TABLE_SCHEMA='{$schemaName}'
				AND TABLE_NAME <> 'fos_user'
				AND TABLE_NAME <> 'migrations_versions'";
    }

    static public function getSQLForAllTableNames()
    {
        return "SELECT
				TABLE_NAME,
				TABLE_SCHEMA
				FROM INFORMATION_SCHEMA.TABLES
				WHERE
				TABLE_TYPE = 'BASE TABLE'
				AND TABLE_NAME <> 'information_schema'
				AND TABLE_NAME <> 'fos_user'
				AND TABLE_NAME <> 'migrations_versions'";
    }

    /**
     * @param $tableName
     * @return string
     */
    static public function getSQLForColumnNames($tableName)
    {
        return "
        SELECT
        COLUMN_NAME,
        IS_NULLABLE,
        DATA_TYPE,
        COLUMN_TYPE,
        COLUMN_KEY
        FROM information_schema.COLUMNS
        WHERE TABLE_NAME = '{$tableName}';";
    }

}