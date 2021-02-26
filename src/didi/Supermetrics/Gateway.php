<?php
namespace Didi\Supermetrics;

use Carbon\Carbon;
use GuzzleHttp\Client;

class Gateway {

	private $client_id;
	private $email;
	private $name;
	private $api_url;

	public function __construct() {

		$this->client_id = SM_CLIENT_ID;
		$this->email = SM_EMAIL;
		$this->name = SM_NAME;
		$this->api_url = SM_API_URL;
	}

	/**
	 * get Supermetrics Token
	 * @return return token
	 */
	public function getToken() {

		try {
			$sl_token = (isset($_COOKIE["sl_token"])) ? $_COOKIE["sl_token"] : '';

			#check if token is expired then request new one
			if (empty($sl_token)) {
				$client = new Client();
				$form_params = [];
				$form_params['client_id'] = $this->client_id;
				$form_params['name'] = $this->name;
				$form_params['email'] = $this->email;
				$response = $client->post($this->api_url . 'register', ['form_params' => $form_params, 'http_errors' => false]);
				$contents = $response->getBody()->getContents();
				$result = json_decode($contents, true);

				/*
					* set token in cookie for an hour since
					* there is no endpoint to check token lifetime
				*/
				$sl_token = (isset($result['data']['sl_token'])) ? $result['data']['sl_token'] : '';

				if (!empty($sl_token)) {
					setcookie("sl_token", $sl_token, time() + 3600); /* expire in 1 hour */
				}

			}

			return $sl_token;

		} catch (Exception $e) {
			echo $e->getMessage();
			exit;
		}

	}

	/**
	 * fetch posts per single page
	 * @return return array
	 */
	public function fetchPostsPerPage($page = 1) {
		$sl_token = $this->getToken();
		$params = [
			'query' => [
				'sl_token' => $sl_token,
				'page' => $page,
			],
			'http_errors' => false,
		];

		$client = new Client();
		$response = $client->get($this->api_url . 'posts', $params);
		$contents = $response->getBody()->getContents();
		$result = json_decode($contents, true);

		return $result;

	}

	/**
	 * fetch brut post and save it in json file
	 * @return return void
	 */
	public function fetchBrutPosts($total_page = 10) {
		#check if we must connect to the server
		if (!isset($_COOKIE["connect_to_srv"])) {
			$sl_token = $this->getToken();
			$total_page = (is_numeric($total_page)) ? $total_page : 10;
			$promises = [];
			$posts = [];
			$client = new Client();

			for ($i = 1; $i <= $total_page; $i++) {
				$params = [
					'query' => [
						'sl_token' => $sl_token,
						'page' => $i,
					],
					'http_errors' => false,
				];
				$promises[] = $client->requestAsync('GET', $this->api_url . 'posts', $params);

			}

			\GuzzleHttp\Promise\all($promises)->then(function (array $responses) {
				foreach ($responses as $key => $response) {
					$posts[$key] = json_decode($response->getBody()->getContents(), true);
				}
				/*
					let save posts  inside a json file.
					we can improve this by using any other storage such as database
					for better performance while dealing with more than 3000 rows of data
					we can also have a cron job that saves periodically data in storage
				*/
				$file = fopen("temp_posts.json", "w") or exit("Unable to open file!");
				fwrite($file, json_encode($posts));
				fclose($file);

			})->wait();

			#set cookie to check if we must do request again to the server
			setcookie("connect_to_srv", true, time() + 3600); /* expire in 1 hour*/
		}

		return;

	}

	/**
	 * fetch posts per total page
	 * @return return array
	 */
	public function fetchPosts($total_page = 10) {
		$this->fetchBrutPosts();
		$temp_posts = file_get_contents("temp_posts.json");
		$posts = [];
		$temp_data = json_decode($temp_posts, true);

		$count = -1;

		foreach ($temp_data as $key => $temp_value) {
			$extract_posts = (isset($temp_value['data']['posts'])) ? $temp_value['data']['posts'] : [];
			for ($i = 0; $i < count($extract_posts); $i++) {
				$count++;
				$id = (isset($extract_posts[$i]['id'])) ? $extract_posts[$i]['id'] : '';
				$from_name = (isset($extract_posts[$i]['from_name'])) ? $extract_posts[$i]['from_name'] : '';
				$from_id = (isset($extract_posts[$i]['from_id'])) ? $extract_posts[$i]['from_id'] : '';
				$message = (isset($extract_posts[$i]['message'])) ? $extract_posts[$i]['message'] : '';
				$type = (isset($extract_posts[$i]['type'])) ? $extract_posts[$i]['type'] : '';
				$created_time = (isset($extract_posts[$i]['created_time'])) ? $extract_posts[$i]['created_time'] : '';

				$posts[$count] = [
					'id' => $id,
					'from_name' => $from_name,
					'from_id' => $from_id,
					'message' => $message,
					'type' => $type,
					'length' => strlen($message),
					'month' => Carbon::parse($created_time)->format('M-Y'),
					'year' => Carbon::parse($created_time)->format('Y'),
					'week' => 'week' . Carbon::parse($created_time)->week . '-' . Carbon::parse($created_time)->format('Y'),
					'created_time' => $created_time,
					'scraped_at' => Carbon::now()->format('Y-m-d h:s:i'),
				];
			}
		}
		return $posts;
	}
}