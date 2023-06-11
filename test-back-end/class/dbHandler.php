<?php
/* The DBHandler class sets up the database connection parameters. */

/**
 * In the DBHandler class, they are some unuse methods, they are here for futurs features
 */
class DBHandler
{
    private $name;
    private $user;
    private $password;
    private $host;

    function __construct()
    {
        $this->name = 'testautobonplan';
        $this->user = 'root';
        $this->password = '';
        $this->host = 'localhost';
    }

    public function connect()
    {
        try {
            $link = mysqli_connect($this->host, $this->user, $this->password, $this->name);
        } catch (Exception $e) {
            echo $e;
            die("Couldn't connect to db");
        }
        return $link;
    }

    /**
     * This function inserts data into a specified table in a database using an array of data.
     * 
     * @param array data An associative array containing the data to be inserted into the database
     * table. The keys represent the column names and the values represent the data to be inserted.
     * @param string table The name of the table in the database where the data will be inserted.
     * 
     * return the ID of the newly created object in the database.
     */
    public function insert(array $data, string $table)
    {
        $con = $this->connect();
        if ($con == false) {
            die("ERROR : couldn't connect properly to database : " . mysqli_connect_error());
        }
        $columns = array_keys($data);
        $values = array_values($data);
        $sql = "INSERT INTO `$table` (" . implode(',', $columns) . ") VALUES (\"" . implode("\", \"", $values) . "\" )";
        $stmt = $con->prepare($sql);
        if (($stmt = $con->prepare($sql))) {
            $stmt->execute();
        } else {
            error_reporting(E_ALL);
            echo "there has been an issue with : " . $sql . " " . mysqli_error($con);
        }
        $idCreateObject = $con->insert_id;
        mysqli_close($con);
        return $idCreateObject;
    }
    /**
     * This function retrieves data from a database table based on a specified parameter and condition.
     * 
     * @param string table The name of the table in the database from which data is to be retrieved.
     * @param string param The column name in the database table that is being used as a filter
     * condition.
     * @param string condition The value that the specified parameter should match in the database
     * table. For example, if the parameter is "username" and the condition is "john", the function
     * will retrieve the row(s) where the username is "john".
     * 
     * return an associative array containing the result of the SQL query executed in the function.
     * The array contains the data of the first row that matches the condition specified in the
     * function parameters.
     */
    public function getFromDbByParam(string $table, string $param, string $condition)
    {
        $con = $this->connect();
        if ($con == false) {
            die("ERROR : couldn't connect properly to database : " . mysqli_connect_error());
        }
        $sql = "SELECT * FROM " . $table . " WHERE " . $param . " = '" . $condition . "'";
        if ($request = $con->prepare($sql)) {
            $request->execute();
            $result = $request->get_result();
        } else {
            die("Can't prepare the sql request properly : " . $sql . " " . mysqli_error($con));
        }
        mysqli_close($con);
        return $result->fetch_assoc();
    }

    /**
     * This function retrieves data from a specified table in a database based on a given condition.
     * 
     * @param string toSelect A string representing the columns to select from the table.
     * @param string table The name of the database table to select data from.
     * @param string rowToSearch The name of the row in the table to search for a specific condition.
     * If this parameter is not provided, the function will return all rows from the table.
     * @param string condition The parameter `` is used as a value to search for in the
     * database table. It is only used if the parameter `` is not null. The query will
     * search for rows in the table where the value in the column specified by `` matches
     * the value of
     * 
     * return an array of associative arrays, where each associative array represents a row of data
     * from the selected table in the database. The keys of the associative arrays correspond to the
     * column names in the table, and the values correspond to the values in the respective rows.
     */
    public function getInDB(string $toSelect, string $table, string $rowToSearch = null, string|int $condition = null)
    {
        $db = $this->connect();
        $query = "";
        if (is_null($rowToSearch)) $query = "SELECT $toSelect FROM `$table`";
        else $query = "SELECT $toSelect FROM `$table` WHERE $rowToSearch = ?";
        $sql = $db->prepare($query);
        if (is_null($rowToSearch)) $sql->execute();
        else $sql->execute([$condition]);
        $resultQuery = $sql->get_result();
        $arrayData = [];
        while ($row = mysqli_fetch_assoc($resultQuery)) array_push($arrayData, $row);
        mysqli_close($db);
        return $arrayData;
    }

    public function getBetween(string $toSelect,string $table,string $rowToSearch,string $min,string $max){
        $db = $this->connect();
        $query = "SELECT $toSelect FROM `$table` WHERE $rowToSearch BETWEEN '$min' AND '$max' ";
        $sql = $db->prepare($query);
        $sql->execute();
        $resultQuery = $sql->get_result();
        $arrayData = [];
        while ($row = mysqli_fetch_assoc($resultQuery)) array_push($arrayData, $row);
        mysqli_close($db);
        return $arrayData;
    }

    /**
     * This function updates a row in a database table with a new value based on a specified condition.
     * 
     * @param string table The name of the table in the database that needs to be updated.
     * @param string rowToUpdate The name of the column in the database table that needs to be updated.
     * @param mixed newValue The new value that you want to update the specified row with in the
     * database table. It can be of any data type.
     * @param string tableCondition The column name in the table that is used as a condition for the
     * update statement.
     * @param string condition The value that will be used to identify the row(s) to be updated in the
     * database table based on the table condition.
     */
    public function updateInDB(string $table, string $rowToUpdate, mixed $newValue, string $tableCondition, string $condition)
    {
        $db = $this->connect();
        $sql = $db->prepare("UPDATE `$table` SET `$rowToUpdate` = $newValue WHERE $tableCondition = ?;");
        echo("UPDATE `$table` SET `$rowToUpdate` = $newValue WHERE $tableCondition = $condition");
        $sql->execute([$condition]);
        mysqli_close($db);
    }

    /**
     * This PHP function deletes a row from a specified table based on a given condition.
     * 
     * @param string table The name of the table from which you want to delete a row.
     * @param string rowToSearch The name of the column in the table that will be used to search for
     * the row to be deleted.
     * @param string condition The value to search for in the specified row of the table. This value
     * will be used to determine which row(s) to delete from the table.
     */
    /**
     * This PHP function deletes a row from a specified table based on a given condition.
     * 
     * @param string table The name of the table from which the row needs to be deleted.
     * @param string rowToSearch The name of the column in the table that will be used to search for
     * the row to be deleted.
     * @param string condition The value to search for in the specified row of the table. This is used
     * to identify the specific row to be deleted.
     */
    public function delete(string $table, string $rowToSearch,string $condition){
        $db = $this->connect();
        $stmt = $db->prepare("DELETE FROM $table WHERE $rowToSearch = ?");
        $stmt->execute([$condition]);
        mysqli_close($db) ;
    }

    /**
     * This function retrieves all distinct dates from a database table and returns them in descending
     * order.
     * 
     * @return an array of distinct dates in the format of 'YYYY-MM-DD' from the 'call' table in
     * descending order.
     */
    public function getAllDates(){
        $db = $this->connect();
        $query = "SELECT DISTINCT DATE_FORMAT(date, '%Y-%m-%d') AS 'date' FROM `call` ORDER BY DATE(date) DESC;";

        $sql = $db->prepare($query);
        $sql->execute();
        $resultQuery = $sql->get_result();
        $arrayData = [];
        while ($row = mysqli_fetch_assoc($resultQuery)) array_push($arrayData, $row);
        mysqli_close($db);
        return $arrayData;
    }
}