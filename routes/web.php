<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->options('/{any:.*}', function () {
    return response('', 200);
});

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/clientes[/{path:.*}]', function (\Illuminate\Http\Request $request, $path = '') {
        $client = new \GuzzleHttp\Client();
        $url = 'http://miramar-ventas-clientes:80/clientes' . ($path ? '/' . $path : '');
        return forwardRequest($client, $request, $url);
    });

    $router->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/ventas[/{path:.*}]', function (\Illuminate\Http\Request $request, $path = '') {
        $client = new \GuzzleHttp\Client();
        $url = 'http://miramar-ventas-clientes:80/ventas' . ($path ? '/' . $path : '');
        return forwardRequest($client, $request, $url);
    });

    $router->addRoute(['GET', 'POST', 'PUT', 'DELETE'], '/productos/{resource}[/{path:.*}]', function (\Illuminate\Http\Request $request, $resource, $path = '') {
        $client = new \GuzzleHttp\Client();
        $url = 'http://miramar-productos:80/' . $resource . ($path ? '/' . $path : '');
        return forwardRequest($client, $request, $url);
    });
});

function forwardRequest($client, $request, $url) {
    try {
        $response = $client->request($request->method(), $url, [
            'headers' => $request->headers->all(),
            'body' => $request->getContent(),
            'query' => $request->query(),
            'http_errors' => false
        ]);
        return response($response->getBody(), $response->getStatusCode())->withHeaders($response->getHeaders());
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error en Gateway: ' . $e->getMessage()], \Illuminate\Http\Response::HTTP_SERVICE_UNAVAILABLE);
    }
}