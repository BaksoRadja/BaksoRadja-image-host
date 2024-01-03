<?php

// Path: upload.php

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get the uploaded file name
    $fileName = $_FILES['file']['name'];

    // Get the uploaded file extension
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Check if the file is an image
    $isImage = getimagesize($_FILES['file']['tmp_name']);

    if ($isImage === false) {
        echo 'File is not an image.';
    } else {
        // Check if the file already exists in the "img" folder
        if (file_exists('img/' . $fileName)) {
            echo 'File already exists.';
        } else {
            // Upload file
            if (!move_uploaded_file($_FILES['file']['tmp_name'], 'img/' . $fileName)) {
                echo 'Error uploading file.';
            } else {
                header('Location: index.php?status=success_upload');
            }
        }
    }
}

?>