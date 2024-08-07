<?php
    session_start();
    $content = $_SESSION[$_GET["id"]];
    
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if (isset($_POST["createFile"]))
        {
            $id = $_GET["id"];
            $file = fopen("downloads/" . $id . ".json", "w");
            fclose($file);

            include_once("storage.php");
            $file = new Storage(new JsonIO("downloads/" . $id . ".json"));
            foreach ($_SESSION[$id] as $s)
            {
                $file->add([
                    "code" => $s["code"],
                    "name" => $s["name"],
                    "credit" => $s["credit"],
                    "grade" => $s["grade"]
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
                    $credMulGrade += $c * $g;
                }

                // Save
                $array = [
                    "code" => $_POST["code"][$i],
                    "name" => $_POST["name"][$i],
                    "credit" => $c,
                    "grade" => $g
                ];
                $newContent[] = $array;
            }

            $_SESSION[$_GET["id"]] = $newContent;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit index counter</title>
    <link rel="stylesheet" href="styles_animations/style.css">
</head>
<body>
    <header>
        <h1><a href="<?php echo "back.php?id=" . $_GET["id"]; ?>">Credit index counter</a></h1>

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
                <h2>Your results</h2>
                <p>Credit index: <?php echo round($credMulGrade / 30, 2); ?></p>
                <p>Corrected credit index: <?php echo round(($credMulGrade / 30) * ($creditAccomplished / $creditCount), 2); ?></p>
                <p>Credit accomplished: <?php echo $creditAccomplished; ?></p>
                <p>Classical average: <?php echo round($gradeSum / $count, 2); ?></p>
                <p>Weighted average: <?php echo round($credMulGrade / $creditCount, 2); ?></p>
            </div>
        <?php endif; ?>

        <h2>Subjects</h2>
        <p>Complete the data with your grades! If necessary, you can change the data here.</p>
        <form method="post">
            <table>
                <tr>
                    <th>Subject code</th>
                    <th>Subject name</th>
                    <th>Credit</th>
                    <th>Grade</th>
                </tr>
                <?php foreach ($content as $c) : ?>
                <tr>
                    <td><input name="code[]" type="text" size="20px" value="<?php echo $c["code"]; ?>"></td>
                    <td><input name="name[]" type="text" size="50px" value="<?php echo $c["name"]; ?>"></td>
                    <td><input name="credit[]" type="number" value="<?php echo $c["credit"]; ?>" min="0" max="10"></td>
                    <td><input name="grade[]" type="number" value="<?php echo $c["grade"]; ?>" min="1" max="5"></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" id="btn" name="countResults" value="Calculate">
        </form>

        <?php if (isset($_POST["countResults"])) : ?>
            <form method="post">
                <br><hr>
                Do you want to save the data?
                <input type="submit" id="btn" name="createFile" value="Create file">
            </form>
        <?php endif; ?>

        <?php if (file_exists("downloads/" . $_GET["id"] . ".json")) : ?>
            <div id="dload">
                <br><hr>
                <p>The file is ready! Download it with the button below!</p>
                <a href="<?php echo "downloads/" . $_GET["id"] . ".json" ?>" download="<?php echo "downloads/" . $_GET["id"] . ".json" ?>" id="btn">Download 💾</a>
            </div>
        <?php endif; ?>
    </div>
    
    <footer>
        Made by SzKK
        <br>
        <a href="https://github.com/SzKataK"><img src="styles_animations/github_logo.png" alt="github_logo"></a>
    </footer>
</body>
</html>