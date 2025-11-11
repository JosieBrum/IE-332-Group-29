<?php
if (isset($_POST["submit-btn"])) { // if the submit-btn has any value
   echo "</div>";
   // { } are needed when referencing an array inside a string as below
   echo "<p>Hello {$_POST['username']} your password is {$_POST['password']}!</p>";
   echo "</div>";
} else{
    echo "Please enter your data into <a href='lab3.html'> this form</a>";
}
?>
