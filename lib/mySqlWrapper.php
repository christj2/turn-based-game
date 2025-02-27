<?php

//create a connection to the database
function connect(){
    $sqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_DBNAME);

    // Check connection
    if ($sqli->connect_error) {
        die("Connection failed: " . $sqli->connect_error);
    }
    return $sqli;
}

/*
 * Insert a new record into a mysql table
 *
 * string $tableName - The name of the table to insert to (ex. "users")
 *
 * array $parameters - An associative array of data to insert into the table.
 *                     where keys are the column index and values are the value to insert
 *                     array("username" => "noah", "email" => "coolguy@email.com")
 */
function insert( $game ){
    $conn = connect();
    $stmt = $conn->prepare("INSERT INTO game (board,player1color,player2color,player2money,player2income,player2expenses,player2revenue,player1money,player1income,player1expenses,player1revenue,player1name,player2name,player1session,player2session) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param(
        "sssiiiiiiiissss",$game['board'],$game['player1color'],
        $game['player2color'],$game['player2money'],$game['player2income'],
        $game['player2expenses'],$game['player2revenue'],$game['player1money'],
        $game['player1income'],$game['player1expenses'],$game['player1revenue'],
        $game['player1name'],$game['player2name'],$game['player1session'],$game['player2session']
    );
    $stmt->execute();
    $id =  $stmt->insert_id;
    $stmt->close();
    return $id;
}

/*
 * Update records in a sql table
 *
 * string $tableName - The name of the table to update (ex. "users")
 *
 * array $setParameters - An associative array of data to update in the table
 *                        array("password" => "abc123")   updates the password field to abc123
 *
 * array $whereParameters - An associative array representing which rows in the table to update
 *                          Having multiple elements in the where parameters will combine them with AND
 *                          array("name" => "noah") will update all rows with the name field currently set to noah
 */
 function update($tableName, $setParameters = array(), $whereParameters = array()){
     $mysqli = connect();

     //escape the set fields
     $escapedSetFields = array();
     $setFields = array_keys($setParameters);
     foreach ($setFields as $value) {
         $escapedSetFields[] = mysqli_real_escape_string($mysqli, $value);
     }
     array_push($escapedSetFields, '');     //to get the additional '=?,' from implode
     $setFields = implode('=?,', $escapedSetFields);
     $setFields = substr($setFields, 0, -1);    //removing the trailing comma

     //escape the where fields
     $escapedWhereFields = array();
     $whereFields = array_keys($whereParameters);
     foreach ($whereFields as $value) {
         $escapedWhereFields[] = mysqli_real_escape_string($mysqli, $value);
     }
     array_push($escapedWhereFields, '');       //to get the additional '=?,' from implode
     $whereFields = implode('=? AND ', $escapedWhereFields);
     $whereFields = substr($whereFields, 0, -4);    //removing the trailing AND

     $stmt = $mysqli->prepare("UPDATE $tableName SET $setFields WHERE $whereFields");

     $types = "";
     foreach( $setParameters as &$setVal ){
         $type = '';
         if( gettype($setVal) == 'string' ){
             $type = 's';
         }elseif( gettype($setVal) == 'integer' ){
             $type = 'i';
         }elseif( gettype($setVal) == 'double' ){
             $type = 'd';
         }else{
             $error = "unsupported parameter type: ".gettype($setVal);
             throw new Exception($error);
         }
         $types .= $type;
     }
     foreach( $whereParameters as &$whereVal ){
         $type = '';
         if( gettype($whereVal) == 'string' ){
             $type = 's';
         }elseif( gettype($whereVal) == 'integer' ){
             $type = 'i';
         }elseif( gettype($whereVal) == 'double' ){
             $type = 'd';
         }else{
             $error = "unsupported parameter type";
             throw new Exception($error);
         }
         $types .= $type;
     }

     $tmp = array($types);
     foreach( $setParameters as $key => $value ){
         $tmp[] = &$setParameters[$key];
     }
     foreach( $whereParameters as $key => $value ){
         $tmp[] = &$whereParameters[$key];
     }

     call_user_func_array( array($stmt, 'bind_param'), $tmp );
     $stmt->execute();

     if ($stmt->error != ""){
         throw new Exception($stmt->error);
     }
     $stmt->close();
 }


 /*
 * Delete records in a sql table
 *
 * string $tableName - The name of the table to delete from (ex. "users")
 *
 * array $whereParameters - An associative array representing which rows in the table to delete
 *                          Having multiple elements in the where parameters will combine them with AND
 *                          array("name" => "noah") will delete all rows with the name field currently set to noah
 */
 function delete($tableName, $whereParameters = array()){
     $mysqli = connect();

     //escape the where fields
     $escapedWhereFields = array();
     $whereFields = array_keys($whereParameters);
     foreach ($whereFields as $value) {
         $escapedWhereFields[] = mysqli_real_escape_string($mysqli, $value);
     }
     array_push($escapedWhereFields, '');       //to get the additional '=?,' from implode
     $whereFields = implode('=? AND ', $escapedWhereFields);
     $whereFields = substr($whereFields, 0, -4);    //removing the trailing AND

     $stmt = $mysqli->prepare("DELETE FROM $tableName WHERE $whereFields");

     $types = "";

     foreach( $whereParameters as &$whereVal ){
         $type = '';
         if( gettype($whereVal) == 'string' ){
             $type = 's';
         }elseif( gettype($whereVal) == 'integer' ){
             $type = 'i';
         }elseif( gettype($whereVal) == 'double' ){
             $type = 'd';
         }else{
             $error = "unsupported parameter type";
             throw new Exception($error);
         }
         $types .= $type;
     }

     $tmp = array($types);
     foreach( $whereParameters as $key => $value ){
         $tmp[] = &$whereParameters[$key];
     }

     call_user_func_array( array($stmt, 'bind_param'), $tmp );
     $stmt->execute();

     if ($stmt->error != ""){
         throw new Exception($stmt->error);
     }
     $stmt->close();
 }


