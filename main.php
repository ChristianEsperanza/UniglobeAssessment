<!DOCTYPE html>
<html>
<head>
    <?php
    echo '<link rel="stylesheet" type="text/css" href="styles.css" media="screen" />';
    ?>
</head>

<body>

    <?php
    include 'helpers.php';

    $textFile=openTextFile("./users_list.txt"); 

    $columnNames = getColumnNamesFromTextFile($textFile);
    $rawUsers = getUsersFromTextFile($textFile, $columnNames);
    $users = formatUsers($rawUsers);
    $columnNames = formatColumnNames($columnNames);
    
    ?>


    <table>
        <tr>
            <th>
                <?php echo implode('</th><th>', $columnNames);?>
            </th>
        </tr>
        <?php 
        foreach($users as $user) {
            echo "<tr>";
            echo "<td>" . implode("</td><td>", $user);
            echo "</tr>";
        }
        
        ?>
    </table>
</body>
</html>
