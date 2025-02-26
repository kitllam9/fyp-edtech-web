<?php

use App\Models\Recommendation;
use App\Models\Tag;
use App\Models\User;
use Tigo\Recommendation\Recommend;

use NlpTools\FeatureFactories\DataAsFeatures;
use NlpTools\Tokenizers\WhitespaceTokenizer;
use NlpTools\Documents\TokensDocument;
use NlpTools\Documents\TrainingSet;
use NlpTools\Models\Lda;
use StopWords\StopWords;

use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;

use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Kernels\Distance\Cosine;
use Rubix\ML\Clusterers\Seeders\PlusPlus;
use Rubix\ML\CrossValidation\Reports\ContingencyTable;
use Rubix\ML\CrossValidation\Reports\ConfusionMatrix;

use App\DataProcessing;

// test('performance test', function () {
//     Tag::factory(100)->create();
//     User::factory(5)->create();
//     Recommendation::factory(200)->create();

//     $client = new Recommend();
//     $records = Recommendation::all()->toArray();

//     shuffle($records);

//     // Determine the size of the held-out set (e.g., 20% of the data)
//     $holdoutSize = 0.2 * count($records);

//     // Extract the held-out set from the existing training set
//     $holdoutSet = array_slice($records, 0, $holdoutSize);

//     foreach ($holdoutSet as $key => $value) {
//         unset($records[$value['id']]);
//     }

//     $train = $records;

//     // Define the number of folds for cross-validation
//     $numFolds = 5;

//     // Perform K-fold cross-validation
//     for ($fold = 0; $fold < $numFolds; $fold++) {
//         $holdoutRating = array_values($client->ranking($holdoutSet, 1));
//         $trainRating = array_values($client->ranking($train, 1));

//         dump($holdoutRating, $trainRating);
//         $errors = [];
//         for ($i = 0; $i < count($holdoutRating); $i++) {
//             $error = pow($holdoutRating[$i] - $trainRating[$i], 2);
//             $errors[] = $error;
//         }

//         // Calculate MSE for this fold
//         $mse = array_sum($errors) / count($errors);
//         $perFoldMSE[] = $mse;
//     }
//     dump($perFoldMSE);
// });

// test('lda test', function () {
//     $stopwords = new StopWords('en');
//     $str = $stopwords->clean(
//         "
//             Art and humanities have shared a deep-rooted connection throughout history, reflecting the
//             essence of culture, emotions, and societal values. From ancient cave paintings to modern
//             digital creations, art serves as a powerful medium of expression, communication, and
//             introspection.
//             Artists, whether painters, sculptors, musicians, writers, or performers, channel their creativity
//             to convey thoughts, emotions, and messages that resonate with individuals and communities.
//             Through their work, artists capture the beauty, struggles, and complexities of the human
//             experience, fostering empathy and understanding across diverse perspectives.
//             Art has the remarkable ability to transcend language barriers, geographical boundaries, and
//             time, serving as a universal language that speaks to the depths of the human soul. It has the
//             power to evoke a wide range of emotions, from joy and inspiration to sadness and
//             introspection, prompting viewers to contemplate their own beliefs, values, and experiences.
//             In addition to its emotional impact, art plays a crucial role in shaping cultural identities,
//             challenging societal norms, and sparking conversations about important issues. Whether it's a
//             thought-provoking painting, a poignant piece of music, or a captivating dance performance,
//             art has the capacity to provoke change, foster dialogue, and inspire individuals to see the
//             world through a different lens.
//             As technology continues to evolve, new forms of artistic expression emerge, blurring the lines
//             between traditional and contemporary art. Digital art, virtual reality experiences, interactive
//             installations, and multimedia collaborations redefine how artists engage with audiences and
//             explore innovative ways to communicate their narratives.
//             Art and humanities are intertwined in a complex and profound relationship, with art serving as
//             a mirror that reflects the diversity, creativity, and resilience of the human spirit. Through art,
//             individuals can find solace, inspiration, and connection, forging bonds that transcend cultural
//             boundaries and unite people in a shared appreciation for the beauty and complexity of the
//             world.
//             "
//     );

//     $tok = new WhitespaceTokenizer();

//     $d = new TokensDocument(
//         $tok->tokenize(
//             $str,
//         )
//     );

//     $_train = new TrainingSet();
//     $_train->addDocument(
//         '',
//         $d

//     );

//     // Assuming you have already defined $train as your existing TrainingSet
//     $documents = $d->getDocumentData(); // Get all documents from the existing training set

//     // Shuffle the documents to randomize the order
//     shuffle($documents);

//     // Determine the size of the held-out set (e.g., 20% of the data)
//     $heldOutSize = 0.2 * count($documents);

//     // Extract the held-out set from the existing training set
//     $heldOutDocuments = array_slice($documents, 0, $heldOutSize);

//     // Create a new TrainingSet for the held-out documents
//     $heldOutSet = new TrainingSet();
//     foreach ($heldOutDocuments as $doc) {
//         $heldOutSet->addDocument('', new TokensDocument(
//             $tok->tokenize(
//                 $doc,
//             )
//         ));
//         unset($documents[$doc]);
//     }

//     // Remove the held-out documents from the original training set
//     $train = new TrainingSet();
//     foreach ($documents as $doc) {
//         $train->addDocument(
//             '', // the class is not used by the lda model
//             new TokensDocument(
//                 $tok->tokenize(
//                     $doc,
//                 )
//             )
//         );
//     }

