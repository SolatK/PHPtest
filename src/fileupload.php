<!DOCTYPE html>
<html>
<head>
<title>Загрузка файла</title>
<meta charset="utf-8" />
</head>
<body>
<?php
if ($_FILES && $_FILES["filename"]["error"]== UPLOAD_ERR_OK)
{
    $name = $_FILES["filename"]["name"];
    if (!is_dir('src' . DIRECTORY_SEPARATOR . 'uploads')) {
        mkdir('src' . DIRECTORY_SEPARATOR . 'uploads', 0777, true);
    }
    move_uploaded_file($_FILES["filename"]["tmp_name"], 'src' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $name);
    echo 'Файл загружен и обработан!';
}
?>
<h2>Загрузка файла</h2>
<form method="post" enctype="multipart/form-data" action="">
<input type="hidden" name="MAX_FILE_SIZE" value="50000000" />
Выберите файл: <input type="file" name="filename" size="50000000" /><br /><br />
<input type="submit" value="Загрузить" />
</form>
</body>
</html>