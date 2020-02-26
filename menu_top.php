<?php

echo '<ul>';
echo '<li><a href="index.php">Home</a></li>';

if (!isset($_SESSION['username'])) {
    echo '<li><a href="login.php">Login</a></li>';
} else {

    // Indien ingelogd is
    if (isset($_COOKIE['ID_mus'])) {
        echo '<li><a href="logout.php">Uitloggen</a></li>';
        echo "<li><a href='nieuws.php?aktie=disp'>Nieuws</a></li>";
        if ($_SESSION['admin']) {
            echo "<li><a href='display_systemlog.php' target='_blank'>Systemlog</a></li>";
        }
    }
}
echo '</ul>';

?>