/*
 * Select records in a sql table
 *
 * string $tableName - The name of the table to select from (ex. "users")
 *
 * array $columnParameters - An array representing which columns to return results from, * for all
 *                           array("username", "email")
 *
 * array $whereParameters - An associative array representing which rows in the table to select from
 *                          Having multiple elements in the where parameters will combine them with AND
 *                          array("name" => "noah") will select all rows with the name field currently set to noah
 */
 function select($tableName, $columnParameters = array(), $whereParameters = array()){
     $mysqli = connect();

     //escape the set fields
     $escapedFields = array();
     $fields = array_values($columnParameters);
     foreach ($fields as $value) {
         $escapedFields[] = mysqli_real_escape_string($mysqli, $value);
     }
     array_push($escapedFields, '');     //to get the additional '=?,' from implode
     $fields = implode(',', $escapedFields);
     $fields = substr($fields, 0, -1);    //removing the trailing comma

     //escape the where fields
     $escapedWhereFields = array();
     $whereFields = array_keys($whereParameters);
     foreach ($whereFields as $value) {
         $escapedWhereFields[] = mysqli_real_escape_string($mysqli, $value);
     }
     array_push($escapedWhereFields, '');       //to get the additional '=?,' from implode
     $whereFields = implode('=? AND ', $escapedWhereFields);
     $whereFields = substr($whereFields, 0, -4);    //removing the trailing AND

     $stmt = $mysqli->prepare("SELECT $fields FROM $tableName WHERE $whereFields ORDER BY ID DESC");

     $types = "";
     foreach( $whereParameters as &$whereVal ){
         $type = '';
         if( gettype($whereVal) == 'string' ){
             $type = 's';
         }elseif( gettype($whereVal) == 'integer' ){
             $type = 'i';
         }elseif( gettype($whereVal) == 'double' ){
             $type = 'd';
         }else{
             $error = "unsupported parameter type";
             throw new Exception($error);
         }
         $types .= $type;
     }

     $tmp = array($types);
     foreach( $whereParameters as $key => $value ){
         $tmp[] = &$whereParameters[$key];
     }

     call_user_func_array( array($stmt, 'bind_param'), $tmp );
     $stmt->execute();

     $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
     $stmt->close();

     return $results;
 }
