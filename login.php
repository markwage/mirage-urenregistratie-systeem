<?php
session_start();

include ("./config.php");
include ("./db.php");
include ("./function.php");
include ("autoload.php");

include ("header.php");

?>
<div id="main">
	<h1>Urenregistratie</h1>
			
<?php
// Indien het Login form is submitted
if (isset($_POST['submit'])) {
    // controleer of username en wachtwoord zijn ingevuld
    if (! $_POST['username'] || ! $_POST['pass']) {
        echo '<blockquote class="error">ERROR: Niet alle verplichte velden zijn ingevuld</blockquote>';
    }

    // Controleer het met de database
    if (! get_magic_quotes_gpc()) {
        $_POST['username'] = addslashes($_POST['username']);
    }

    $sql_code = "SELECT * FROM users
                 WHERE username = '" . $_POST['username'] . "'";

    $sql_out = mysqli_query($dbconn, $sql_code);
    // Error indien username wel ingevuld maar onbekend
    $sql_num_rows = mysqli_num_rows($sql_out);

    if ($sql_num_rows == 0 && $_POST['username'] != '') {
        writelog("login", "WARN", "Er werd geprobeerd om in te loggen met een niet bestaande username: " . $_POST['username']);
        echo '<blockquote class="error">ERROR: Username is onbekend</blockquote>';
    }

    while ($sql_rows = mysqli_fetch_array($sql_out)) {
        $_POST['pass'] = stripslashes($_POST['pass']);
        $sql_rows['password'] = stripslashes($sql_rows['password']);
        $_POST['pass'] = md5($_POST['pass']);
        $_POST['admin'] = $sql_rows['admin'];
        $_POST['approvenallowed'] = $sql_rows['approvenallowed'];
        $_POST['voornaam'] = $sql_rows['voornaam'];
        $_POST['tussenvoegsel'] = $sql_rows['tussenvoegsel'];
        $_POST['achternaam'] = $sql_rows['achternaam'];
        $_POST['emailadres'] = $sql_rows['emailadres'];
        $_POST['indienst'] = $sql_rows['indienst'];
        $_POST['lastloggedin'] = $sql_rows['lastloggedin'];
        $_POST['uren_invullen'] = $sql_rows['uren_invullen'];
        $wrong_pass_count = $sql_rows['wrong_password_count'];

        // Error indien user niet meer in dienst is
        if (! $_POST['indienst']) {
            writelog("login", "WARN", "User " . $_POST['username'] . " probeerde in te loggen terwijl deze niet meer in dienst is");
            echo '<blockquote class="error">ERROR: User is niet meer in dienst</blockquote>';
        } // Error indien password fout
        elseif ((($_POST['pass'] != $sql_rows['password'])) || ($wrong_pass_count >= 3)) {
            $wrong_pass_count ++;
            $sql_code_wrong_pass = "UPDATE users SET wrong_password_count = " . $wrong_pass_count . "
				                    WHERE username = '" . $_POST['username'] . "'";
            $sql_out_wrong_pass = mysqli_query($dbconn, $sql_code_wrong_pass);
            if (! $sql_out_wrong_pass) {
                writelog("login", "ERROR", "De query voor updaten user tijden login is fout gegaan - " . mysqli_error($dbconn));
                exit($MSGDB001E);
            }
            if ($wrong_pass_count >= 3) {
                writelog("login", "ERROR", "User " . $_POST['username'] . " heeft meer dan 3 keer met een foutief wachtwoord ingelogd en is nu geblokkeerd");

                if ($_SERVER['SERVER_NAME'] != 'localhost') {
                    $mail_to = $_POST['emailadres'];
                    $mail_subject = 'User is geblokkeerd voor Mirage Urenregistratie Systeem';
                    $mail_from = 'mark.wage@hotmail.com';

                    // Aanmaken email headers
                    $headers = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    $headers .= 'From: ' . $mail_from . "\r\n" . 'CC: mark.wage@mirage.nl, mark.wage@outlook.com' . "\r\n" . 'Reply-To: ' . $mail_from . "\r\n" . 'X-Mailer: PHP/' . phpversion();

                    // Creeeren van de email message
                    mail_message_header();
                    $message .= 'Hoi ' . $_POST['voornaam'] . ',';
                    if($wrong_pass_count == 3) {
                        $message .= '<p>Jouw userid <strong>' . $_POST['username'] . '</strong> is <strong>geblokkeerd</strong> in Mirage Urenregistratie Systeem<br />';
                        $message .= 'Reden hiervoor is dat er met dit userid drie keer of meer is geprobeerd in te loggen met een foutief wachtwoord.</p>';
                    } else {
                        $message .= '<p>Er is wederom geprobeerd om met jouw userid <strong>' . $_POST['username'] . '</strong> in te loggen in Mirage Urenregistratie Systeem met een foutief wachtwoord<br />';
                        $message .= 'Dit was poging nummer '. $wrong_pass_count;
                    }
                    $message .= '<p>Om je userid te laten resetten dien je met kantoor te bellen. Je zal dan een nieuw wachtwoord krijgen waarna je weer met dit nieuwe wachtwoord kunt inloggen. Je kunt daarna je wachtwoord zelf wijzigen.</p>';
                    mail_message_footer($message);

                    // Versturen van de email
                    if (mail($mail_to, $mail_subject, $message, $headers)) {
                        // echo '<blockquote>De mail is succesvol verstuurd.</blockquote>';
                        writelog("add_user", "INFO", "Mail succesvol verstuurd naar " . $mail_to . " ivm geblokkeerd userid " . $_POST['username']);
                    } else {
                        echo '<blockquote class="errmsg">Het was niet mogelijk om de mail te versturen. Probeer het nogmaals.</blockquote>';
                        writelog("add_user", "ERROR", "Het is niet gelukt om een mail te versturen naar " . $mail_to . " ivm geblokkeerd userid " . $_POST['username']);
                    }
                }

                echo '<blockquote class="error">ERROR: Er is drie keer of meer geprobeerd met deze user in te loggen met een foutief wachtwoord.<br />Bel naar kantoor om het userid te laten resetten.<br /><br /></blockquote>';
                exit();
            } else {
                writelog("login", "WARN", "User " . $_POST['username'] . " probeerde in te loggen met een foutief wachtwoord (count: " . $wrong_pass_count . ")");
                echo '<blockquote class="error">ERROR: Het opgegeven wachtwoord is niet correct.</blockquote>';
            }
        } else {
            // Toevoegen cookie indien username-password correct
            $_POST['username'] = stripslashes($_POST['username']);

            if (($_SERVER['HTTP_HOST'] == 'localhost') || ($_POST['admin']) || ($_SERVER['HTTP_HOST'] == 'onedrivehost')) {
                $hour = time() + 86400; // Cookie is 24 uur geldig
            } else {
                $hour = time() + 1800; // cookie is 30 minuten geldig
            }
            // $hour = time() + 1800; // cookie is 30 minuten geldig
            // $hour = time() + 86400; // Cookie is 24 uur geldig
            setcookie('ID_mus', $_POST['username'], $hour);
            setcookie('Key_mus', $_POST['pass'], $hour);

            $_SESSION['username'] = $_POST['username'];
            $_SESSION['admin'] = $_POST['admin'];
            $_SESSION['approvenallowed'] = $_POST['approvenallowed'];
            $_SESSION['voornaam'] = $_POST['voornaam'];
            $_SESSION['tussenvoegsel'] = $_POST['tussenvoegsel'];
            $_SESSION['achternaam'] = $_POST['achternaam'];
            $_SESSION['emailadres'] = $_POST['emailadres'];
            $_SESSION['lastloggedin'] = $_POST['lastloggedin'];
            $_SESSION['indienst'] = $_POST['indienst'];
            $_SESSION['uren_invullen'] = $_POST['uren_invullen'];

            $_SESSION['username_encrypted'] = convert_string('encrypt', $_SESSION['username']);
            // update lastloggedin in de tabel
            date_default_timezone_set('Europe/Amsterdam');
            $sql_code = "UPDATE users SET lastloggedin = '" . date('Y-m-d H:i:s') . "',
                         wrong_password_count = 0 
				         WHERE username = '" . $_POST['username'] . "'";
            $sql_out = mysqli_query($dbconn, $sql_code);
            header("location: index.php");

            writelog("login", "INFO", "User " . $_POST['username'] . " is succesvol ingelogd");
        }
    }
}
// indien niet ingelogd het inlogform displayen
?>
	
<h3>Login</h3>
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
<p>
<label>Name</label><input name="username" type="text" maxlength="40" />
<label>Password</label> <input name="pass" type="password"maxlength="32" /><br /><br /> 
<input class="button" name="submit" type="submit" value="login" />
</p>
</form>
<br />
</div>

<!-- content-wrap ends here -->
</div>
</div>


<?php
include ("footer.php");
?>