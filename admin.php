<!DOCTYPE html>
<html lang="de">
<head>
    <title>Admin-Panel</title>
    <meta charset="UTF-8">
    <!--CSS-Datei initialisieren, die immer neuen Zeitstempel hat, damit sie sich immer neu läd-->
    <link rel="stylesheet" type="text/css" href="style.css?t=<?php echo time() ?>" />
</head>
<body>
<?php
// Einlesen der db.php-Datei für die Variablen um sich mit der Datenbank zu verbinden
include("db.php");
// Erstellen des PDO Objektes für die Datenbankverbindung
$pdo = new PDO("mysql:host=".$db_host.";dbname=".$db_name,$db_user,$db_pass);
?>
<h1>Admin Panel</h1>
<?php
// Funktion um die Ereignisse dem "Admin" zu zeigen, was gerade passiert ist, bspw. eine Frage wurde hinzugefügt oder gelöscht
function sendEvent($message) {
    echo "
    <div class='box shadow'>
    <h3>Ereignis</h3>
    <p>".$message."</p>
    <form method='post'>
        <button name='buttonOk' id='buttonOk' class='button shadow'>Ok!</button>
    </form>
    </div>
    ";
}

// ADD QUESTION -> SQL-Statement um eine Frage hinzuzufügen & Abfrage ob die folgenden Parameter gesetzt sind
if(isset($_POST["question"]) && isset($_POST["correctAnswer"]) && isset($_POST["wrongAnswer0"]) && isset($_POST["wrongAnswer1"]) && isset($_POST["wrongAnswer2"]) && isset($_POST["difficulty"]) && isset($_POST["subject"]) && isset($_POST["contributor"])) {
    $statement = $pdo->prepare("INSERT INTO questions (question, correctAnswer, wrongAnswer0, wrongAnswer1, wrongAnswer2, difficultyId, subjectId, contributor) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    // Ersetzen der Fragezeichen und Ausführung des Befehls (damit wir das jetzt nicht überall hinschreiben müssen, hoffen wir, dass sie das verstehen, wenn wir das bei den weiteren SQL-Statements weglassen und nur hinzuschreiben, was diese machen und nicht nochmals explizit diese Zeile erklären)
    $statement->execute(array($_POST["question"], $_POST["correctAnswer"], $_POST["wrongAnswer0"], $_POST["wrongAnswer1"], $_POST["wrongAnswer2"], $_POST["difficulty"], $_POST["subject"], $_POST["contributor"]));

    sendEvent("Die Frage \"<em>".$_POST["question"]."</em>\" wurde hinzugefügt.");
}

// DELETE QUESTION -> SQL-Statement um eine oder mehrere Fragen zu löschen
if(isset($_POST["selectDelete"])) {
    $longMessage = "";
    foreach ($_POST["selectDelete"] as $selected) {
        // das Element "selected" anhand der "|" aufsplitten (das gleiche Konzept wird auch beim Löschen eines Themas verwendet)
        $select = explode('|', $selected);
        // SQL-Statement um die Frage zu löschen
        $statement = $pdo->prepare("DELETE FROM questions WHERE id = ?");
        $statement->execute(array($select[0]));
        // Nachricht erstellen, die dann im Ereignisfenster angezeigt werden kann
        $longMessage = $longMessage."<p>Die Frage \"<em>".$select[1]."</em>\" wurde gelöscht.</p>";
    }
    sendEvent($longMessage);
}

// EDIT QUESTION -> SQL-Statement um eine Frage zu updaten
if(isset($_POST["questionEdit"]) && isset($_POST["correctAnswerEdit"]) && isset($_POST["wrongAnswer0Edit"]) && isset($_POST["wrongAnswer1Edit"]) && isset($_POST["wrongAnswer2Edit"]) && isset($_POST["difficultyEdit"]) && isset($_POST["subjectEdit"]) && isset($_POST["idEdit"]) && isset($_POST["contributorEdit"])) {
    $statement = $pdo->prepare("UPDATE questions SET question = ?, correctAnswer = ?, wrongAnswer0 = ?, wrongAnswer1 = ?, wrongAnswer2 = ?, difficultyId = ?, subjectId = ? WHERE id = ?");
    $statement->execute(array($_POST["questionEdit"], $_POST["correctAnswerEdit"], $_POST["wrongAnswer0Edit"], $_POST["wrongAnswer1Edit"], $_POST["wrongAnswer2Edit"], $_POST["difficultyEdit"], $_POST["subjectEdit"], $_POST["idEdit"]));

    sendEvent("Die Frage \"<em>".$_POST["questionEdit"]."</em>\" wurde erfolgreich bearbeitet.");
}

// ADD SUBJECT -> SQL-Statement um ein Thema hinzuzufügen
if(isset($_POST["subjectName"]) && isset($_POST["subjectContributor"])) {
    $statement = $pdo->prepare("INSERT INTO subjects (name, contributor) VALUES (?, ?)");
    $statement->execute(array($_POST["subjectName"], $_POST["subjectContributor"]));

    sendEvent("Das Thema \"<em>".$_POST["subjectName"]."</em>\" wurde hinzugefügt.");
}

// RENAME SUBJECT -> SQL-Statement um den Namen eines Themas zu ändern
if(isset($_POST["subjectEditName"]) && isset($_POST["idEditSubject"]) && isset($_POST["contributorEditSubject"])) {
    $statement = $pdo->prepare("UPDATE subjects SET name = ? WHERE id = ?");
    $statement->execute(array($_POST["subjectEditName"], $_POST["idEditSubject"]));

    sendEvent("Das Thema \"<em>".$_POST["subjectEditName"]."</em>\" wurde erfolgreich umbenannt.");
}

// DELETE SUBJECT -> SQL-Statement um ein oder mehrere Themen zu löschen
if(isset($_POST["selectDeleteSubject"])) {
    $longMessage = "";
    foreach ($_POST["selectDeleteSubject"] as $selected) {
        $select = explode('|', $selected);
        $statement = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        $statement->execute(array($select[0]));
        $longMessage = $longMessage."<p>Das Thema \"<em>".$select[1]."</em>\" wurde gelöscht.</p>";
    }
    sendEvent($longMessage);
}

// SQL Abfrage um die Themen zu erhalten
$sql = "SELECT id, name FROM subjects";
// Array für die Themen
$subjects = array();
foreach ($pdo->query($sql) as $row) {
    // hier wird jedes neue Element der SQL-Abfrage dem Array hinzugefügt
    array_push($subjects, $row);
}
// SQL Abfrage um die Schwierigkeitsgrade zu erhalten
$sql = "SELECT id, name, sort FROM difficulties";
// Array für die Schwierigkeitsgrade
$difficulties = array();
foreach ($pdo->query($sql) as $row) {
    // hier wird jedes neue Element der SQL-Abfrage dem Array hinzugefügt
    array_push($difficulties, $row);
}
// Die Schwierigkeiten nach der Sortierung sortieren
usort($difficulties, function ($a, $b) {
    return $a["sort"] <=> $b["sort"];
});
?>
<!-- Ausklappbares Formular, welches alle Fragen anzeigt und nach der jeweiligen Spalte sortiert wird -->
<details open>
    <summary id="summaryOverview">Übersicht</summary>
    <table>
        <tr>
            <th id="question">Frage<form class="sort" method="post"><button title="Absteigend" class="sort" name="sort_desc" value="question">&#x2191;</button><button title="Aufsteigend" class="sort" name="sort_asc" value="question">&#x2193;</button></form></th>
            <th id="correctAnswer">Richtige Antwort<form class="sort" method="post"><button title="Absteigend" class="sort" name="sort_desc" value="correctAnswer">&#x2191;</button><button title="Aufsteigend" class="sort" name="sort_asc" value="correctAnswer">&#x2193;</button></form></th>
            <th id="wrongAnswer0">1. Falsche Antwort<form class="sort" method="post"><button title="Absteigend" class="sort" name="sort_desc" value="wrongAnswer0">&#x2191;</button><button title="Aufsteigend" class="sort" name="sort_asc" value="wrongAnswer0">&#x2193;</button></form></th>
            <th id="wrongAnswer1">2. Falsche Antwort<form class="sort" method="post"><button title="Absteigend" class="sort" name="sort_desc" value="wrongAnswer1">&#x2191;</button><button title="Aufsteigend" class="sort" name="sort_asc" value="wrongAnswer1">&#x2193;</button></form></th>
            <th id="wrongAnswer2">3. Falsche Antwort<form class="sort" method="post"><button title="Absteigend" class="sort" name="sort_desc" value="wrongAnswer2">&#x2191;</button><button title="Aufsteigend" class="sort" name="sort_asc" value="wrongAnswer2">&#x2193;</button></form></th>
            <th id="difficultyId">Schwierigkeit<form class="sort" method="post"><button title="Absteigend" class="sort" name="sort_desc" value="d.sort">&#x2191;</button><button title="Aufsteigend" class="sort" name="sort_asc" value="d.sort">&#x2193;</button></form></th>
            <th id="subjectId">Thema<form class="sort" method="post"><button title="Absteigend" class="sort" name="sort_desc" value="subject">&#x2191;</button><button title="Absteigend" class="sort" name="sort_asc" value="subject">&#x2193;</button></form></th>
            <th id="date">Datum<form class="sort" method="post"><button title="Absteigend" class="sort" name="sort_desc" value="date">&#x2191;</button><button title="Absteigend" class="sort" name="sort_asc" value="date">&#x2193;</button></form></th>
            <th id="contributor">Ersteller<form class="sort" method="post"><button title="Absteigend" class="sort" name="sort_desc" value="contributor">&#x2191;</button><button title="Absteigend" class="sort" name="sort_asc" value="contributor">&#x2193;</button></form></th>
        </tr>
        <?php
        // sortieren, in die richtige Richtung Absteigend oder Aufsteigend, je nachdem, welcher Button in dem tableheader ausgewählt wurde
        if(isset($_POST["sort_desc"])) {
            $sql = "SELECT q.question, q.correctAnswer, q.wrongAnswer0, q.wrongAnswer1, q.wrongAnswer2, d.name as difficulty, s.name as subject, q.date, q.contributor FROM questions q, difficulties d, subjects s WHERE q.difficultyId = d.id AND q.subjectId = s.id ORDER BY ".$_POST["sort_desc"]." DESC";
        } else if(isset($_POST["sort_asc"])) {
            $sql = "SELECT q.question, q.correctAnswer, q.wrongAnswer0, q.wrongAnswer1, q.wrongAnswer2, d.name as difficulty, s.name as subject, q.date, q.contributor FROM questions q, difficulties d, subjects s WHERE q.difficultyId = d.id AND q.subjectId = s.id ORDER BY ".$_POST["sort_asc"]." ASC";
        } else {
            $sql = "SELECT q.question, q.correctAnswer, q.wrongAnswer0, q.wrongAnswer1, q.wrongAnswer2, d.name as difficulty, s.name as subject, q.date, q.contributor FROM questions q, difficulties d, subjects s WHERE q.difficultyId = d.id AND q.subjectId = s.id ORDER BY question ASC";
        }

        foreach ($pdo->query($sql) as $row) {
            $date = new DateTime($row['date']);
            // Ausgabe einer Frage in einer Tabellenzeile
            echo "
            <tr class='data'>
            <td>".$row['question']."</td>
            <td>".$row['correctAnswer']."</td>
            <td>".$row['wrongAnswer0']."</td>
            <td>".$row['wrongAnswer1']."</td>
            <td>".$row['wrongAnswer2']."</td>
            <td>".$row['difficulty']."</td>
            <td>".$row['subject']."</td>
            <td>".$date->format('d.m.y')."</td>
            <td>".$row['contributor']."</td>
            </tr>
            ";
        }
        ?>
    </table>
</details>
<?php
// Falls selectSelectEdit (ID der Frage) gesetzt ist, öffnet sich dieses Detail-Feld schon mal, damit man die Frage gleich bearbeiten kann
if(isset($_POST["selectSelectEdit"])) {
    echo "<details open>";
} else {
    echo "<details>";
}
?>
<!-- Ausklappbares Formular, in dem eine Frage hinzugefügt werden kann -->
    <summary id="questions">Fragen</summary>
    <p>Hier kannst du die Fragen bearbeiten</p>
    <details>
        <summary id="summaryAdd">Hinzufügen</summary>
        <form method="post" id="formAdd">
            <label class="top" for="questionAdd">Frage</label>
                <textarea required class='shadow' name='question' id="questionAdd" placeholder='Was ist die Antwort auf alles?'></textarea>
            <label class="top" for="correctAnswerAdd">Richtige Antwort</label>
                <textarea required class='correct shadow' name='correctAnswer' id="correctAnswerAdd"  placeholder='42'></textarea>
            <label class="top" for="wrongAnswer0Add">1. Falsche Antwort</label>
                <textarea required class='wrong shadow' name='wrongAnswer0' id="wrongAnswer0Add" placeholder='24'></textarea>
            <label class="top" for="wrongAnswer1Add">2. Falsche Antwort</label>
                <textarea required class='wrong shadow' name='wrongAnswer1' id="wrongAnswer1Add" placeholder='1337'></textarea>
            <label class="top" for="wrongAnswer2Add">3. Falsche Antwort</label>
                <textarea required class='wrong shadow' name='wrongAnswer2' id="wrongAnswer2Add" placeholder='420'></textarea>

            <label class="top">Schwierigkeit</label>
            <div class="difficulty" id="difficultyAdd">
            <?php
            // die Schwierigkeitsgrade nacheinander auflisten
            foreach($difficulties as $difficulty) {
                // Schwierigkeitsgrad 0 NaD -> Not a Difficulty überspringen
                if($difficulty["id"] != 0) {
                    echo "
                    <div class='degree'>
                    <label class='side' for='difficulty" . $difficulty["name"] . "Add'>" . $difficulty["name"] . "</label>
                        <input type='radio' name='difficulty' id='difficulty" . $difficulty["name"] . "Add' value='" . $difficulty["id"] . "' required>
                    </div>
                    ";
                }
            }
            ?>
            </div>
            <label class="top" for="contributorAdd">Ersteller</label>
                <textarea required class='shadow' name='contributor' id="contributorAdd" placeholder='Max Mustermann'></textarea>

            <label class="top" for="subjectAdd">Thema</label>
            <select class='shadow' name='subject' id="subjectAdd" required>
                <option disabled selected value>Wähle ein Thema</option>
                <?php
                // Iteriert durch alle Themen und zeigt diese im Select an
                foreach($subjects as $subject) {
                    if($subject["id"] != 0) {
                        echo "<option value='".$subject["id"]."'>".$subject["name"]."</option>";
                    }
                }
                ?>
            </select>
            <button name="buttonAdd" id="buttonAdd" class="button shadow">Hinzufügen</button>
        </form>
    </details>

    <?php
    // Falls selectSelectEdit  (ID der Frage) gesetzt ist, öffnet sich dieses Detail-Feld schon mal, damit man die Frage gleich bearbeiten kann
    if(isset($_POST["selectSelectEdit"])) {
        echo "<details open>";
    } else {
        echo "<details>";
    }
    ?>
    <!-- Ausklappbares Formular, um eine Frage auszuwählen, um diese zu bearbeiten -->
        <summary id="summaryEdit">Editieren</summary>
        <form method="post" id="formSelectEdit">
            <label class="top" for="selectSelectEdit">Frage</label>
            <select class='shadow' name="selectSelectEdit" id="selectSelectEdit" required>
                <option disabled selected value>Wähle eine Frage</option>
            <?php
            // SQL Abfrage um alle Fragen zu bekommen
            $sql = "SELECT id, question FROM questions";
            foreach($pdo->query($sql) as $row) {
                // Listet alle Fragen in dem Select auf
                echo "
                    <option value='".$row['id']."'>".$row['question']."</option>";
            }
            ?>
            </select>
            <button name="buttonSelectEdit" id="buttonSelectEdit" class="button shadow">Editieren</button>
        </form>
        <?php

        // Funktion um die richtige Schwierigkeit zu markieren für die Radio buttons
        function getDifficultyIsChecked($_name, $_difficulty) {
            if($_name == $_difficulty) {
                return "checked";
            }
            return "";
        }

        if(isset($_POST["selectSelectEdit"])) {
            // SQL-Statement für die Abfrage einer Frage
            $statement = $pdo->prepare("SELECT q.id, q.question, q.correctAnswer, q.wrongAnswer0, q.wrongAnswer1, q.wrongAnswer2, d.name as difficulty, s.name as subject, q.contributor FROM questions q, difficulties d, subjects s WHERE q.difficultyId = d.id AND q.subjectId = s.id AND q.id = ?");
            $statement->execute(array($_POST["selectSelectEdit"]));
            while ($row = $statement->fetch()) {
                echo "
                <form method='post' id='formEdit'>
                    <label class='top' for='idEdit'>ID</label>
                        <textarea required class='shadow' name='idEdit' id='idEdit' readonly>".$row["id"]."</textarea>
                    <label class='top' for='questionEdit'>Frage</label>
                        <textarea required class='shadow' name='questionEdit' id='questionEdit'>".$row["question"]."</textarea>
                    <label class='top' for='correctAnswerEdit'>Richtige Antwort</label>
                        <textarea required class='correct shadow' name='correctAnswerEdit' id='correctAnswerEdit'>".$row["correctAnswer"]."</textarea>
                    <label class='top' for='wrongAnswer0Edit'>1. Falsche Antwort</label>
                        <textarea required class='wrong shadow' name='wrongAnswer0Edit' id='wrongAnswer0Edit'>".$row["wrongAnswer0"]."</textarea>
                    <label class='top' for='wrongAnswer1Edit'>2. Falsche Antwort</label>
                        <textarea required class='wrong shadow' name='wrongAnswer1Edit' id='wrongAnswer1Edit'>".$row["wrongAnswer1"]."</textarea>
                    <label class='top' for='wrongAnswer2Edit'>3. Falsche Antwort</label>
                        <textarea required class='wrong shadow' name='wrongAnswer2Edit' id='wrongAnswer2Edit'>".$row["wrongAnswer2"]."</textarea>
                    <label class='top'>Schwierigkeit</label>
                        <div class='difficulty' id='difficultyEdit'>
                    ";
                // Auflistung der ganzen Schwierigkeitsgrade & markiere dank der Funktion getDifficultyIsChecked die markierte Schwierigkeit
                    foreach($difficulties as $difficulty) {
                        if($difficulty["id"] != 0) {
                            echo "
                            <div class='degree'>
                            <label class='side' for='difficulty" . $difficulty["name"] . "Edit'>" . $difficulty["name"] . "</label>
                                <input type='radio' name='difficultyEdit' id='difficulty" . $difficulty["name"] . "Edit' value='" . $difficulty["id"] . "' required " . getDifficultyIsChecked($difficulty["name"], $row["difficulty"]) . ">
                            </div>
                            ";
                        }
                    }
                        echo "
                        </div>
                    <label class='top' for='contributorEdit'>Ersteller</label>
                        <textarea required class='shadow' name='contributorEdit' id='contributorEdit' readonly>".$row["contributor"]."</textarea>
                    <label class='top' for='subjectEdit'>Thema</label>
                        <select class='shadow' name='subjectEdit' id='subjectEdit' required>
                            <option disabled value>Wähle eine Frage</option>
                            ";
                    // Auflistung der Thmenen und wählt direkt das der zur Frage dazugehörende Thema aus / Iteriert über alle Themen
                            foreach($subjects as $subject) {
                                // Überspringt sozusagen das Thema mit der ID=0
                                if($subject["id"] != 0) {
                                    // Falls der Name des Themas, dem Thema der Frage übereinstimmt, wird dieses als ausgewählt markiert
                                    if ($subject["name"] == $row["subject"]) {
                                        echo "<option selected value='" . $subject["id"] . "'>" . $subject["name"] . "</option>";
                                        continue;
                                    }
                                    echo "<option value='" . $subject["id"] . "'>" . $subject["name"] . "</option>";
                                }
                            }
                            echo "
                        </select>
                    <button name='buttonEdit' id='buttonEdit' class='button shadow'>Änderungen bestätigen</button>
                </form>
                ";
            }
        }

        ?>
    </details>

    <details>
        <!-- Ausklappbares Formular, um eine Frage oder mehrere Fragen zu löschen -->
        <summary id="summaryDelete">Löschen</summary>
        <form method="post" id="formDelete">
            <label class="top" for="selectDelete">Frage</label>
            <select name="selectDelete[]" id="selectDelete" multiple required>
                <?php
                // SQL-Abfrage um alle Fragen zu bekommen
                $sql = "SELECT id, question FROM questions";
                foreach($pdo->query($sql) as $row) {
                    // AUflistung aller Fragen in dem Select
                    echo "
                    <option value='".$row['id']."|".$row['question']."'>".$row['question']."</option>";
                }
                ?>
            </select>
            <button name="buttonDelete" id="buttonDelete" class="buttonDelete button shadow">Löschen</button>
        </form>
    </details>
</details>


<?php
// Falls selectSelectEditSubject (ID des Themas) gesetzt ist, öffnet sich dieses Detail-Feld schon mal, damit man die Frage gleich bearbeiten kann
if(isset($_POST["selectSelectEditSubject"])) {
    echo "<details open>";
} else {
    echo "<details>";
}
?>
<!-- Ausklappbares Formular für die Themen -->
    <summary id="summarySubjects">Themen</summary>
    <p>Hier kannst du die Themen bearbeiten</p>

    <!-- Ausklappbares Formular, um ein Thema hinzuzufügen -->
    <details>
        <summary id="summaryAddSubject">Hinzufügen</summary>
        <form method="post" id="formAddSubject">
            <label class="top" for="subjectName">Thema</label>
                <textarea required class='shadow' name='subjectName' id="subjectName" placeholder='PHP'></textarea>
            <label class="top" for="subjectContributor">Ersteller</label>
                <textarea required class='shadow' name='subjectContributor' id="subjectContributor" placeholder='Max Mustermann'></textarea>
            <button name="buttonAddSubject" id="buttonAddSubject" class="button shadow">Hinzufügen</button>
        </form>
    </details>

    <?php
    // Falls selectSelectEditSubject (ID des Themas) gesetzt ist, öffnet sich dieses Detail-Feld schon mal, damit man die Frage gleich bearbeiten kann
    if(isset($_POST["selectSelectEditSubject"])) {
        echo "<details open>";
    } else {
        echo "<details>";
    }
    ?>
    <!-- Ausklappbares Formular, um das zu bearbeitende Thema auszuwählen -->
    <summary id="summaryEditSubject">Editieren</summary>
    <form method="post" id="formSelectEditSubject">
        <label class="top" for="selectSelectEditSubject">Themen</label>
        <select class='shadow' name="selectSelectEditSubject" id="selectSelectEditSubject" required>
            <option disabled selected value>Wähle ein Thema</option>
            <?php
            // SQL Abfrage um alle Themen zu bekommen
            $sql = "SELECT id, name FROM subjects";
            // AUflistung aller Thema
            foreach($pdo->query($sql) as $row) {

                if($row['id'] != 0) {
                    echo "
                        <option value='" . $row['id'] . "'>" . $row['name'] . "</option>
                    ";
                }
            }
            ?>
        </select>
        <button name="buttonSelectEditSubject" id="buttonSelectEditSubject" class="button shadow">Editieren</button>
    </form>
    <!-- Ausklappbares Formular, um den Namen eines Themas zu ändern -->
    <?php

    if(isset($_POST["selectSelectEditSubject"])) {
        // SQL-Statement um das Thema mit der richtigen ID zu erhalten
        $statement = $pdo->prepare("SELECT id, name, contributor FROM subjects WHERE id = ?");
        $statement->execute(array($_POST["selectSelectEditSubject"]));
        while ($row = $statement->fetch()) {
            echo "
                <form method='post' id='formEditSubject'>
                    <label class='top' for='idEditSubject'>ID</label>
                        <textarea required class='shadow' name='idEditSubject' id='idEditSubject' readonly>".$row["id"]."</textarea>
                    <label class='top' for='subjectEditName'>Thema</label>
                        <textarea required class='shadow' name='subjectEditName' id='subjectEditName'>".$row["name"]."</textarea>
                        </div>
                    <label class='top' for='contributorEditSubject'>Ersteller</label>
                        <textarea required class='shadow' name='contributorEditSubject' id='contributorEditSubject' readonly>".$row["contributor"]."</textarea>
                    <button name='buttonEditSubject' id='buttonEditSubject' class='button shadow'>Änderungen bestätigen</button>
                </form>
                ";
        }
    }

    ?>
    </details>

    <!-- Ausklappbares Formular, um ein oder mehrere Themen zu löschen -->
    <details>
        <summary id="summaryDeleteSubject">Löschen</summary>
        <form method="post" id="formDeleteSubject">
            <label class="top" for="selectDeleteSubject">Frage</label>
            <select name="selectDeleteSubject[]" id="selectDeleteSubject" multiple required>
                <?php
                // SQL Abfrage um alle Themen zu bekommen
                $sql = "SELECT id, name FROM subjects";
                // AUflistung aller Thema
                foreach($pdo->query($sql) as $row) {
                    if($row['id'] != 0) {
                        echo "
                        <option value='".$row['id']."|".$row['name']."'>".$row['name']."</option>
                    ";
                    }
                }
                ?>
            </select>
            <button name="buttonDeleteSubject" id="buttonDeleteSubject" class="buttonDelete button shadow">Löschen</button>
        </form>
    </details>
</details>
</body>
</html>