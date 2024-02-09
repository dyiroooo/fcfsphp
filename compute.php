<?php

if (isset($_POST['payload'])) {
    $formData = json_decode($_POST['payload']);


    $formDataArray = (array) $formData;

    // Encode the array as JSON
    echo json_encode($formDataArray);
} else {
    echo "No payload received"; // Handle case where payload is not received
}
