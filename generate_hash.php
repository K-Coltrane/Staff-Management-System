<?php
$plainPassword = 'admin'; // Replace with the actual plain text password
$hashedPassword = '$2y$10$8WxYR0aA58Ot3ULPwP1Jx.ZHrN3EQA5XLUhQnSR8G9KBtcD2Qjtw2';

if (password_verify($plainPassword, $hashedPassword)) {
    echo "Password is valid!";
} else {
    echo "Password is invalid!";
}

$newPassword = 'admin123'; // Replace with the new plain text password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

echo "New Hashed Password: " . $hashedPassword;
?>