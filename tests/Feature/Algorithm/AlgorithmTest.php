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

test('lda test', function () {
    $stopwords = new StopWords('en');
    $str = $stopwords->clean(
        "
            Art and humanities have shared a deep-rooted connection throughout history, reflecting the
            essence of culture, emotions, and societal values. From ancient cave paintings to modern
            digital creations, art serves as a powerful medium of expression, communication, and
            introspection.
            Artists, whether painters, sculptors, musicians, writers, or performers, channel their creativity
            to convey thoughts, emotions, and messages that resonate with individuals and communities.
            Through their work, artists capture the beauty, struggles, and complexities of the human
            experience, fostering empathy and understanding across diverse perspectives.
            Art has the remarkable ability to transcend language barriers, geographical boundaries, and
            time, serving as a universal language that speaks to the depths of the human soul. It has the
            power to evoke a wide range of emotions, from joy and inspiration to sadness and
            introspection, prompting viewers to contemplate their own beliefs, values, and experiences.
            In addition to its emotional impact, art plays a crucial role in shaping cultural identities,
            challenging societal norms, and sparking conversations about important issues. Whether it's a
            thought-provoking painting, a poignant piece of music, or a captivating dance performance,
            art has the capacity to provoke change, foster dialogue, and inspire individuals to see the
            world through a different lens.
            As technology continues to evolve, new forms of artistic expression emerge, blurring the lines
            between traditional and contemporary art. Digital art, virtual reality experiences, interactive
            installations, and multimedia collaborations redefine how artists engage with audiences and
            explore innovative ways to communicate their narratives.
            Art and humanities are intertwined in a complex and profound relationship, with art serving as
            a mirror that reflects the diversity, creativity, and resilience of the human spirit. Through art,
            individuals can find solace, inspiration, and connection, forging bonds that transcend cultural
            boundaries and unite people in a shared appreciation for the beauty and complexity of the
            world.
            "
    );

    $tok = new WhitespaceTokenizer();

    $d = new TokensDocument(
        $tok->tokenize(
            $str,
        )
    );

    $_train = new TrainingSet();
    $_train->addDocument(
        '',
        $d

    );

    // Assuming you have already defined $train as your existing TrainingSet
    $documents = $d->getDocumentData(); // Get all documents from the existing training set

    // Shuffle the documents to randomize the order
    shuffle($documents);

    // Determine the size of the held-out set (e.g., 20% of the data)
    $heldOutSize = 0.2 * count($documents);

    // Extract the held-out set from the existing training set
    $heldOutDocuments = array_slice($documents, 0, $heldOutSize);

    // Create a new TrainingSet for the held-out documents
    $heldOutSet = new TrainingSet();
    foreach ($heldOutDocuments as $doc) {
        $heldOutSet->addDocument('', new TokensDocument(
            $tok->tokenize(
                $doc,
            )
        ));
        unset($documents[$doc]);
    }

    // Remove the held-out documents from the original training set
    $train = new TrainingSet();
    foreach ($documents as $doc) {
        $train->addDocument(
            '', // the class is not used by the lda model
            new TokensDocument(
                $tok->tokenize(
                    $doc,
                )
            )
        );
    }

    $lda = new Lda(
        new DataAsFeatures(), // a feature factory to transform the document data
        5, // the number of topics we want
        1.2, // the dirichlet prior assumed for the per document topic distribution
        1.2  // the dirichlet prior assumed for the per word topic distribution
    );

    // Define the number of folds for cross-validation
    $numFolds = 5;
    $perFoldResults = [];

    // Perform K-fold cross-validation
    for ($fold = 0; $fold < $numFolds; $fold++) {
        // Split your dataset into training and validation sets for this fold

        // Train the LDA model on the training set
        $lda->train($train, 100); // You may need to adjust the number of iterations

        $evaluationResult = calculatePerplexity($lda, $heldOutSet);

        // Store the evaluation results for this fold
        $perFoldResults[] = $evaluationResult;
    }

    dump($perFoldResults);
});

function calculatePerplexity($ldaModel, $heldOutSet)
{
    $logLikelihood = 0.0;
    $numTokens = 0;

    $ldaModel->train($heldOutSet, 100);
    $logLikelihood = $ldaModel->getWordsPerTopicsProbabilities();
    foreach ($heldOutSet as $document) {
        $numTokens += count($document->getDocumentData());
    }

    dump($logLikelihood);
    return 0;
}
