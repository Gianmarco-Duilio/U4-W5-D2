<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "language";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connessione fallita: " . $e->getMessage());
}

// LANGUAGE____________________________________________

$supported_languages = ['en', 'it'];
$default_language = 'en';

if (isset($_COOKIE['language']) && in_array($_COOKIE['language'], $supported_languages)) {
    $current_language = $_COOKIE['language'];
} else {
    $current_language = $default_language;
}

function translate($lang_key, $conn, $current_language)
{
    $query = "SELECT $current_language FROM labels WHERE lang_key = :lang_key";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':lang_key', $lang_key);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result[$current_language];
    } else {
        return $lang_key;
    }
}

function getNews($conn, $current_language)
{
    $news = array();
    $query = "SELECT id, title_$current_language AS title, content_$current_language AS content FROM news";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}

setcookie('language', $current_language, time() + (86400 * 30), "/");
if (!isset($_COOKIE['language'])) {
    setcookie('language', 'it', $cookie_expiration);
    $language = 'it';
} else {
    $language = $_COOKIE['language'];
}


// THEME____________________________________________

if (isset($_GET['change-theme'])) {
    // Cambia il tema invertendo tra chiaro e scuro
    $newTheme = ($_COOKIE['theme'] === 'light') ? 'dark' : 'light';
    setcookie('theme', $newTheme, time() + 5 * 60);
    header('Location: ./');
    exit;
}

$islight = isset($_COOKIE['theme']) ? ($_COOKIE['theme'] === 'light') : true;
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <!-- META_TAGS -->
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
    <!-- CSS -->
    <link rel="stylesheet" href="<?= $islight ? 'style-light.css' : 'style-dark.css' ?>">

    <!-- WEBPAGE_NAME -->
    <title><?php echo translate('page_title', $conn, $current_language) ?></title>

</head>

<body>
    <div>
        <?php
        $news = getNews($conn, $current_language);
        foreach ($news as $article) {
            echo "<h1>{$article['title']}</h1>";
            echo "<p>{$article['content']}</p>";
        }
        ?>
        <form action="<?= 'http://localhost/Corso%20Epicode-Ifoa%20Back%20End/U4-W5-D2/change-language.php' ?>" method="get">
            <select name="language">
                <option value="it" <?= $language === 'it' ? ' selected' : '' ?>>IT</option>
                <option value="en" <?= $language === 'en' ? ' selected' : '' ?>>EN</option>

            </select>
            <button>change</button>
        </form>
        <?php echo translate('footer_message', $conn, $current_language); ?>
    </div>
    <a href="?change-theme">Cambia tema</a>
</body>

</html>