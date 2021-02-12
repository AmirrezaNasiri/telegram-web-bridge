<?php

use Slim\Http\Request;
use Slim\Http\Response;
use GuzzleHttp\Client;

// Routes

$app->get('/test', function (Request $request, Response $response, array $args) {
	die('Routes are being hit successfully.');
})->setName('test');

$app->get('/bot{token}/{method}', function (Request $request, Response $response, array $args) {
	$token = $args['token'];
	$method = $args['method'];
	if ($this->get('settings')['logApiCalls'])
		$this->logger->info("API call by {$token}: '{$method}' ", ['asd']);

	$url = "https://api.telegram.org/bot{$token}/{$method}?";

	if(strlen($request->getUri()->getQuery()) > 0){
	    $url .= $request->getUri()->getQuery();
	}
	
	$client = new Client([
		'http_errors' => false,
	]);

	return $client->get($url);
})->setName('GET_ApiCall');

$app->post('/bot{token}/{method}', function (Request $request, Response $response, array $args) {
	$token = $args['token'];
	$method = $args['method'];
	if ($this->get('settings')['logApiCalls'])
		$this->logger->info("API call by {$token}: '{$method}' ", ['asd']);

	$url = "https://api.telegram.org/bot{$token}/{$method}";
	$client = new Client([
		'http_errors' => false,
	]);

	$multipart = [];

	$data = $request->getParsedBody();
	$files = $request->getUploadedFiles();

	$setting_endpoints = $this->get('settings')['webhookEndpoints'];
	if ($method === 'setWebhook' && isset($setting_endpoints[$token])) {
		$appUrl = rtrim($this->get('settings')['appUrl'], '/');
		$data['url'] = "$appUrl/webhooks/{$token}";
		if ($certificate = $this->get('settings')['crtPath'])
			$files['certificate'] = new \Slim\Http\UploadedFile($certificate);
		else
			unset($files['certificate']);
	}

	if ($data)
		foreach ($data as $key => $value) {
			$multipart[] = [
				'name'     => $key,
				'contents' => $value
			];
		}

	if ($files)
		foreach ($files as $key => $uploadedFile) {
			/** @var \Slim\Http\UploadedFile $uploadedFile */
			$multipart[] = [
				'name'     => $key,
				'contents' => fopen($uploadedFile->file, 'r')
			];
		}

	return $client->post($url, [
		'multipart' => $multipart,
	]);
})->setName('POST_ApiCall');


$app->post('/webhooks/{token}', function (Request $request, Response $response, array $args) {
	$token = $args['token'];
	if ($this->get('settings')['logWebhookCalls'])
		$this->logger->info("Webhook for $token");

	// Try to set real endpoint from either main or test settings file.
	$setting_endpoints = $this->get('settings')['webhookEndpoints'];
	$real_endpoint = '';
	if (isset($setting_endpoints[$token]))
		$real_endpoint = $setting_endpoints[$token];

	if (!$real_endpoint)
		return $response->withStatus(404);

	$client = new Client([
		'http_errors' => false,
		'verify' => false, // Skip SSL verification for real webhook handler
	]);

	$request = new \GuzzleHttp\Psr7\Request(
		'POST',
		$real_endpoint,
		$request->getHeaders(),
		$request->getBody()->getContents()
	);

	return $client->send($request);
})->setName('POST_WebhookCall');
