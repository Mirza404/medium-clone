<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LangchainController extends Controller
{
    public function create(): View
    {
        return view('ai.chat');
    }

    public function chat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'prompt' => ['required', 'string', 'max:4000'],
        ]);

        $baseUrl = rtrim((string) config('services.langchain.base_url'), '/');
        if ($baseUrl === '') {
            return response()->json([
                'error' => 'LangChain service URL is not configured.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $http = Http::timeout((int) config('services.langchain.timeout', 15));

        if ($internalKey = config('services.langchain.key')) {
            $http = $http->withHeaders([
                'X-Internal-Key' => $internalKey,
            ]);
        }

        try {
            $serviceResponse = $http->acceptJson()
                ->post("{$baseUrl}/chat", Arr::only($data, ['prompt']));
        } catch (\Throwable $exception) {
            Log::error('LangChain service unreachable', [
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'error' => 'LangChain service is unavailable. Please try again later.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if ($serviceResponse->failed()) {
            Log::warning('LangChain service error', [
                'status' => $serviceResponse->status(),
                'body' => $serviceResponse->body(),
            ]);

            return response()->json([
                'error' => 'Unable to complete LangChain request.',
                'status' => $serviceResponse->status(),
                'details' => $serviceResponse->json() ?? $serviceResponse->body(),
            ], $serviceResponse->status() ?: Response::HTTP_BAD_GATEWAY);
        }

        return response()->json($serviceResponse->json());
    }
}
