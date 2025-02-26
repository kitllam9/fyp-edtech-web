<?php

namespace App;

use App\Models\User;
use Illuminate\Support\Str;
use NlpTools\FeatureFactories\DataAsFeatures;
use NlpTools\Tokenizers\WhitespaceTokenizer;
use NlpTools\Documents\TokensDocument;
use NlpTools\Documents\TrainingSet;
use NlpTools\Models\Lda;

use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;

use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Kernels\Distance\Cosine;
use Rubix\ML\Clusterers\Seeders\PlusPlus;
use Rubix\ML\CrossValidation\Reports\ContingencyTable;

use StopWords\StopWords;

class DataProcessing
{
    public static function topicModeling(string $text)
    {
        $stopwords = new StopWords('en');
        $str = $stopwords->clean($text);

        $tok = new WhitespaceTokenizer();
        $train = new TrainingSet();
        $train->addDocument(
            '', // the class is not used by the lda model
            new TokensDocument(
                $tok->tokenize(
                    $str
                )
            )
        );
        $lda = new Lda(
            new DataAsFeatures(), // a feature factory to transform the document data
            5, // the number of topics we want
            1.2, // the dirichlet prior assumed for the per document topic distribution
            1.2  // the dirichlet prior assumed for the per word topic distribution
        );

        // run the sampler 100 times
        $lda->train($train, 100);

        $result = $lda->getPhi(10);

        $joinedArr = [];
        foreach ($result as $words) {
            $joinedArr = array_merge($joinedArr, $words);
        }
        $joinedArr = array_unique($joinedArr);

        arsort($joinedArr);

        $topWords = array_keys(array_slice($joinedArr, 0, 5, true)); // Extract only the top 5 words

        $topWords = array_filter($topWords, function ($value) {
            return strlen($value) > 1;
        });

        $topWords =  array_map(function ($str) {
            return Str::title($str);
        }, $topWords);

        return $topWords;
    }

    public static function userClustering()
    {
        // Get the interests as string
        $samples = User::select('id', 'interests')->get()->map(function ($item) {
            return [$item['id'] => json_encode($item['interests'])];
        })->reject(function ($item) {
            return reset($item) === 'null';
        })->toArray();

        if (count($samples) <= 1) {
            return;
        }


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
                    // User::find($userId)->update(['group_id' => $id]);
                }
            }
        }

        ksort($labels);
        dd(calculateSilhouetteScore($arrDataset, $labels));
    }


    public static function tfidfTest()
    {
        $faker = \Faker\Factory::create();

        // Number of fake records to generate
        $numRecords = [1, 250, 500, 750, 1000];
        $executionTime = [];

        foreach ($numRecords as $num) {
            $samples = [];
            for ($i = 0; $i < $num; $i++) {
                $samples[] = $faker->text();
            }

            $startTime = microtime(true);

            $vectorizer = new TokenCountVectorizer(new WordTokenizer());
            $vectorizer->fit($samples);
            $vectorizer->transform($samples);

            $tfidf = array_values($samples);
            $transformer = new TfIdfTransformer($tfidf);
            $transformer->transform($tfidf);

            $endTime = microtime(true);
            $executionTime[] = $endTime - $startTime;
        }



        dd(array_combine($numRecords, $executionTime));


        return view('test.test', [
            'inputData' => json_encode($numRecords),
            'timeData' => json_encode($executionTime),
        ]);
    }

    public static function cosineDistance(&$A, &$B)
    {
        $v1 = &$A;
        $v2 = &$B;
        $prod = 0.0;
        $v1_norm = 0.0;
        foreach ($v1 as $i => $xi) {
            if (isset($v2[$i])) {
                $prod += $xi * $v2[$i];
            }
            $v1_norm += $xi * $xi;
        }
        $v1_norm = sqrt($v1_norm);

        $v2_norm = 0.0;
        foreach ($v2 as $i => $xi) {
            $v2_norm += $xi * $xi;
        }
        $v2_norm = sqrt($v2_norm);

        return 1 - ($prod / ($v1_norm * $v2_norm));
    }
}
