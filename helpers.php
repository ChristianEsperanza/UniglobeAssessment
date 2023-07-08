<?php
include 'security.php';
include 'constants.php';

/**
 * This file is where the logic and/or functionality is to be handled. 
 * 
 * TODO:
 *      1. Error handling throughout (Checking for nulls);
 */


/**
 * Function to read the text file located at the given file path.
 */
function openTextFile($filePath)
{
    $file = fopen($filePath, "r") or die("Unable to open file");
    return $file;
}


/**
 * Read first line of the text file to get the column names. 
 * ASSUMPTIONS:
 *    - The first line of every text file used will be the column names. 
 * 
 */
function getColumnNamesFromTextFile($textFile) 
{
    $firstLine = fgets($textFile);
    $firstLine = rtrim($firstLine);
    return preg_split("/\t+/", $firstLine);
}

/**
 * Function to read the text file and dynamically turn the data into an associative array.
 *  
 * The decision to turn into an associative array was made for readability and flexibility. In a production environment where the text file 
 * is data that is from an SQL table, it could also be represented as a model. 
 * 
 * ASSUMPTIONS:
 *    - Each data point is separated by a tab character. 
 */
function getUsersFromTextFile($textFile, $columnNames) 
{
    $users = [];

    // Transform text file line by line, separated at tabs, into associative array
    while (!feof($textFile)) {
        $line = fgets($textFile);
        $line = rtrim($line);
        $lineArray = preg_split("/\t+/", $line);
        $user = [];

        foreach ($columnNames as $index => $columnName) {
            $user[$columnName] = $lineArray[$index];
        }
        
        array_push($users, $user);
    }
    return $users;
}

function formatUsers($users) 
{
    $result = [];
    foreach ($users as $user) {
        array_push($result, formatUser($user));
    }
    return $result;
}

/**
 * Format the column names for the table headers by deleting City/State/Zip, which will be part of the address.
 */
function formatColumnNames($columnNames) 
{
    $namesToDelete = ['City/State/Zip'];
    return array_diff($columnNames, $namesToDelete);
}

/**
 * Format data a user to be displayed in a table
 * Takes in an array that represents a user and returns a correctly formatted user.
 * ASSUMPTIONS:
 *    - Given there are usually more gender options for users, I decided to split gender formatting into a separate 
 *        function so it can easily be adjusted as needed. 
 * 
 */
function formatUser($user) 
{
    // 1. Show First name in Title Case (JOHN -> John)
    $user['FirstName'] = ucfirst(strtolower($user['FirstName']));

    // 2. Display Address on separate lines, accounting for cities with multiple words.
    $addressParts = explode(", ", $user['City/State/Zip']);
    $city = $addressParts[0];
    $stateZip = $addressParts[1];
    $stateZipParts = explode(" ", $stateZip);
    $user['Address'] = $user['Address'] . "<br>"
        . $city . ", " . $stateZipParts[0] . "<br>"
        . $stateZipParts[1];
    unset($user['City/State/Zip']);


    // 3. Obfuscate email addresses by starring first portion (john@domain.com -> ****@domain.com)
    $emailParts = explode('@', $user['EmailAddress']);
    $username = $emailParts[0];
    $domain = $emailParts[1];
    $user['EmailAddress'] = str_repeat("*", strlen($username)). '@' . $domain;

    // 4. Show gender as M or F
    $user['Gender'] = formatGender($user['Gender']);

    // 5. Show DateCreated in this format 2018-03-29 (Year-Month-Day)
    $user['DateCreated'] = date('Y m d', strtotime($user['DateCreated']));
    $user['DateCreated'] = str_replace(" ", "-", $user['DateCreated']);
    

    // 6. Highlight their name with a link to http://domain.com/index.php?user=UNIQUEID (using the UniqueID from the data)
    $user['UniqueID'] = "<a href=" . BASE_USER_DOMAIN_URL . $user['UniqueID'] . ">" . $user['UniqueID'] ."</a>";

    // 7. Don't show their SSN number
    $user['SSN'] = encryptSSN($user['SSN']);
    
    return $user;
}


/**
 * Helper function to format gender
 */
function formatGender($gender) {
    switch ($gender) {
        case "Male": 
            return "M";
        case "Female":
            return "F";
        default:
            break;
    }
}

?>

