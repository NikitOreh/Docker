<?php
// Укажите путь к makefont.php
require_once __DIR__ . '/fpdf/makefont.php';

// Укажите путь к вашему .ttf файлу
$ttf_file = __DIR__ . '/fpdf/DejaVuSans.ttf'; // Замените на реальный путь к файлу

// Укажите папку, где будут сохранены сгенерированные файлы
$font_dir = __DIR__ . '/fpdf/font/';

// Генерация шрифта
if (file_exists($ttf_file)) {
    MakeFont($ttf_file, $font_dir);
    echo "Шрифт успешно сгенерирован. Проверьте папку $font_dir на наличие файлов dejavusans.php и dejavusans.z.";
} else {
    die("Файл шрифта $ttf_file не найден!");
}
?>