<?php
require_once('push.php');

if(isCommandLineInterface()) {
    $path = readline('Введите путь до файла: ');
    ProcessWords($path);
    ConnectDBAndPush();
} else {
    require_once('src/fileupload.php');
    if($_FILES) {
        ProcessWords('src/uploads' . DIRECTORY_SEPARATOR . end($_FILES)['name']);
        ConnectDBAndPush();
    }
}


function ProcessWords(string $path): void
{
    $file = fopen($path, 'r');

    $currentLetter = '';
    $dir = '';
    $data = true;

    while ($data !== false) {
        $data = fgets($file);
        $word = trim($data);

        if ($word == '') {
            continue;
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
        rewind($libCounter);
        fwrite($libCounter, $counter);
    }
}

function isCommandLineInterface()
{
    return (php_sapi_name() === 'cli');
}