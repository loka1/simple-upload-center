<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        main {
            padding: 2rem;
        }
        form {
            background-color: white;
            border: 1px solid #ced4da;
            padding: 1rem;
            border-radius: 0.25rem;
            max-width: 500px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
        }
        input[type="file"] {
            display: block;
            margin-bottom: 1rem;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 1rem;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <h1>Welcome to Image Upload Page</h1>
    </header>

    <!-- Form Section -->
    <main>
        <?php
        // Database connection
        $servername = "localhost";
        $username = "user_name";
        $password = "password";
        $dbname = "image_upload";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $message = "";
        $messageClass = "";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $message = "File is not an image.";
                $messageClass = "alert-danger";
                $uploadOk = 0;
            }

            // Check if file already exists
            if (file_exists($target_file)) {
                $message = "Sorry, file already exists.";
                $messageClass = "alert-danger";
                $uploadOk = 0;
            }

            // Check file size
            if ($_FILES["fileToUpload"]["size"] > 500000) {
                $message = "Sorry, your file is too large.";
                $messageClass = "alert-danger";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $messageClass = "alert-danger";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $message = "Sorry, your file was not uploaded.";
                $messageClass = "alert-danger";
            // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    $filename = htmlspecialchars(basename($_FILES["fileToUpload"]["name"]));
                    $filepath = $target_file;
                    $hash = md5(uniqid(rand(), true));

                    // Insert image info into database
                    $sql = "INSERT INTO images (filename, filepath, hash) VALUES ('$filename', '$filepath', '$hash')";
                    if ($conn->query($sql) === TRUE) {
                        $last_id = $conn->insert_id;
                        $message = "The file $filename has been uploaded.";
                        $messageClass = "alert-success";
                        echo "<br><a href='view.php?id=$last_id'>View Image</a>";
                        echo " | <a href='delete.php?hash=$hash'>Delete Image</a>";
                    } else {
                        $message = "Error: " . $sql . "<br>" . $conn->error;
                        $messageClass = "alert-danger";
                    }
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                    $messageClass = "alert-danger";
                }
            }
        }

        $conn->close();
        ?>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $messageClass; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="index.php" method="post" enctype="multipart/form-data">
            <label for="fileToUpload">Select image to upload:</label>
            <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*">
            <input type="submit" value="Upload Image" name="submit">
        </form>
    </main>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2023 Image Upload Page</p>
    </footer>
</body>
</html>