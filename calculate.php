<?php
    session_start();
    $content = $_SESSION[$_GET["id"]];
    
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if (isset($_POST["download"]))
        {
            $file = fopen("downloads/" . $_GET["id"] . ".txt", "w");
            fclose($file);

            include_once("storage.php");
            $file = $content = new Storage(new JsonIO("downloads/" . $_GET["id"] . ".txt"));
            for ($i = 0; $i < count($_POST["subject"]); $i++)
            {
                $file->add([
                    "code" => $_POST["code"][$i],
                    "name" => $_POST["subject"][$i],
                    "credit" => intval($_POST["credit"][$i]),
                    "grade" => intval($_POST["grade"][$i])
                ]);
            }

            //header("Location: downloads/" . $_GET["id"] . ".txt");
        }

        // Calculate
        $credMulGrade = 0;
        $creditCount = 0;
        $creditAccomplished = 0;
        $gradeSum = 0;
        $count = count($_POST["subject"]);

        for ($i = 0; $i < $count; $i++)
        {
            $c = intval($_POST["credit"][$i]);
            $g = intval($_POST["grade"][$i]);
            $creditCount += $c;
            $gradeSum += $g;

            if (intval($_POST["grade"][$i]) > 1)
            {
                $creditAccomplished += $c;
                $credMulGrade += $c * $g;
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
        <?php if ($_POST) : ?>
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
                <?php foreach ($content as $c) : ?>
                <tr>
                    <td><input name="code[]" type="text" size="20px" value="<?php echo $c["code"]; ?>"></td>
                    <td><input name="subject[]" type="text" size="50px" value="<?php echo $c["name"]; ?>"></td>
                    <td><input name="credit[]" type="number" value="<?php echo $c["credit"]; ?>" min="0" max="10"></td>
                    <td><input name="grade[]" type="number" value="<?php echo $c["grade"]; ?>" min="1" max="5"></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" id="btn" value="Számolj!">
        </form>

        <form action="<?php echo 'download.php?id=' . htmlspecialchars($_GET["id"]); ?>">
            <br><hr>
            Menteni szeretnéd az adatokat?
            <input type="submit" id="btn" name="download" value="Itt letöltheted!">
            <a href="<?php echo $id; ?>"></a>
        </form>
    </div>
</body>
</html>