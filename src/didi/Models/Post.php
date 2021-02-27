<?php
namespace Didi\Models;

use Didi\Interfaces\PostInterface;
use Didi\Supermetrics\Gateway;

class Post implements PostInterface {

	private $posts;
	private $posts_collection;

	public function __construct($total_page = 10) {
		$this->gateway = new Gateway();
		$this->posts = $this->gateway->fetchPosts();
		$this->posts_collection = collect($this->posts);
	}

	/**
	 * get all post using total given page
	 *
	 * @param $total_page
	 * @return Array
	 */
	public function getAll() {
		return $this->posts;
	}

	/**
	 * Average character length of posts per month
	 *
	 * @return Array
	 */
	public function avgCharacterLengthPerMonth() {
		$posts_per_month = $this->posts_collection->groupBy('month');

		$avg_posts_tmp = $posts_per_month->map(function ($posts) {
			$sum_length = $posts->sum('length');
			$count = $posts->count();
			return round(($sum_length / $count), 2);
		})->all();

		return $avg_posts_tmp;

	}

	/**
	 * Longest post by character length per month
	 *
	 * @return Posts Array
	 */
	public function longestPostPerMonth() {
		$posts_per_month = $this->posts_collection->groupBy('month');
		$posts_max_arr = [];

		$posts_max_tmp = $posts_per_month->map(function ($posts) {
			return $posts->max('length');
		});

		$k = 0;
		#build unique keys for search in collection
		foreach ($posts_max_tmp as $post) {
			$posts_max[$k] = $post;
			$k++;
		}

		$longest_posts = $posts_per_month->map(function ($posts) use ($posts_max) {
			$collection = $posts->whereIn('length', $posts_max);
			$unique = $collection->unique('length')->collapse()->all();
			return $unique;
		})->all();

		return $longest_posts;

	}

	/**
	 * Total posts split by week number
	 *
	 * @return Array
	 */
	public function totalPerWeek() {
		$posts_per_week = $this->posts_collection->groupBy('week');
		$total_posts_per_week = [];

		foreach ($posts_per_week as $wk => $week) {
			$total_posts_per_week[$wk] = count($week);
		}

		return $total_posts_per_week;

	}

	/**
	 * get Average number of posts per user per month
	 *
	 * @return Array
	 */
	public function avgPerUserPerMonth() {
		$user_posts = $this->posts_collection->groupBy('from_id');
		$posts_per_month = $this->posts_collection->groupBy('month');
		$total_user = count($user_posts);
		$avg_posts_per_month = [];

		foreach ($posts_per_month as $mk => $month) {
			$avg_posts_per_month[$mk] = round(count(($month) / $total_user), 2);
		}

		return $avg_posts_per_month;
	}
}