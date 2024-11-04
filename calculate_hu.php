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

    // Define the name comparison function
    function compareByName($a, $b)
    {
        return strcmp($a['name'], $b['name']);
    }

    // Start session
    session_start();

    // Set content
    $content = [];

    if (isset($_POST) && isset($_POST["countResults"]))
    {
        $count = count($_POST["name"]);
        for ($i = 0; $i < $count; $i++)
        {
            $array = [
                "code" => $_POST["code"][$i],
                "name" => $_POST["name"][$i],
                "credit" => intval($_POST["credit"][$i]),
                "grade" => intval($_POST["grade"][$i])
            ];
            $content[] = $array;
        }
        usort($content, 'compareByName');
    }
    else
    {
        $content = $_SESSION[$_GET["id"]];
        usort($content, 'compareByName');
    }
    
    // Handle POST requests
    if ($_POST)
    {
        if (isset($_POST["createFile"]))    // Create file
        {
            $id = $_GET["id"];
            $file = fopen("downloads/" . $id . ".json", "w");
            fclose($file);

            include_once("storage.php");
            $file = new Storage(new JsonIO("downloads/" . $id . ".json"));
            foreach ($content as $c)
            {
                $file->add([
                    "code" => $c["code"],
                    "name" => $c["name"],
                    "credit" => $c["credit"],
                    "grade" => $c["grade"]
                ]);
            }
            $file = "";
        }
        else if (isset($_POST["countResults"]))   // Calculate results
        {
            $newContent = [];
            
            $credMulGrade = 0;
            $creditCount = 0;
            $creditAccomplished = 0;
            $gradeSum = 0;
            $count = count($_POST["name"]);

            for ($i = 0; $i < $count; $i++)
            {
                // Calculate
                $c = intval($_POST["credit"][$i]);
                $g = intval($_POST["grade"][$i]);
                $creditCount += $c;
                $gradeSum += $g;

                if (intval($_POST["grade"][$i]) > 1)
                {
                    $creditAccomplished += $c;
                    $credMulGrade += ($c * $g);
                }

                // Save
                $array = [
                    "code" => $_POST["code"][$i],
                    "name" => $_POST["name"][$i],
                    "credit" => intval($_POST["credit"][$i]),
                    "grade" => intval($_POST["grade"][$i])
                ];
                $newContent[] = $array;
            }

            $_SESSION[$_GET["id"]] = $newContent;
        }
        else if (isset($_POST["addNewSubject"]))    // Add new subject
        {
            $array = [
                "code" => $_POST["code"],
                "name" => $_POST["name"],
                "credit" => intval($_POST["credit"]),
                "grade" => intval($_POST["grade"])
            ];
            $content[] = $array;
            usort($content, 'compareByName');
            $_SESSION[$_GET["id"]] = $content;

            // Redirect to clear POST
            header("Location: calculate_hu.php?id=" . $_GET["id"]);
        }
        else if (isset($_POST["conf"]) && isset($_POST["index"]))    // Delete subject
        {
            if ($_POST["conf"] == "true" && $_POST["index"] !== null)
            {
                array_splice($content, $_POST["index"], 1);
                $_SESSION[$_GET["id"]] = $content;

                // Redirect to clear POST
                header("Location: calculate_hu.php?id=" . $_GET["id"]);
            }
        }
        else
        {
            $index = null;
            foreach ($_POST as $key => $value)
            {
                if (strpos($key, 'del-') === 0)
                {
                    $index = intval(str_replace('del-', '', $key));
                    break;
                }
            }

            if ($index !== null)    // Confirm deletion
            {
                echo "<script>
                document.addEventListener('DOMContentLoaded', function()
                {
                    var conf = confirm('Biztos törölni akarod ezt a tantárgyat?');

                    if (conf !== null)
                    {
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';

                        // Create input for confirmation
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'conf';
                        input.value = conf;
                        form.appendChild(input);

                        // Create input for index
                        var indexInput = document.createElement('input');
                        indexInput.type = 'hidden';
                        indexInput.name = 'index';
                        indexInput.value = " . json_encode($index) . ";
                        form.appendChild(indexInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
                </script>";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <header>
        <div>
            <h1><a href="<?php echo "back.php?id=" . $_GET["id"]; ?>">Kreditindex számoló</a></h1>

            <div id="lang">
                <a href="<?php echo "calculate_hu.php?id=" . $_GET["id"]; ?>">
                    <div>HU</div>
                </a>
                <a href="<?php echo "calculate_eng.php?id=" . $_GET["id"]; ?>">
                    <div>ENG</div>
                </a>
            </div>

            <div id="icon">
                <a href="javascript:void(0);" onclick="showSettings()">
                    <i class="fa fa-bars"></i>
                </a>
            </div>
        </div>
        <div id="settings">
            Nyelvváltás:
            <a href="<?php echo "calculate_hu.php?id=" . $_GET["id"]; ?>">
                <div>HU</div>
            </a>
            <a href="<?php echo "calculate_eng.php?id=" . $_GET["id"]; ?>">
                <div>ENG</div>
            </a>
        </div>
    </header>

    <div class="content">
        <?php if (isset($_POST["countResults"])) : ?>
            <div class="results">
                <h2>Az eredményeid</h2>
                <p>Kreditindex: <?php echo round($credMulGrade / 30, 2); ?></p>
                <p>Korrigált kreditindex: <?php echo round(($credMulGrade / 30) * ($creditAccomplished / $creditCount), 2); ?></p>
                <p>Kredit teljesítve: <?php echo $creditAccomplished; ?></p>
                <p>Hagyományos átlag: <?php echo round($gradeSum / $count, 2); ?></p>
                <p>Súlyozott átlag: <?php echo round($credMulGrade / $creditCount, 2); ?></p>
            </div>
        <?php endif; ?>

        <h2>Tárgyak</h2>
        <p>Egészítsd ki az adatokat a jegyeiddel! Szükség esetén az adatokat itt tudod módosítani. A tárgy mellett található "-" gombbal tudod törölni a tárgyat. Új tárgyat lent tudsz hozzáadni a "+" gombbal.</p>
        <form method="post" class="credit-form">
            <table>
                <tr>
                    <th>Tárgykód</th>
                    <th>Tárgynév</th>
                    <th>Kredit</th>
                    <th>Jegy</th>
                    <th></th>
                </tr>
                <?php $count = 0; ?>
                <?php foreach ($content as $c) : ?>
                <tr>
                    <td><input name="code[]" type="text" size="20px" value="<?php echo $c["code"]; ?>" require></td>
                    <td><input name="name[]" type="text" size="50px" value="<?php echo $c["name"]; ?>" require></td>
                    <td><input name="credit[]" type="number" value="<?php echo $c["credit"]; ?>" min="0" max="10" require></td>
                    <td><input name="grade[]" type="number" value="<?php echo $c["grade"]; ?>" min="1" max="5" require></td>
                    <td><input name="del-<?php echo $count; ?>" type="submit" value="-" id="table-btn"></td>
                </tr>
                <?php $count += 1; ?>
                <?php endforeach; ?>
            </table>

            <input type="submit" id="btn" name="countResults" value="Számolj!">            
        </form>

        <form method="post">
            <br>
            Szeretnéd fájlba menteni az adatokat?
            <input type="submit" id="btn" name="createFile" value="Fájl elkészítése">
        </form>

        <h2>Új tárgy hozzáadása</h2>
        <form method="post" class="credit-form">
            <table>
                <tr>
                    <td><input name="code" type="text" placeholder="Tárgykód" size="20px" required></td>
                    <td><input name="name" type="text" placeholder="Tárgynév" size="50px" required></td>
                    <td><input name="credit" type="number" placeholder="Kredit" required min="0" max="10"></td>
                    <td><input name="grade" type="number" placeholder="Jegy" required min="1" max="5"></td>
                    <td><input type="submit" id="table-btn" name="addNewSubject" value="+"></td>
                </tr>
            </table>
        </form>

        <?php if (file_exists("downloads/" . $_GET["id"] . ".json")) : ?>
            <div id="dload">
                <br><hr>
                <p>A fájl elkészült! Töltsd le az alábbi gomb segítségével!</p>
                <a href="<?php echo "downloads/" . $_GET["id"] . ".json" ?>" download="<?php echo "downloads/" . $_GET["id"] . ".json" ?>" id="btn">Letöltés 💾</a>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        Készítette: SzKK
        <br>
        <a href="https://github.com/SzKataK"><img src="styles_animations/github_logo.png" alt="github_logo"></a>
    </footer>

    <script src="styles_animations/animation.js"></script>
</body>
</html>