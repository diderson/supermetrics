<?php
/*	======================================================
Didi Baka Senior Software Developer (dieudonnedbaka@gmail.com)
------------------------------------------------------
NOTE: This code is intellectual property of
Diderson Baka  and may not be
reproduced or used without prior permission.
------------------------------------------------------
Copyright 2021
======================================================	*/
require 'bootstrap.php';
use Didi\Models\Post;

$post = new Post;

echo '============================================== <br>';
echo 'Average character length of posts per month <br>';
echo '============================================== <br>';

echo json_encode($post->avgCharacterLengthPerMonth()) . '<br><br>';

echo '============================================== <br>';
echo 'Total posts split by week number <br>';
echo '============================================== <br>';

echo json_encode($post->totalPerWeek()) . '<br><br>';

echo '============================================== <br>';
echo 'get Average number of posts per user per month <br>';
echo '============================================== <br>';

echo json_encode($post->avgPerUserPerMonth()) . '<br><br>';

echo '============================================== <br>';
echo 'Longest post by character length per month <br>';
echo '============================================== <br>';

echo json_encode($post->longestPostPerMonth());
