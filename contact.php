<?php
session_start();
include ("config.php");
include ("db.php");
include ("function.php");
include ("autoload.php");


// Connectie met de database maken en database selecteren
$dbconn = mysqli_connect($dbhost, $dbuser, $dbpassw, $dbname);

// Controleren of cookie aanwezig is. Anders login-scherm displayen
check_cookies();

include ("header.php");

?>
<div id="main">
<h1>Contactformulier</h1>

<?php 

// Get value from the form
if(isset($_POST['submit'])) 
{
	$fName   = $_POST['fName'];
	$lName   = $_POST['lName'];
	$email   = $_POST['email'];
	$message = $_POST['message'];

    // Create a function that makes sure the email is a valide name@domain.com
	function verify_email($email)
	{
    	if(!preg_match('/^[_A-z0-9-]+((\.|\+)[_A-z0-9-]+)*@[A-z0-9-]+(\.[A-z0-9-]+)*(\.[A-z]{2,4})$/',$email))
    	{
        	return false;
    	}
    	else
    	{
       	    return $email;
    	}
	}
	
    // If email is in the correct format create a function that makes sure the domain behind the @ symbol exists
	function verify_email_dns($email)
	{
    	//list($name, $domain) = split('@',$email);
    	$split_email = explode("@", $email);
    	$domain = $split_email[1];
    	if(!checkdnsrr($domain,'MX'))
    	{
        	return false;
    	}
    	else
    	{
        	return $email;
    	}
	}
	
    // If email domain is verified check to make sure all required fields are not empty, if they are redirect to with error message
	if(verify_email($email))
	{
		if(verify_email_dns($email))
		{
			if ($fName == '')
			{
				header('location:contact.php?error=missing');
			}
			elseif ($email == '')
			{
				header('location:contact.php?error=missing');
			}
			elseif ($message == '')
			{
				header('location:contact.php?error=missing');
			}
			else
			{
				// If everything is where it should be then get values and send in an email
				//foreach ($myvars as $var)
				//{
				//	if (isset($_POST[$var]))
				//	{
				//		$$var=$_POST[$var];
				//	}
				//}
				
				// Value are explained below
				$subject = "Email Contact"; // Subject of email sent to you
				$add="mark.wage@hotmail.com"; // Real email address to have the email sent to
				$msg="First Name:          \t$fName\n"; // First name (\t needs to line up with next  line to have a straight email, \n is a <br>
				$msg.="Last Name:           \t$lName\n"; // Last Name
				$msg.="Email:               \t$email\n"; // Email
				$msg.="Message:             \t$message\n"; // Message
				$mailheaders="From: $email\n"; // Email that the visitor put in the email field.  This will be the from email address
				$mailheaders.="Reply-To: $email\n"; // Email that the visitor put in the email field.  This will be the reply email address
				mail("$add", "$subject", $msg, $mailheaders); // This part just adds the headers using the variables from above
				header('location:contact.php?error=none');  // Redirect using using none so they can see the email was a success
			} 
		}
		else
		{
			// If they fail the verify_email_dns function redirect to with error message
			header('location:contact.php?error=mx');
    	}
	}
	else
	{
		// If they fail the verify_email function redirect to with error message
		header('location:contact.php?error=format');
	}
}

if (isset($_GET['eror']))
{
    $error = $_GET['error'];


    switch ($error) {
        case "mx":
            echo "<br><span class='red'>The domain name you entered for your email address does not exsit.  Please try again.</span><br>";
            break;
        case "format":
            echo "<br><span class='red'>Your email address is not in the correct format, it should look like name@domain.com. Please try again.</span><br>";
            break;
        case "missing":
            echo "<br><span class='red'>You seem to be missing a required field, please try again.</span><br>";
            break;
	    case "none":
            echo "<br>Your email was sent.  We will reply within 24 hours.  Thank you for your interest.<br>";
   	    	break;
    	default:
           echo "<br><br>";
    }
}
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="contactForm" name="contactForm">
    <p>
	<table>
	    <tr>
		    <td>Voornaam</td>
		    <td><input type="text" readonly required name="fName" size="10" maxlength="10" value="<?php echo $_SESSION['voornaam']; ?>"></td>
	    </tr>
	    <tr>
		    <td>Achternaam</td>
		    <td><input type="text" readonly required name="lName" size="18" maxlength="18" value="<?php echo $_SESSION['tussenvoegsel'].' '.$_SESSION['achternaam']; ?>"></td>
	    </tr>
	    <tr>
		    <td>Emailadres</td>
		    <td><input type="text" readonly required name="email" size="50" maxlength="50" value="<?php echo $_SESSION['emailadres']; ?>"></td>
	    </tr>
	    <tr>
		    <td>Onderwerp</td>
		    <td><textarea name="message" rows="5" cols="80" id="message"></textarea></td>
	    </tr>
	</table>
	<!-- 
    <label for="fName">Voornaam:<span class="required">*</span></label>
    <input type="text" name="fName" value="<?php //echo $_SESSION['voornaam']; ?>" id="fName">
    <br><br>
    
    <label for="Achternaam">Achternaam:</label>
    <input type="text" name="lName" value="<?php //echo $_SESSION['tussenvoegsel'].' '.$_SESSION['achternaam']; ?>" id="lName">
    <br><br>

    <label for="email">Emailadres:<span class="required">*</span></label>
    <input type="text" name="email" value="<?php //echo $_SESSION['emailadres']; ?>" id="email">
    <br><br>

    <label for="message">Onderwerp:<span class="required">*</span></label>
    <textarea name="message" rows="5" cols="60" id="message"></textarea>
    <br><br>
    -->

    <input class=button type="submit" name="submit" id="submit" value="Submit">
    
    </p>
</form>

</body>
</html>

