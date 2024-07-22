<?php
    $format = isset($_GET["format"]) ? $_GET["format"] : "";
    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // TEXT
        if ($format == "text")
        {
            $content = isset($_POST["content"]) ? $_POST["content"] : "";
            if (empty($content))
            {
                $errors[] = "The content cannot be empty!";
            }
            else
            {
                $content = explode("\n", $content);
                for ($i = 0; $i < count($content); $i++)
                {
                    $content[$i] = rtrim($content[$i]);
                    $content[$i] = explode("\t", $content[$i]);

                    if (count($content[$i]) == 4)
                    {
                        $credit = $content[$i][2];

                        if (!is_numeric($credit) || intval($credit) < 0 || intval($credit) > 10)
                        {
                            $errors[] = "Wrong credit value in row" . ($i + 1) . "!";
                            break;
                        }
                        else
                        {
                            $content[$i] = [
                                "code" => $content[$i][0],
                                "name" => $content[$i][1],
                                "credit" => intval($credit),
                                "grade" => 1
                            ];
                        }
                    }
                    else
                    {
                        $errors[] = "Wrong format in row" . ($i + 1) . "!";
                        break;
                    }
                }

                if (count($errors) == 0)
                {
                    $id = uniqid();
                    session_start();
                    $_SESSION[$id] = $content;
                    header("Location: calculate_eng.php?id=". $id);
                }
            }
        }
        // JSON FILE
        else if ($format == "jsonFile")
        {
            if ($_FILES["fileToUpload"]["error"] == 4)
            {
                $errors[] = "You have not selected a file!";
            }
            else
            {
                $targetFile = "uploads/" . basename($_FILES["fileToUpload"]["name"]);
                $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                if (file_exists($targetFile))
                {
                    $errors[] = "A file with that name is already uploaded. Rename the file or try uploading again in a few minutes!";
                }

                if ($fileType != "json")
                {
                    $errors[] = "Only JSON files can be uploaded!";
                }

                if (count($errors) > 0)
                {
                    $errors[] = "Failed to upload the file!";
                }
                else
                {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile))
                    {
                        include_once('storage.php');
                        $content = new Storage(new JsonIO($targetFile));
                        foreach ($content->findAll() as $c)
                        {
                            if (count($c) != 5 || !isset($c["code"]) || !isset($c["name"]) || !isset($c["credit"]) || !isset($c["grade"]))
                            {
                                $errors[] = "Wrong format in row" . ($i + 1) . "!";
                                break;
                            }
                        }

                        if (count($errors) == 0)
                        {
                            $id = uniqid();
                            session_start();
                            $_SESSION[$id] = $content->findAll();
                            $content = "";
                            unlink($targetFile);
                            header("Location: calculate_eng.php?id=". $id);
                        }
                        else
                        {
                            $errors[] = "Failed to upload the file!";
                            $content = "";
                            unlink($targetFile);
                        }
                    }
                    else
                    {
                        $errors[] = "Failed to upload the file!";
                    }
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit index counter</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <header>
        <a href="index_eng.php"><h1>Credit index counter</h1></a>
        <div id="language">
            <div><a href="index.php">HU</a></div>
            <div>ENG</div>
        </div>
    </header>

    <div class="content">
        <h2>Usage</h2>
        <form method="get">
            <p>Choose an input method!</p>

            <input type="radio" id="text" name="format" value="text">
            <label for="text">Text from Neptun</label><br>

            <input type="radio" id="jsonFile" name="format" value="jsonFile">
            <label for="jsonFile">Upload JSON file</label><br>

            <input type="submit" id="btn" value="Select">
        </form>

        <?php if ($format == "text") : ?>
        <div class="text-input">
            <hr>
            <h3>Based on Neptun text</h3>
            <p>Open your Neptun and navigate to this page:</p>
            <p>Neptun → Subjects → Registered subjects</p>
            <p>Copy the content here!</p>
            <form method="post">
                <textarea id="content" name="content" rows="20" cols="80" placeholder="Copy the content from Neptun here!"></textarea>
                <br>
                <input type="submit" value="Process" id="btn">
            </form>
        </div>
        <?php endif; ?>
        
        <?php if ($format == "jsonFile") : ?>
        <div class="json-file-input">
            <hr>
            <h3>Upload JSON file</h3>
            <p>Upload a JSON file eralier downloaded from this site!</p>
            <form method="post" enctype="multipart/form-data">
                Choose the file:
                <input type="file" name="fileToUpload" id="fileToUpload">
                <br><br>
                <input type="submit" value="Upload File" name="uploadFile" id="btn">
            </form>
        </div>
        <?php endif; ?>
        <?php
            if (($format == "text" || $format == "jsonFile") && count($errors) > 0)
            {
                echo "<p>Error occurred!</p>";
                echo "<ul>";
                foreach ($errors as $e)
                {
                    echo "<li>$e</li>";
                }
                echo "</ul>";
            }
        ?>
    </div>
    
    <footer>
        <p>Credit index counter for ELTE students</p>
    </footer>
</body>
</html>