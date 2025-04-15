<?php
include_once("../config/database.php");

if ($conn) {
    echo "✅ Database connection successful!";
} else {
    echo "❌ Connection failed!";
}
?>