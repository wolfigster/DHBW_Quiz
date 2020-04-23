<!DOCTYPE html>
<html lang="de">
<head>
    <title>Quiz</title>
    <meta charset="UTF-8">
    <!--CSS-Datei initialisieren, die immer neuen Zeitstempel hat, damit sie sich immer neu läd-->
    <link rel="stylesheet" type="text/css" href="style.css?t=<?php echo time() ?>" />

</head>
<body>
<h1>QuizMeHarDaddy</h1>
<?php
// Einlesen der db.php-Datei für die Variablen um sich mit der Datenbank zu verbinden
include("db.php");
// Erstellen des PDO Objektes für die Datenbankverbindung
$pdo = new PDO("mysql:host=".$db_host.";dbname=".$db_name,$db_user,$db_pass);
$inGame = false;
$current = 1;
$finishGame = false;

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

// Quiz erstellen START
// Erst wenn alle Parameter gesetzt sind, die für das Quiz wichtig sind startet das Quiz
if(isset($_POST["quizer"]) && isset($_POST["amount"]) && isset($_POST["difficulty"]) && isset($_POST["subject"])) {
    // Wenn die Anzahl ins negative geht oder Null ist, kommt gar kein Quiz zustande
    if($_POST["amount"] < 0) {
        $finishGame = true;
    }
    if($current == 1) {
        $inGame = true;
        $q_subjects = "";
        $q_amount = 0;

        // SQL-Statement um das Erstellen eines Quiz's vorzubereiten
        $statement = $pdo->prepare("INSERT INTO quiz (amount, quizer) VALUES (?, ?)");
        $statement->execute(array($q_amount, $_POST["quizer"]));
        // Die ID des letzten erstellen Quiz's bekommen, damit man weiß, welches Quiz man gerade spielt
        $quizId = $pdo->lastInsertId();

        // SQL-Statement, welches die Themen in die quiz_subjects Tabelle schreibt, also welches Quiz welche Themen hatte (also ein Quiz kann mehrere Themen haben)
        $statement = $pdo->prepare("INSERT INTO quiz_subjects (quizId, subject) VALUES (?, ?)");
        foreach ($_POST["subject"] as $selected) {
            // das Element "selected" anhand der "|" aufsplitten um dadurch mehrere Themen hineinzuschreiben
            $select = explode('|', $selected);
            // Abfrage für das Erste Thema, da mindestens ein Thema ausgewählt sein muss
            if($_POST["subject"][0] == $select[0]."|".$select[1]) {
                $q_subjects = $q_subjects." q.subjectId = '".$select[0]."'";
            } else {
                // bei jedem weiteren Thema wird ein OR für die Datenbankabfrage davorgehangen
                $q_subjects =  $q_subjects." OR q.subjectId = '".$select[0]."'";
            }
            // Jedes Thema mit der jeweiligen QuizId und der ThemaId in die Tabelle schreiben
            $statement->execute(array($quizId, $select[0]));
        }


        // SQL Abfrage um die maximale Anzahl der Fragen zu bekommen, die mit den momentanen Kriterien möglich sind
        $sql = "SELECT COUNT(*) as amount FROM questions q, difficulties d, subjects s WHERE (d.sort <= ".$_POST["difficulty"].") AND (".$q_subjects.") AND q.difficultyId = d.id AND q.subjectId = s.id ORDER BY RAND() LIMIT ".$_POST["amount"];
        foreach ($pdo->query($sql) as $row) {
            $q_amount = $row["amount"];
        }
        // Anzahl falls benötigt korrigieren, damit man nicht zu viele Fragen hat
        if($q_amount >= $_POST["amount"]) {
            $q_amount = $_POST["amount"];
        }
        // SQL-Statement, welches die Anzahl der Fragen des Quiz's korrigiert
        $statement = $pdo->prepare("UPDATE quiz SET amount = ".$q_amount." WHERE id = ".$quizId);
        $statement->execute(array($q_amount, $_POST["quizer"]));

        // Variable für die Reihenfolge der Fragen
        $order = 1;
        // SQL-Abfrage um die Fragen für das Quiz zu bekommen, mit den passenden Themen, der Schwierigkeitsgrad und der Anzahl
        $sql = "SELECT q.id FROM questions q, difficulties d, subjects s WHERE (d.sort <= ".$_POST["difficulty"].") AND (".$q_subjects.") AND q.difficultyId = d.id AND q.subjectId = s.id ORDER BY RAND() LIMIT ".$q_amount;
        foreach ($pdo->query($sql) as $row) {
            // SQL-Statement für jede Frage wird in der Tabelle quiz_questions die Frage zu der passenden QuizId, + line für die Reihenfolge und false eingetragen, damit die Antwort fürs erste Falsch ist
            $statement = $pdo->prepare("INSERT INTO quiz_questions (quizId, questionId, line, correct) VALUES (?, ?, ?, ?)");
            $statement->execute(array($quizId, $row["id"], $order, false));
            $order++;
        }
    }
}
// Wenn der buttonNext->übergibt alle wichtigen Infos zum Quiz, die Id, die momentane Frage, die Anzahl etc. und answer-> also die Antwort, welche angekreuzt wurde gesetzt sind
if(isset($_POST["buttonNext"]) && isset($_POST["answer"])) {
    $temp = explode("|",$_POST["buttonNext"]);
    $quizId = $temp[0];
    $current = $temp[1];
    $q_amount = $temp[2];
    $question = $temp[3];
    $correct = false;
    $givenanswer = $_POST["answer"];

    // SQL-Abfrage um die korrekte Antwort zu bekommen
    $sql = "SELECT q.correctAnswer FROM questions q, quiz_questions qq WHERE qq.questionId = q.id";
    foreach ($pdo->query($sql) as $row) {
        if($row["correctAnswer"] == $_POST["answer"]) {
            $correct = true;
        }
    }

    // SQL-Statement Quiz updaten mit der angegebenen Antwort, und falls diese korrekt war correct auf true setzen
    $statement= $pdo->prepare("UPDATE quiz_questions SET correct = ?, answer = ? WHERE line = ? AND quizId = ?");
    $statement->execute(array($correct, $givenanswer, $current, $quizId));

    // Falls das Spiel noch läuft und die momentane Frage(Nr) die Anzahl der Fragen erreicht hat, wird das Spiel beendet, falls dies nicht der fall ist, wird die Nr der momentanen Frage um eins erhöht.
    $inGame = true;
    if($current == $q_amount) {
        $finishGame = true;
    } else {
        $current++;
    }
}
// Quiz erstellen ENDE

