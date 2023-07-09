<?php
include 'security.php';
include 'constants.php';

/**
 * This file is where the logic and/or functionality is to be handled. 
 */

/**
 * Function to read the text file located at the given file path. Throws error if file opening
 * encounters error.
 * PARAMS:
 *    $filepath - String pointing to filepath.
 * RETURNS: 
 *    $file - Resource binded to a stream.
 */
function openTextFile($filePath)
{
    if (!file_exists($filePath)) {
        die("File not found");
    } else {
        $file = fopen($filePath, "r") or die("Unable to open file");
        return $file;
    }
}


/**
 * Reads first line of a given text file. The first line of the text file MUST contain the column 
 * names.
 * PARAMS: 
 *   $textFile - A text file resource
 * RETURNS: 
 *   Array of strings.
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
 * PARAMS:
 *     $textFile - Text file resource.
 *     $columnNames - Array of strings.
 * RETURNS:
 *     $users - Array of users. 
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

/**
 * Formats all users in the array that is passed in.
 * PARAMS:
 *      $users - Array of users.
 * RETURNS: 
 *      $result - Formatted array that was passed in.
 */
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
 * If other column names need to be deleted, the column name can be added to the $namesToDelete array.
 * PARAMS:
 *      $columnNames - String array.
 * RETURNS: 
 *      $columnNames - String array.
 */
function formatColumnNames($columnNames) 
{
    $namesToDelete = ['City/State/Zip'];
    return array_diff($columnNames, $namesToDelete);
}

/**
 * Format data a user to be displayed in a table
 * ASSUMPTIONS:
 *    - Given there are usually more gender options for users, I took creative liberty to split gender formatting into a separate 
 *        function so it can easily be adjusted as needed. 
 * PARAMS:
 *      $user - Array containing user data.
 * RETURNS:
 *      $user - Array containing formatted user data.
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
 * Helper function to format gender.
 * PARAMS:
 *      $gender - String.
 * RETURNS:
 *      $gender - String, formatted accordingly.
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

