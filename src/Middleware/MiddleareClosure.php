<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;

require __DIR__ .'/../vender/autoload.php';

$app = AppFactory::create();

$beforeMiddleware = function (Request $request, RequestHandler $handler) {
  $response = new Response();
  $response->getBody()->write('BEFORE' . $existingContent);

  return $response;
};

$afterMiddleware = function ($request, $handler) {
  $response = $handler->handle($request);
  $response->getBody()->write('AFTER');
  return $response;
};

$app->add($beforeMiddleware);
$app->add($afterMiddleware);