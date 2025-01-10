<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComprehendController extends Controller
{
    public function analyzeSentiment(Request $request)
    {
        $text = $request->input('text');

        // Resolve the AWS Comprehend service
        $comprehend = app('AwsComprehend');

        // Call the AWS Comprehend API
        $response = $comprehend->detectSentiment([
            'Text' => $text,
            'LanguageCode' => 'en', // Specify language code
        ]);

        return response()->json($response);
    }
}
