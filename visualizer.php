<!DOCTYPE html>
<head>
    <title>Visualizer</title>
<?php
error_reporting(0);

function stdDir($dir) { return trim($dir, '/')."/"; }
function printAsJsKeyValue($root) {
    foreach(scandir($root) as $file) {
        $code = substr($file,0,4);
        $path = $root.$file;
        echo "\"$code\" : \"$path\",";
    }
}

$config = json_decode(file_get_contents("../manager.conf") ?? "{}", true);
if(!$config) die("{success: 0, error: 1}");

$defaultPath = stdDir($config["default-svg"] ?? "./");
$userPath    = stdDir($config["svg-path"] ?? "./");
?>
    <script>
        var iconList = {
            <?php if($defaultPath && is_dir($defaultPath)) { printAsJsKeyValue($defaultPath); } ?>
            <?php if($userPath && is_dir($userPath)) { printAsJsKeyValue($userPath); } ?>
        };
    </script>
    <script src="assets/visualizer.js"></script>

    <link rel="stylesheet" href="assets/visualizer.css">
</head>
<body>
    <input id="selector" type="text" value="(?<=\\u)(\w{4})">
    <textarea id="editor" rows="10"></textarea>
    <pre id="preview"></pre>
</body>
</html>