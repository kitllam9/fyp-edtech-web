<?php

namespace App;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use NlpTools\FeatureFactories\DataAsFeatures;
use NlpTools\Tokenizers\WhitespaceTokenizer;
use NlpTools\Documents\TokensDocument;
use NlpTools\Documents\TrainingSet;
use NlpTools\Models\Lda;

use Phpml\Clustering\KMeans;
use Phpml\Clustering\DBSCAN;
use Phpml\Math\Distance\Minkowski;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;


use StopWords\StopWords;

use Pdo;

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
        $samples = User::select('id', 'interest')->get()->map(function ($item) {
            return [$item['id'] => json_encode($item['interest'])];
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

        // Build the dictionary.
        $vectorizer->fit($samples);

        // Transform the provided text samples into a vectorized list.
        $vectorizer->transform($samples);

        // Clustering according to the vectorized string
        $kmeans = new KMeans(2);
        $clusters = $kmeans->cluster($samples);

        // Assign `group_id` from the result of clustering to the users
        foreach ($clusters as $id => $cluster) {
            foreach ($cluster as $userId => $data) {
                User::find($userId)->update(['group_id' => $id]);
            }
        }

        dd($clusters);
    }
}
