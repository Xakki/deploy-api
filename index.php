<?php


function jsonResponseError(string $mess, array $data = []): never
{
    $data = [
        'success' => false,
        'message' => $mess,
        'data' => $data,
    ];
    jsonResponse(400, $data);
}

function jsonResponseOk(string $mess, array $data): never
{
    $data = [
        'success' => true,
        'message' => $mess,
        'data' => $data,
    ];
    jsonResponse(200, $data);
}

function jsonResponse(int $code, array $response): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
}

function execCommand(string $dir, array $commands): array
{
    $flag = true;
    $out = [];
    foreach ($commands as $name => $command) {
        $output = null;
        $resultCode = null;
        $code = exec('cd ' . $dir . ' && ' . $command, $output, $resultCode);
        $out[$name] = $resultCode . ' : ' . implode(PHP_EOL, $output);
        if ($code === false) {
            $flag = false;
        }
    }
    return [$flag, $out];
}

$post = (array) json_decode((string) file_get_contents('php://input'), true);

if (trim($_SERVER['REQUEST_URI'], '/') === 'api' && count($post)) {

    $config = include 'config.php';

    if (isset($config[$post['project'] ?? ''])) {
        $conf = $config[$post['project']];
        if ($conf['token'] !== $post['token']) {
            jsonResponseError('Wrong token');
        }

        list($flag, $out) = execCommand($conf['dir'], $conf['command']);
        if ($flag) {
            jsonResponseOk('Success deploy for '. $post['project'], $out);
        } else {
            jsonResponseError('Fail deploy', $out);
        }
    }
    jsonResponseError('Wrong project');
} else {
    jsonResponseError('Hello world');
}