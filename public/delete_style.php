<?php header("Content-Type: text/plain"); $content = file_get_contents("../app/views/layouts/main.php"); $content = preg_replace("/<style>.*?<\/style>/s", "", $content); $content = preg_replace("/<script>.*?<\/script>/s", "<script src=\"/js/main.js\"></script>", $content); file_put_contents("../app/views/layouts/main.php", $content); echo "删除成功！"; ?>
