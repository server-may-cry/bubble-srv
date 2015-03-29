<?php
$dbh = new PDO('mysql:host=localhost;dbname=bubble', 'bubble');
$dbh->exec('update user set remaining_tries = 5 where remaining_tries < 5');