if(!$inGame) {
    echo "
    <!-- Ausklappbares Formular, um alle Kriterien (Schwierigkeitsgrad, Anzahl der Fragen, Name des Quizers und die Themen) auszuwählen -->
    <details open>
        <summary id='summaryQuiz'>Kriterien</summary>
        <form method='post'>
            <label class='top' for='quizer'>Quizer</label>
                <textarea required class='short shadow' name='quizer' id='quizer' placeholder='Max Mustermann'></textarea>
            <label class='top' for='amountSelect'>Anzahl</label>
                <input required class='shadow' name='amount' id='amountSelect' type='number' placeholder='8'>
            <label class='top'>Schwierigkeit</label>
                <div class='difficulty' id='difficultySelect'>
                    ";
                    // die Schwierigkeitsgrade nacheinander auflisten
                    foreach($difficulties as $difficulty) {
                        // Schwierigkeitsgrad 0 NaD -> Not a Difficulty überspringen
                        if($difficulty["id"] != 0) {
                            echo "
                            <div class='degree'>
                            <label class='side' for='difficulty" . $difficulty["name"] . "'>" . $difficulty["name"] . "</label>
                                <input type='radio' name='difficulty' id='difficulty" . $difficulty["name"] . "' value='" . $difficulty["sort"] . "' required>
                            </div>
                            ";
                        }
                    }
                    echo "
                </div>
            <label class='top' for='selectSubject'>Thema</label>
                <select class='shadow' name='subject[]' id='selectSubject' multiple required>
                    <option disabled selected value>Wähle die Themen raus</option>
                    ";
                    // SQL-Statement um die Anzahl der Fragen mit dem jeweiligen Thema zu bekommen
                    $statement = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE subjectId = ?");
                    foreach ($subjects as $subject) {
                        // Auflistung der Themen mit der Anzahl in Klammern
                        if($subject["id"] != 0) {
                            // Thema 0 NaS -> Not a Subject überspringen
                            $statement->execute(array($subject["id"]));
                            $result = $statement->fetch();
                            // Als value wird ein | hinzugefügt, damit man dieses dann später trennen kann
                            echo "<option value='" . $subject["id"] . "|" . $result[0] . "'>" . $subject["name"] . " (" . $result[0] . ")</option>";
                        }
                    }
                    echo "
                </select>
    
            <button name='buttonStart' id='buttonStart' class='button shadow'>Quiz starten</button>
        </form>
    </details>
    ";
} else if ($finishGame) {
    //Falls das Spiel beendet ist

    // Funktion um herauszufinden, ob die Antwort die korrekte Antwort ist
    function isAnswerCorrect($_answer, $_correct) {
        if($_answer == $_correct) {
            // gibt correct oder wrong zurück dies sind "classes" von css, wrong wird rot hinterlegt und correct grün
            return "correct";
        }
        return "wrong";
    }

    // Funktion um herauszufinden, ob die Antwort, die der Quizer gegeben hat, die korrekte Antwort ist
    function isAnswerAnswered($_answer, $_answered) {
        if($_answer == $_answered) {
            // gibt checked für den Radiobutton zurück
            return "checked";
        }
        return "";
    }

    echo "<h2>Ergebnis zu Quiz Nr. ".$quizId."</h2>";

    // SQL Abfrage um alle Infos einer Frage zu bekommen
    $sql = "SELECT q.question, q.correctAnswer, q.wrongAnswer0, q.wrongAnswer1, q.wrongAnswer2, d.name as difficulty, s.name as subject, qq.answer, qq.line FROM questions q, difficulties d, subjects s, quiz qz, quiz_questions qq WHERE q.id = qq.questionId AND qz.id = qq.quizId AND q.difficultyId = d.id AND q.subjectId = s.id AND qz.Id = ".$quizId." ORDER BY qq.line";
    foreach ($pdo->query($sql) as $row) {
        echo "
        <details>
            <summary id='questionNumber".$row["line"]."'>Frage Nr. ".$row["line"]."</summary>
            <label class='top' for='question".$row["line"]."'>Fragenstellung</label>
                <textarea required class='shadow ".isAnswerCorrect($row["answer"], $row["correctAnswer"])."' name='question".$row["line"]."' id='question".$row["line"]."' readonly>".$row["question"]."</textarea>
                 ";
        // alle Antworten in ein Array packen
        $answers = array($row["correctAnswer"], $row["wrongAnswer0"], $row["wrongAnswer1"], $row["wrongAnswer2"]);
        // das Array mischen
        shuffle($answers);
        // p als Zählvariable für das Array answers
        $p = 0;
        foreach ($answers as $a) {
            // die Antworten mit den den jeweils richtig markierten Antworten ausgeben
            echo "
            <div class='answer'>
                <label class='longside result ".isAnswerCorrect($a, $row["correctAnswer"])."' for='answer".$row["line"].$p."'>" . $a . "</label>
                    <input class='result' type='radio' name='answer".$row["line"]."' id='answer".$row["line"].$p."' value='answer".$row["line"].$p."' disabled required ".isAnswerAnswered($a, $row["answer"]).">
            </div>
            ";
            $p++;
        }
        echo "
        </details>
        ";
    }
} else {
    // Wenn das Quiz läuft
        echo "
    <div class='box shadow'>
        <h3 id='QuizRunning'>Quiz Nr. ".$quizId."</h3>
        <!-- Fortschrittsbalken für das Quiz (wo ist man momentan) -->
            <div id='progressbar'>
                <div style='width: ". 100 / $q_amount * $current."%'>
                <p>".$current." / ".$q_amount."</p>
                </div>
            </div>
            <form method='post'>";
        // SQL-Abfrage für die Frage + Antworten etc. für die momentane Frage (line ist ja die Reihenfolge)
        $sql = "SELECT q.question, q.correctAnswer, q.wrongAnswer0, q.wrongAnswer1, q.wrongAnswer2 FROM questions q, quiz qz, quiz_questions qq WHERE q.id = qq.questionId AND qz.id = qq.quizId AND qz.Id = ".$quizId." AND line = ".$current;

        foreach ($pdo->query($sql) as $row) {
            // Frage ausgeben
            echo "
                <label class='top' for='question'>Frage Nr. ".$current."</label>
                    <textarea required class='shadow' name='question' id='question' readonly>".$row["question"]."</textarea>
                    <label class='top'>Antworten</label>
                <div class='answers' id='answers'>
                    ";
            $array = array();
            // da das Array einmal die Werte aus der SQL-Abfrage hat also [question],[correctAnswer], etc. und dann noch einmal mit Indizes, werden nur die ersten fünf Elemente des Arrays übernommen und in das neue Array gepusht
            for($k = 0; $k < 5; $k++) {
                array_push($array, $row[$k]);
            }
            $row = $array;
            // nun wird die Frage (question) als dem Array rausgeshiftet bzw. das erste Element wird gelöscht
            array_shift($row);
            // hier werden die Antwortmöglichkeiten nochmals gemischelt
            shuffle($row);
            // i als Zählvariable für das Array answers
            $i = 1;
            foreach($row as $answer) {
                // jede Antwortmöglichkeit wird ausgegeben
                echo "
                            <div class='answer'>
                            <label class='longside' for='answer" . $i. "'>" . $answer . "</label>
                                <input type='radio' name='answer' id='answer" . $i . "' value='" . $answer . "' required>
                            </div>
                    ";
                $i++;
            }
        }
        // Wenn das Quiz die letzte Frage erreicht hat, wird wen neuer Button angezeigt, mit der Aufschrift "Ergebnis anzeigen" anstatt "Antwort bestätigen" hierbei werden dann alle wichtigen Parameter, wie die QuizId, die momentane Frage, die Anzahl der Fragen und die momentane Fage übergeben als value des buttons
        if($current == $q_amount) {
            echo "
                </div>
            <button name='buttonNext' id='buttonNext' value='".$quizId."|".$current."|".$q_amount."|".$row["question"]."' class='button shadow'>Ergebnis ansehen</button>
            ";
        } else {
            echo "
                </div>
            <button name='buttonNext' id='buttonNext' value='".$quizId."|".$current."|".$q_amount."|".$row["question"]."' class='button shadow'>Antwort bestätigen</button>
            ";
        }
        echo "
            </form>
    </div>
    ";
}

?>

</body>



</html>