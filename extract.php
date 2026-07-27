<?php
$content = file_get_contents('C:\xampp\htdocs\Capstone\temp_docx\word\document.xml');
$content = str_replace('<w:p>', "\n", $content);
$content = str_replace('<w:p ', "\n<w:p ", $content);
file_put_contents('C:\xampp\htdocs\Capstone\template_text.txt', strip_tags($content));
