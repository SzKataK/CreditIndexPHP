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
                $errors[] = "A tartalom nem lehet üres!";
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
                            $errors[] = "Hibás kreditérték a(z) " . ($i + 1) . ". sorban!";
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
                        $errors[] = "Hibás formátum a(z) " . ($i + 1) . ". sorban!";
                        break;
                    }
                }

                if (count($errors) == 0)
                {
                    $id = uniqid();
                    session_start();
                    $_SESSION[$id] = $content;
                    header("Location: calculate.php?id=". $id);
                }
            }
        }
        // JSON FILE
        else if ($format == "jsonFile")
        {
            if ($_FILES["fileToUpload"]["error"] == 4)
            {
                $errors[] = "Nem választottál ki fájlt!";
            }
            else
            {
                $targetFile = "uploads/" . basename($_FILES["fileToUpload"]["name"]);
                $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                if (file_exists($targetFile))
                {
                    $errors[] = "Már van egy ilyen nevű fájl feltöltve. Nevezd át a fájlt vagy próbáld újra feltölteni pár perc múlva!";
                }

                if ($fileType != "json")
                {
                    $errors[] = "Csak JSON fájlok tölthetőek fel!";
                }

                if (count($errors) > 0)
                {
                    $errors[] = "A fájl feltöltése sikertelen!";
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
                                $errors[] = "Hibás formátum a(z) " . ($i + 1) . ". sorban!";
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
                            header("Location: calculate.php?id=". $id);
                        }
                        else
                        {
                            $errors[] = "A fájl feltöltése sikertelen!";
                            $content = "";
                            unlink($targetFile);
                        }
                    }
                    else
                    {
                        $errors[] = "Hiba történt a fájl feltöltése közben!";
                    }
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kreditindex számoló</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <header>
        <a href="index.php"><h1>Kreditindex számoló</h1></a>
        <div id="language">
            <div>HU</div>
            <div>ENG</div>
        </div>
    </header>

    <div class="content">
        <h2>Használat</h2>
        <form method="get">
            <p>Válassz beviteli módot!</p>
            <input type="radio" id="text" name="format" value="text">
            <label for="text">Szöveg a Neptunból</label><br>
            <input type="radio" id="jsonFile" name="format" value="jsonFile">
            <label for="jsonFile">JSON fájl feltöltése</label><br>
            <input type="submit" id="btn" value="Kiválaszt">
        </form>
        <?php if ($format == "text") : ?>
        <div class="text-input">
            <hr>
            <h3>Neptunos szöveg alapján</h3>
            <p>Neptun → Tárgyak → Felvett tárgyak</p>
            <p>Másold át a tartalmat ide!</p>
            <form method="post">
                <textarea id="content" name="content" rows="20" cols="80" placeholder="Másold ide a tartalmat a Neptunból!"></textarea>
                <br>
                <input type="submit" value="Feldolgozás" id="btn">
            </form>
        </div>
        <?php endif; ?>
        <?php if ($format == "jsonFile") : ?>
        <div class="json-file-input">
            <hr>
            <h3>JSON fájl feltöltése</h3>
            <p>Töltsd fel az oldalról letöltött JSON fájlt!</p>
            <form method="post" enctype="multipart/form-data">
                Válaszd ki a fájlt:
                <input type="file" name="fileToUpload" id="fileToUpload">
                <br><br>
                <input type="submit" value="Fájl feltöltése" name="uploadFile" id="btn">
            </form>
        </div>
        <?php endif; ?>
        <?php
            if (($format == "text" || $format == "jsonFile") && count($errors) > 0)
            {
                echo "<p>Hiba történt!</p>";
                echo "<ul>";
                foreach ($errors as $e)
                {
                    echo "<li>$e</li>";
                }
                echo "</ul>";
            }
        ?>
    </div>
</body>
</html>