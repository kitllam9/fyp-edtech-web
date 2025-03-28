<?php

namespace App\Sidecar;

use Hammerstone\Sidecar\LambdaFunction;

class OgImage extends LambdaFunction
{
    public function handler()
    {
        // Define your handler function.
        // (Javascript file + export name.)
        return 'resources/lambda/image.handler';
    }

    public function package()
    {
        // All files and folders needed for the function.
        return [
            'resources/lambda',
        ];
    }
}
