<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TTSController extends Controller
{
    public function generate(Request $request)
    {
        $text = $request->input('text');

        if (empty($text)) {
            return response()->json(['error' => 'Text is required'], 400);
        }

        // Generate Google Translate TTS URL
        $encodedText = urlencode($text);
        $url = "https://translate.google.com/translate_tts?ie=UTF-8&tl=id&client=tw-ob&q={$encodedText}";

        try {
            // Fetch audio from Google Translate
            $audioContent = file_get_contents($url);

            if ($audioContent === false) {
                return response()->json(['error' => 'Failed to generate TTS'], 500);
            }

            // Return audio as MP3
            return response($audioContent, 200)
                ->header('Content-Type', 'audio/mpeg')
                ->header('Content-Disposition', 'inline; filename="tts.mp3"');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
