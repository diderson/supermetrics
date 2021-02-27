<?php
require 'constants.php';
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

final class GatewayTest extends TestCase {

	public $token;
	public function testCanGetToken(): void{

		$sl_token = $this->getToken();
		$this->assertNotNull($sl_token);

	}

	public function testCanFetchPost(): array{
		$sl_token = $this->getToken();
		$params = [
			'query' => [
				'sl_token' => $sl_token,
				'page' => 1,
			],
			'http_errors' => false,
		];

		$client = new Client();
		$response = $client->get(SM_API_URL . 'posts', $params);
		$contents = $response->getBody()->getContents();
		$result = json_decode($contents, true);
		$posts = (isset($result['data'])) ? $result['data'] : [];

		$this->assertNotEmpty($posts);

		return $posts;
	}

	private function getToken() {
		$client = new Client();
		$form_params = [];
		$form_params['client_id'] = SM_CLIENT_ID;
		$form_params['name'] = SM_NAME;
		$form_params['email'] = SM_EMAIL;
		$response = $client->post(SM_API_URL . 'register', ['form_params' => $form_params, 'http_errors' => false]);
		$contents = $response->getBody()->getContents();
		$result = json_decode($contents, true);
		$sl_token = (isset($result['data']['sl_token'])) ? $result['data']['sl_token'] : '';

		return $sl_token;
	}
}