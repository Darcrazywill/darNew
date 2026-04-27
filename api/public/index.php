<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);
$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$action = basename($path);
switch ($action) {
  case 'hello':
      echo json_encode(['message' => 'Hello, API!', 'method' => $method]);
  break;
  case 'echo':
      $input = json_decode(file_get_contents('php://input'), true);
      echo json_encode([
            'received' => $input ?? $_GET ?? $_POST,
           'headers'  => getallheaders(),
           'method'   => $method
       ]);
       break;
  case 'date':
       echo json_encode([
           'timestamp' => time(),                           
           'date'      => date('Y-m-d'),
           'time'      => date('H:i:s'),
           'tz'        => date_default_timezone_get()
       ]);
       break;
   default:
       http_response_code(404);
       echo json_encode(['error' => 'Endpoint not found']);
}





?>
