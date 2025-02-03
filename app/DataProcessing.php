<?php

namespace App;

use App\Models\User;
use Illuminate\Support\Str;

use NlpTools\FeatureFactories\DataAsFeatures;
use NlpTools\Tokenizers\WhitespaceTokenizer;
use NlpTools\Documents\TokensDocument;
use NlpTools\Documents\TrainingSet;
use NlpTools\Models\Lda;

// use Phpml\Clustering\KMeans;
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
            1, // the dirichlet prior assumed for the per document topic distribution
            1  // the dirichlet prior assumed for the per word topic distribution
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

        // Build the dictionary.
        $vectorizer->fit($samples);

        // Transform the provided text samples into a vectorized list.
        $vectorizer->transform($samples);

        $tfidf = array_values($samples);
        $transformer = new TfIdfTransformer($tfidf);
        $transformer->transform($tfidf);
        $dataset = new Labeled($tfidf, $keys);


        $maxClusters = 10;
        $prevWcss = null;
        $wcssChanges = [];

        for ($k = 1; $k <= $maxClusters; $k++) {
            $estimator = new KMeans($k, kernel: new Cosine(), seeder: new PlusPlus());
            $estimator->train($dataset);

            $currentWcss = 0;

            foreach ($estimator->centroids() as $clusterId => $clusterData) {
                // Normalize centroid data
                $centroid = array_sum($clusterData) / count($clusterData);

                foreach ($clusterData as $sample) {
                    $currentWcss += pow($sample - $centroid, 2);
                }
            }

            if ($prevWcss != null) {
                $wcssChanges[] = $currentWcss - $prevWcss;
            }

            $prevWcss = $currentWcss;

            if (count($wcssChanges) > 1 && end($wcssChanges) < array_slice($wcssChanges, -2, 1)) {
                $report = new ContingencyTable();
                $result = $report->generate($estimator->predict($dataset), $keys);

                // Assign `group_id` from the result of clustering to the users
                foreach ($result as $id => $clusterData) {
                    foreach ($clusterData as $userId => $inCluster) {
                        if ($inCluster == 1) {
                            User::find($userId)->update(['group_id' => $id]);
                        }
                    }
                }

                break;
            }
        }
    }
}
