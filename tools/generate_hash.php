<?php
echo "Admin hash: <br>";
echo password_hash("123admin", PASSWORD_DEFAULT);

echo "<br><br>User hash: <br>";
echo password_hash("123user", PASSWORD_DEFAULT);
?>
