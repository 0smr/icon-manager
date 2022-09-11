<!DOCTYPE html>
<head>
    <title>Glyph Viewer</title>
    <link rel="stylesheet" href="assets/index.css">
    <script src="https://cdn.jsdelivr.net/npm/svg-path-commander/dist/svg-path-commander.min.js"></script>
    <script src="assets/index.js"></script>
</head>
<body>
<?php
error_reporting(0);

function stdDir($dir) { return trim($dir, '/')."/"; }

function initializeTable(array $iconList, $rootPath, &$table) {
    foreach($iconList as $index => $file) {
        $isfile = $file[0] != ".";
        if($isfile) {
            $row = substr($file,0,3); // 3 letter icon row "xxx".
            $col = hexdec($file[3]);  // Icon column a number from 0 to 16.

            $table[$row] ??= array_fill(0,15,"");
            $table[$row][$col] = $rootPath.$file;
        }
    }
}

$config = json_decode(file_get_contents("../manager.conf") ?? "{}", true);
if(!$config) die("{success: 0, error: 1}");

$defaultPath = stdDir($config["default-svg"] ?? "./");
$userPath    = stdDir($config["svg-path"] ?? "./");
$iconTable   = [];

if($defaultPath && is_dir($defaultPath)) {
    initializeTable(scandir($defaultPath), $defaultPath, $iconTable);
}
if($userPath && is_dir($userPath)) {
    initializeTable(scandir($userPath), $userPath, $iconTable);
}

?>
    <div id="unicode-table">
        <div class="head">
            <div class="col"><h1 style="display:inline;margin:0;">Glyph Viewer</h1></div>
            <div class="col"><input id="file-dialog" type="file" accept=".svg"></div>
            <div class="col">
                <label class="button">
                    <input id="generator" type="button" value="generate"></input>
                </label>
            </div>
        </div>
        <div class="grid">
        <?php
            foreach($iconTable as $rkey => $cols) {
                echo "<div class='row'>";
                foreach($cols as $ckey => $icon) {
                    if($icon) {
                        $base = pathinfo($icon, PATHINFO_FILENAME);
                        echo "<div class='col'><img src='$icon' alt='$base'><b class='fix'>F</b><i>$base</i></div>";
                    } else {
                        $hex = dechex($ckey);
                        echo "<div class='col'><b class='add'>$rkey$hex</b></div>";
                    }
                }
                echo "</div>";
            }
        ?>
        </div>
    </div>
    <div id="logger"></div>
    <div id="cover"></div>
    <div id="modal">
        <div class="grid">
            <div class="row">
                <div id="container" class="col"></div>
                <div class="vguide">0px</div>
                <div class="hguide">0px</div>
            </div>
        </div>
    </div>
</body>
</html>