<?php
    // Define the name comparison function
    function compareByName($a, $b)
    {
        return strcmp($a['name'], $b['name']);
    }

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
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if (isset($_POST["createFile"]))
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
        else if (isset($_POST["countResults"]))
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
        <h1><a href="<?php echo "back.php?id=" . $_GET["id"]; ?>">Kreditindex számoló</a></h1>

        <div id="lang">
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
        <p>Egészítsd ki az adatokat a jegyeiddel! Szükség esetén az adatokat itt tudod módosítani.</p>
        <form method="post">
            <table>
                <tr>
                    <th>Tárgykód</th>
                    <th>Tárgynév</th>
                    <th>Kredit</th>
                    <th>Jegy</th>
                </tr>
                <?php $count = 0; ?>
                <?php foreach ($content as $c) : ?>
                <tr>
                    <td><input name="code[]" type="text" size="20px" value="<?php echo $c["code"]; ?>"></td>
                    <td><input name="name[]" type="text" size="50px" value="<?php echo $c["name"]; ?>"></td>
                    <td><input name="credit[]" type="number" value="<?php echo $c["credit"]; ?>" min="0" max="10"></td>
                    <td><input name="grade[]" type="number" value="<?php echo $c["grade"]; ?>" min="1" max="5"></td>
                    
                    <td><input name="minus-<?php $count ?>" type="button" value="-"></td>
                    <?php $count += 1; ?>
                </tr>
                <?php endforeach; ?>
            </table>

            <input type="submit" id="btn" name="countResults" value="Számolj!">            
        </form>

        <form action="post">
            <p>Új tárgy hozzáadása</p>
            <table>
                <tr>
                    <td><input name="code" type="text" size="20px" placeholder="Tárgykód"></td>
                    <td><input name="name" type="text" size="50px" placeholder="Tárgynév"></td>
                    <td><input name="credit" type="number" min="0" max="10" placeholder="Kredit"></td>
                    <td><input name="grade" type="number" min="1" max="5" placeholder="Jegy"></td>
                    <td><input type="submit" name="addNewSubject" value="+"></td>
                </tr>
            </table>
        </form>

        <?php if (isset($_POST["countResults"])) : ?>
            <form method="post">
                <br><hr>
                Menteni szeretnéd az adatokat?
                <input type="submit" id="btn" name="createFile" value="Fájl elkészítése">
            </form>
        <?php endif; ?>

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
</body>
</html>