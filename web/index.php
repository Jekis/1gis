<?php

use Symfony\Component\HttpFoundation\JsonResponse;

require_once __DIR__.'/../bootstrap.php';


// Response with json on all errors.
$app->error(function (\Exception $e, $code) {
    $error = array(
        'message' => $e->getMessage(),
    );

    // Dirty check, but it's ok for this purpose.
    $isHttpErrorCode = preg_match('~^[4|5]\d\d$~', $code);

    if ($isHttpErrorCode) {
        $status = $code;
    } else {
        $status = 400;
        $error['code'] = $code;
    }

    if ($code === 404) {
        $error['message'] = 'Not found';
    }

    return new JsonResponse(array('error' => $error), $status);
});


$app->run();
