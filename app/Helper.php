<?php

function snakeTitle(string $str)
{
    $snake_title = preg_replace('/\s+/', '_', $str); // Replace spaces with underscores
    $snake_title = preg_replace('/[^a-zA-Z0-9]/', '_', $snake_title); // Replace non-alphanumeric characters with underscores
    strtolower($snake_title);

    return $snake_title;
}

function calculateMeanSquaredError($actual, $predicted)
{
    $sum = 0;
    $n = count($actual);

    for ($i = 0; $i < $n; $i++) {
        $sum += pow($actual[$i] - $predicted[$i], 2);
    }

    return $sum / $n;
}
