<?php

use App\DataProcessing;

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

function calculateSilhouetteScore($data, $labels)
{
    $n = count($data);
    $silhouetteScores = [];

    for ($i = 1; $i <= $n; $i++) {
        $a = 0;
        $b = INF;
        $label = $labels[$i];

        // Calculate the average distance of the point to all other points in the same cluster
        foreach ($labels as $j => $otherLabel) {
            if ($otherLabel === $label) {
                $a += DataProcessing::cosineDistance($data[$i], $data[$j]);
            }
        }

        $a /= max(array_count_values($labels)[$label] - 1, 1);

        // Calculate the average distance of the point to all points in the nearest cluster
        foreach (array_unique($labels) as $otherLabel) {
            if ($otherLabel !== $label) {
                $b = min($b, averageDistanceToCluster($data, $labels, $i, $otherLabel));
            }
        }

        $silhouetteScores[] = ($b - $a) / max($a, $b);
    }

    return array_sum($silhouetteScores) / $n;
}
function averageDistanceToCluster($data, $labels, $index, $clusterLabel)
{
    $sum = 0;
    $count = 0;

    foreach ($labels as $i => $label) {
        if ($label === $clusterLabel) {
            $sum += DataProcessing::cosineDistance($data[$index], $data[$i]);
            $count++;
        }
    }

    return $sum / max($count, 1);
}
