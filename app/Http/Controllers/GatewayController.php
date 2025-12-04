<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use Illuminate\Http\Response;

use GuzzleHttp\Client;



class GatewayController extends Controller

{

    private $httpClient;



    public function __construct()

    {

        $this->httpClient = new Client();

    }



    /**


     * @param Request 

     * @param string 

     * @param string 

     * @return Response 

     */

    public function proxy(Request $request, string $service, string $path = '')

    {


        switch ($service) {

            case 'productos':

                $host = env('PRODUCTS_SERVICE_HOST');

                $port = env('PRODUCTS_SERVICE_PORT');

                break;

            case 'ventas-clientes':

                $host = env('SALES_CLIENTS_SERVICE_HOST');

                $port = env('SALES_CLIENTS_SERVICE_PORT');

                break;

            default:


                return response()->json(['error' => 'Servicio de destino no encontrado'], Response::HTTP_NOT_FOUND);

        }



        $baseUrl = "http://{$host}:{$port}/";

        $fullUrl = $baseUrl . $path;




        try {

            $response = $this->httpClient->request($request->method(), $fullUrl, [


                'body' => $request->getContent(),

                'headers' => $request->headers->all(),

                'query' => $request->query() 

            ]);




            return response($response->getBody(), $response->getStatusCode())

                ->withHeaders($response->getHeaders());



        } catch (\GuzzleHttp\Exception\RequestException $e) {


            if ($e->hasResponse()) {

                $statusCode = $e->getResponse()->getStatusCode();

                $body = $e->getResponse()->getBody();

                return response($body, $statusCode)->withHeaders($e->getResponse()->getHeaders());

            }




            return response()->json(['error' => 'Error de comunicación con el servicio interno.'], Response::HTTP_SERVICE_UNAVAILABLE); 

        }

    }




}