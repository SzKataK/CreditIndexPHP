<?php
    /*
    MIT License

    Copyright (c) 2024-present KatieSz

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in all
    copies or substantial portions of the Software.

    Any file or portion of the Software not authored by the copyright holder
    is governed by its own respective license. If no license is explicitly included
    in a file or portion of the Software, it is not covered under this MIT License 
    and must adhere to its original licensing terms.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
    SOFTWARE.
    */

    // Delete files older than 1 hour
    foreach (scandir("downloads") as $file)
    {
        if (pathinfo($file, PATHINFO_EXTENSION) == "json" && strlen($file) == 18)
        {
            if (time() - filemtime("downloads/" . $file) > 3600)
            {
                unlink("downloads/" . $file);
            }
        }
    }

    // format
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
                    header("Location: calculate_hu.php?id=". $id);
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
                            header("Location: calculate_hu.php?id=". $id);
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
    <link rel="stylesheet" href="styles_animations/style.css">
</head>
<body>
    <header>
        <h1><a href="index.php">Kreditindex számoló</a></h1>

        <div id="lang">
            <a href="index.php">
                <div>HU</div>
            </a>
            <a href="index_eng.php">
                <div>ENG</div>
            </a>
        </div>
    </header>

    <div class="content">
        <h2>Kreditindex számoló egyetemi hallgatóknak</h2>
        <p>A kreditindex egy tanulmányi mutató, amelyet a magyar egyetemeken használnak. Intézményenként eltérhet, hogy mi alapján adják az ösztöndíjakat, de sok helyen ezt használják. Az oldal arra szolgál, hogy könnyedén kiszámíthasd a kreditindexedet.</p>
        <button class="collapsible">Egyetemek listája</button>
        <div class="collContent">
            <ul>
                <li><a href="https://www.elte.hu/dstore/document/898/ELTE_SZMSZ_II_170530.pdf">Eötvös Loránd Tudományegyetem (ELTE) – HKR (2017)</a></li>
                <li><a href="https://u-szeged.hu/szabalyzatok/tanulmanyi-220819">Szegedi Tudományegyetem (SZTE) – TVSZ (2022)</a></li>
                <li><a href="https://www.kth.bme.hu/document/3040/original/BME_TVSz_2016_elfogadott_mod_20240625_T_S.pdf">Budapesti Műszaki és Gazdaságtudományi Egyetem (BME) – TVSZ (2016)</a></li>
                <li><a href="https://www.uni-corvinus.hu/contents/uploads/2023/07/HKR_3_TVSZ_2020_december_1.1b0.pdf">Budapesti Corvinus Egyetem (CORVINUS) – TVSZ (2020)</a></li>
                <li><a href="https://btk.ppke.hu/uploads/articles/3763/file/Tanulm%C3%A1nyi%20%C3%A9s%20Vizsgaszab%C3%A1lyzat%20egys%C3%A9ges%20szerkezetben%20a%20BTK%20kieg%C3%A9sz%C3%ADt%C5%91%20rendelkez%C3%A9seivel.pdf">Pázmány Péter Katolikus Egyetem (PPKE) – TVSZ (2023)</a></li>
                <li><a href="https://portal.kre.hu/index.php/home/szabalyzatok.html?download=27:iii-hallgatoi-kovetelmenyrendszer-tvsz-2024-06-12">Károli Gáspár Református Egyetem (KRE) – TVSZ (2023)</a></li>
                <li><a href="https://webapi.uni-bge.hu/api/v1/files/download/documents/hallgatoi-dokumentumok/hallgatoi-kovetelmenyrendszer/a-budapesti-gazdasagi-egyetem-hallgatoi-kovetelmenyrendszere?id=28072&download=true">Budapesti Gazdasági Egyetem (BGE) – HKR (2023)</a></li>
            </ul>
        </div>

        <h2>Válassz beviteli módot!</h2>
        <form method="get">
            <input type="radio" id="text" name="format" value="text">
            <label for="text">Szöveg a Neptunból</label><br>
            <input type="radio" id="jsonFile" name="format" value="jsonFile">
            <label for="jsonFile">JSON fájl feltöltése</label><br>
            <input type="submit" id="btn" value="Kiválaszt">
        </form>
        <br>

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
            <p>Ha már használtad az oldalt, akkor töltsd fel az oldalról letöltött JSON fájlt!</p>
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

    <footer>
        Készítette: SzKK
        <br>
        <a href="https://github.com/SzKataK"><img src="styles_animations/github_logo.png" alt="github_logo"></a>
    </footer>

    <script src="styles_animations/animation.js"></script>
</body>
</html>