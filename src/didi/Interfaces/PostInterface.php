<?php
namespace Didi\Interfaces;

interface PostInterface {

	public function getAll();

	public function avgCharacterLengthPerMonth();

	public function longestPostPerMonth();

	public function totalPerWeek();

	public function avgPerUserPerMonth();
}