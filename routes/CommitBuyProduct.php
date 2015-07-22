<?php

$app->post('/ReqCommitBuyProduct', function($request, $response) {
    $req = $request->getParsedBody();
/*
{
}
*/

    $template = [
        'ReqCommitBuyProduct_NotRedyYet'
    ];

    return render($response, $template);
});