//     $lda = new Lda(
//         new DataAsFeatures(), // a feature factory to transform the document data
//         5, // the number of topics we want
//         1.2, // the dirichlet prior assumed for the per document topic distribution
//         1.2  // the dirichlet prior assumed for the per word topic distribution
//     );

//     // Define the number of folds for cross-validation
//     $numFolds = 5;
//     $perFoldResults = [];

//     // Perform K-fold cross-validation
//     for ($fold = 0; $fold < $numFolds; $fold++) {
//         // Split your dataset into training and validation sets for this fold

//         // Train the LDA model on the training set
//         $lda->train($train, 100); // You may need to adjust the number of iterations

//         $evaluationResult = calculatePerplexity($lda, $heldOutSet);

//         // Store the evaluation results for this fold
//         $perFoldResults[] = $evaluationResult;
//     }

//     dump($perFoldResults);
// });

// function calculatePerplexity($ldaModel, $heldOutSet)
// {
//     $logLikelihood = 0.0;
//     $numTokens = 0;

//     $ldaModel->train($heldOutSet, 100);
//     $logLikelihood = $ldaModel->getWordsPerTopicsProbabilities();
//     foreach ($heldOutSet as $document) {
//         $numTokens += count($document->getDocumentData());
//     }

//     dump($logLikelihood);
//     return 0;
// }


test('k-means clustering performance', function () {
    Tag::factory(100)->create();
    User::factory(10)->create();
    // Get the interests as string
    $samples = User::select('id', 'interests')->get()->map(function ($item) {
        return [$item['id'] => json_encode($item['interests'])];
    })->reject(function ($item) {
        return reset($item) === 'null';
    })->toArray();

    $values = array();
    $keys = array();
    foreach ($samples as $v) {
        $values = array_merge($values, array_values($v));
        $keys = array_merge($keys, array_keys($v));
    }
    $samples = array_combine($keys, $values);

    $vectorizer = new TokenCountVectorizer(new WordTokenizer());
    $vectorizer->fit($samples);
    $vectorizer->transform($samples);

    $tfidf = array_values($samples);
    $transformer = new TfIdfTransformer($tfidf);
    $transformer->transform($tfidf);

    $dataset = new Labeled($tfidf, $keys);
    $arrDataset = array_combine($dataset->labels(), $dataset->samples());

    $maxClusters = 10;

    $prevWcss = null;
    $wcssChanges = [];

    $finalResult = [];
    $finalK = 0;

    for ($k = 1; $k <= $maxClusters; $k++) {
        $estimator = new KMeans($k, kernel: new Cosine(), seeder: new PlusPlus(new Cosine()));
        $estimator->train($dataset);

        $report = new ContingencyTable();
        $result = $report->generate($estimator->predict($dataset), $keys)->toArray();

        foreach ($result as $id => $clusterData) {
            $filtered = array_map(function ($subarray) {
                return array_filter($subarray, function ($value) {
                    return $value !== 0;
                });
            }, $result);

            $result = $filtered;
        }

        $finalResult[$k] = $result;

        $currentWcss = 0;

        foreach ($estimator->centroids() as $clusterId => $clusterData) {
            $_data = array_intersect_key($arrDataset, $result[$clusterId]);
            foreach ($_data as $key => $value) {
                $currentWcss += pow(DataProcessing::cosineDistance($value, $clusterData), 2);
            }
        }

        if ($prevWcss != null) {
            $wcssChanges[$k] = abs(($currentWcss - $prevWcss) / $prevWcss);
        }

        $prevWcss = $currentWcss;

        if ($k == 1 && $currentWcss == 0) {
            $finalK = 1;
            break;
        }

        if ($k == 1) {
            continue;
        }

        // Elbow point
        if ((count($wcssChanges) > 1 && $wcssChanges[$k - 1] > $wcssChanges[$k]) || $wcssChanges[$k] == 1) {
            $finalK = $k - 1;
            break;
        }
    }

    $labels = [];

    // Assign `group_id` from the result of clustering to the users
    foreach ($finalResult[$finalK] as $id => $clusterData) {
        foreach ($clusterData as $userId => $inCluster) {
            if ($inCluster == 1) {
                $labels[$userId] = $id;
                User::find($userId)->update(['group_id' => $id]);
            }
        }
    }

    ksort($labels);

    dump(calculateSilhouetteScore($arrDataset, $labels));
});

// function silhouetteScore($data, $labels, $distance_matrix)
// {
//     dump($distance_matrix);
//     $num_samples = count($data);
//     $silhouette_scores = [];

//     for ($i = 1; $i <= $num_samples; $i++) {
//         $a = 0;
//         $b = INF; // Initialize b to infinity

//         // Calculate average distance of point i to points in the same cluster (a)
//         foreach ($labels as $j => $label) {
//             if ($label == $labels[$i] && $i != $j) {
//                 $a += $distance_matrix[$i][$j];
//             }
//         }
//         $a /= max(1, array_count_values($labels)[$labels[$i]] - 1);

//         // Calculate average distance of point i to points in the nearest cluster (b)
//         foreach ($labels as $j => $label) {
//             if ($label != $labels[$i]) {
//                 $b = min($b, $distance_matrix[$i][$j]);
//             }
//         }

//         $silhouette_scores[$i] = ($b - $a) / max($a, $b);
//     }

//     $silhouette_score = array_sum($silhouette_scores) / $num_samples;
//     return $silhouette_score;
// }



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
