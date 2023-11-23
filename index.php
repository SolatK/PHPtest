<?php
require_once('push.php');

if (isCommandLineInterface()) {
    $path = readline('Введите путь до файла: ');
    ProcessWords($path);
    ConnectDBAndPush();
} else {
    require_once('src/fileupload.php');
    if ($_FILES) {
        ProcessWords('src' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . end($_FILES)['name']);
        ConnectDBAndPush();
    }
}


function ProcessWords(string $path): void
{
    $path = trim($path, '"');
    if (file_exists($path) && is_file($path)) {
        $file = fopen($path, 'r');
    } else {
        echo 'Файла по заданному пути не существует.';
        exit();
    }


    $currentLetter = '';
    $dir = '';
    $data = fgets($file);

    mb_detect_order(['CP1251','CP1252','IBM866','UTF-8']);

    while ($data !== false) {
        if (trim($data) == '') {
            $data = fgets($file);
            continue;
        }

        if (mb_detect_encoding($data) != mb_internal_encoding()) {
            $text = trim(iconv(mb_detect_encoding($data), mb_internal_encoding(), $data));
        } else {
            $text = trim($data);
        }

        $words = preg_split('/[\d\n\r\s,?.!;()+-=*>"]+/', $text, 0, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $word) {
            if (isset($libCounter) && isset($counter) && $currentLetter && $currentLetter != mb_strtolower(mb_substr($word, 0, 1))) {
                rewind($libCounter);
                fwrite($libCounter, $counter);
            }
            if (!$currentLetter || $currentLetter != mb_strtolower(mb_substr($word, 0, 1))) {
                if (isset($libPage) && isset($libCounter)) {
                    fclose($libPage);
                    fclose($libCounter);
                }
                $currentLetter = mb_strtolower(mb_substr($word, 0, 1));
                $dir = 'library' . DIRECTORY_SEPARATOR . $currentLetter . DIRECTORY_SEPARATOR;

                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                $libPage = fopen($dir . 'words.txt', 'a');
                $libCounter = fopen($dir . 'count.txt', 'c+');
                $counter = (int)fread($libCounter, 10) ?? 0;
            }

            fwrite($libPage, $word . "\r\n");
            $counter += mb_substr_count(mb_strtolower($word), $currentLetter);
        }
        $data = fgets($file);
    }
    rewind($libCounter);
    fwrite($libCounter, $counter);
}

function isCommandLineInterface()
{
    return (php_sapi_name() === 'cli');
}
