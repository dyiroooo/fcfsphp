<?php

if (isset($_POST['payload'])) {
    $formData = json_decode($_POST['payload']);
    // $row = $_POST['row']; // Removed this line
    echo json_encode($formData); // Send the processed data back to the client
} else {
    echo "No payload received"; // Handle case where payload is not received
}
