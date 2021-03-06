<?php
    // Start the session and provide the connection information
    require_once('./includes/connectvars.inc.php');
    require_once('./includes/startsession.inc.php');  

    // Clear the error message
    $error_msg = "";

    // If the user isn't logged in, try to log them in
    if (!isset($_SESSION['employeeNumber'])) {
        if (isset($_POST['submit'])) {
            // Connect to the database
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

            // Grab the user-entered log-in data
            $user_username = mysqli_real_escape_string($dbc, trim($_POST['username']));
            $user_password = mysqli_real_escape_string($dbc, trim($_POST['password']));

            if (!empty($user_username) && !empty($user_password)) {
                // Look up the username and password in the database
                $query = "SELECT employeeNumber, username FROM employee WHERE username = '$user_username' AND password = MD5('$user_password')";
                $data = mysqli_query($dbc, $query);

                if (mysqli_num_rows($data) == 1) {
                    // The log-in is OK so set the user ID and username session vars (and cookies), and direct to the welcome page
                    $row = mysqli_fetch_array($data);
                    $_SESSION['employeeNumber'] = $row['employeeNumber'];
                    $_SESSION['username'] = $row['username'];
                    setcookie('employeeNumber', $row['employeeNumber'], time() + (60 * 60 * 24 * 10));    // expires in 10 days
                    setcookie('username', $row['username'], time() + (60 * 60 * 24 * 10));  // expires in 10 days
                    $welcome_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/welcome.php';
                    header('Location: ' . $welcome_url);
                } else {
                    // The username/password are incorrect so set an error message
                    $error_msg = 'Sorry, you must enter a valid username and password to log in.';
                }
            } else {
                // The username/password weren't entered so set an error message
                $error_msg = 'Sorry, you must enter your username and password to log in.';
            }
        }
    }
    // Insert header and show the navigation menu
    $page_title = 'Log In';
    require_once('./includes/htmlhead.inc.php');   
    require_once('./includes/navmenu.inc.php');
?>
       
    <div id="content">
<?php
    // If the session var is empty, show any error message and the log-in form; otherwise confirm the log-in
    if (empty($_SESSION['employeeNumber'])) {
        echo '<p class="error">' . $error_msg . '</p>';
?>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <table>
            <tr>
                <td>User Name:</td>
                <td><input name="username" type="text" value="<?php if (!empty($user_username)) echo $user_username; ?>"></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input name="password" type="password"></td>
            </tr> 
        </table>  
        <input type="submit" value="Log In" name="submit">
  </form>

<?php
    } else {
        // Confirm the successful log-in
       echo('<p class="login">You are logged in as ' . $_SESSION['username'] . '.</p>');
    }
?>
    </div>
 <?php 
 require_once('./includes/footer.inc.php');
?>
