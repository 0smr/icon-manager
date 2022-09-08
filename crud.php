<?php
error_reporting(0);

function stdDir($dir) { return trim($dir, '/')."/"; }

class CRUD {
    public $userDir;

    function __construct() {
        $config = json_decode(file_get_contents("../manager.conf") ?? "{}", true);
        if(!$config) die("{success: 0, error: 1}");

        $dir = $config["svg-path"];

        if(!is_dir($dir)) mkdir($dir); // Make directory if not exist.

        $this->userDir = stdDir($dir);
    }

    public function addIcon(?string $fileName, $fileContent): bool {
        if($fileName && $fileContent) {
            return boolval(file_put_contents($this->userDir.$fileName, $fileContent));
        } else {
            return false;
        }
    }

    public function removeIcon(?string $fileName): bool {
        return $fileName ? unlink($this->userDir.$fileName) : false;
    }

    public function generateFont(): array {
        $output = "";
        $result = 0;
        exec("python3 font-generator.py", $output, $result);
        return [!$result, $output];
    }
}

$data = json_decode(file_get_contents('php://input'), true);
if($data) {
    $crud = new CRUD;
    $success = false;
    $message = "";

    switch($data["op"]) {
        case "add":
            $success = $crud->addIcon($data["name"], $data["content"]);
            break;
        case "remove":
            $success = $crud->removeIcon($data["name"]);
            break;
        case "generateFont":
            list($success, $message) = $crud->generateFont();
            break;
    }

    echo json_encode([
        "success" => $success,
        "message" => $message,
    ]);
}
?>