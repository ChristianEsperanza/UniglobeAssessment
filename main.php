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
    if (!$textFile) {
        // TODO: Error handle here
    }
    
    $columnNames = getColumnNamesFromTextFile($textFile);
    $users = getUsersFromTextFile($textFile, $columnNames);
    // $users = formatUsers($users);

    ?>


    <table>
        <tr>
            <th>
                <?php echo implode('</th><th>', $columnNames);?>
            </th>
        </tr>
        <?php 
        foreach($users as $user) {
            echo "<tr class='tableRow'>";
            echo "<td>" . implode("</td><td>", $user);
            echo "</tr>";
        }
        
        ?>
    </table>
</body>
</html>
