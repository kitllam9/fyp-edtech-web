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

use Phpml\Classification\SVC;
use Phpml\Dataset\ArrayDataset;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\CrossValidation\RandomSplit;
use Phpml\Regression\LeastSquares;
use Phpml\SupportVectorMachine\Kernel;
use Phpml\ModelManager;

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

        $samples = array_combine($keys, $tfidf);
        // Clustering according to the vectorized string
        $kmeans = new KMeans(2);
        $clusters = $kmeans->cluster($samples);

        // Assign `group_id` from the result of clustering to the users
        foreach ($clusters as $id => $cluster) {
            foreach ($cluster as $userId => $data) {
                User::find($userId)->update(['group_id' => $id]);
            }
        }
    }

    // public static function train()
    // {
    //     // Load the processed dataset from the JSON file
    //     $datasetFile = storage_path('app/public/processed_dataset.json');
    //     $rawDataset = json_decode(file_get_contents($datasetFile), true);

    //     $samples = [];

    //     shuffle($rawDataset);  // reorder the array. 
    //     $random = array_slice($rawDataset, 0, 2000); // cut off after $count elements.

    //     foreach ($random as $data) {
    //         for ($i = 0; $i < count($data['answers']); $i++) {
    //             $text = $data['question'];
    //             $samples[] = [$text];
    //             $labels[] =  $data['answers'][$i];
    //         }
    //     }

    //     $dataset = new ArrayDataset($samples, $labels);

    //     $split = new StratifiedRandomSplit($dataset, 0.2);
    //     // $samples = $split->getTrainSamples();

    //     // Train the DecisionTree classifier
    //     $classifier = new SVC(
    //         probabilityEstimates: true,
    //     );
    //     $classifier->train($split->getTrainSamples(), $split->getTrainLabels());

    //     $modelManager = new ModelManager();
    //     $modelManager->saveToFile($classifier, storage_path('app/public/question-answer-model'));
    // }

    // public static function predict()
    // {
    //     $modelManager = new ModelManager();
    //     $model = $modelManager->restoreFromFile(storage_path('app/public/question-answer-model'));
    //     dd($model->predict(['What is the Grotto at Notre Dame?']));
    // }
}
