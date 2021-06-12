<?php
function pre($arr = [], $quit = false) {
    echo "<pre>";
    print_r($arr);
    echo "</pre>";

    if($quit) exit;
}

function send_error($status = 400, $msg = 'Requisição invalida') {
    echo json_encode(['status' => $status, 'msg' => $msg]);exit;
}

function send_success($status = 200, $data = null) {
    echo json_encode(['status' => $status, 'data' => $data]);exit;
}