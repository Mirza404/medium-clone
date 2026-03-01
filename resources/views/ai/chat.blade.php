<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            LangChain Agent Playground
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form id="ai-chat-form" class="space-y-4">
                    @csrf
                    <div>
                        <label for="prompt" class="block text-sm font-medium text-gray-700">
                            Prompt
                        </label>
                        <textarea
                            id="prompt"
                            name="prompt"
                            rows="5"
                            required
                            class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="e.g. Summarize the latest article I wrote about AI tooling."
                        ></textarea>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button type="submit" id="ai-submit-button">
                            Send Prompt
                        </x-primary-button>
                        <p id="ai-status" class="text-sm text-gray-500"></p>
                    </div>

                    <p id="ai-error" class="text-sm text-red-600"></p>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Latest Response</h3>
                    <button
                        id="ai-clear"
                        type="button"
                        class="text-sm text-indigo-600 hover:underline"
                    >
                        Clear
                    </button>
                </div>
                <pre
                    id="ai-response"
                    class="mt-4 whitespace-pre-wrap text-sm text-gray-800 min-h-[6rem]"
                >Awaiting prompt...</pre>
                <dl id="ai-usage" class="mt-4 text-xs text-gray-500 space-y-1 hidden">
                    <div>
                        <dt class="font-semibold inline">Input Tokens:</dt>
                        <dd id="ai-input-tokens" class="inline"></dd>
                    </div>
                    <div>
                        <dt class="font-semibold inline">Output Tokens:</dt>
                        <dd id="ai-output-tokens" class="inline"></dd>
                    </div>
                    <div>
                        <dt class="font-semibold inline">Latency:</dt>
                        <dd id="ai-latency" class="inline"></dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('ai-chat-form');
            const promptField = document.getElementById('prompt');
            const statusEl = document.getElementById('ai-status');
            const errorEl = document.getElementById('ai-error');
            const responseEl = document.getElementById('ai-response');
            const submitBtn = document.getElementById('ai-submit-button');
            const clearBtn = document.getElementById('ai-clear');
            const usageWrapper = document.getElementById('ai-usage');
            const inputTokensEl = document.getElementById('ai-input-tokens');
            const outputTokensEl = document.getElementById('ai-output-tokens');
            const latencyEl = document.getElementById('ai-latency');
            const chatEndpoint = @json(route('ai.chat'));
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content');

            clearBtn.addEventListener('click', () => {
                responseEl.textContent = 'Awaiting prompt...';
                usageWrapper.classList.add('hidden');
                inputTokensEl.textContent = '';
                outputTokensEl.textContent = '';
                latencyEl.textContent = '';
                errorEl.textContent = '';
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const prompt = promptField.value.trim();

                if (!prompt) {
                    errorEl.textContent = 'Prompt is required.';
                    return;
                }

                errorEl.textContent = '';
                usageWrapper.classList.add('hidden');
                statusEl.textContent = 'Sending...';
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75');

                const started = performance.now();

                try {
                    const response = await fetch(chatEndpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrfToken ?? '',
                        },
                        body: JSON.stringify({ prompt }),
                    });

                    const payload = await response.json().catch(() => null);

                    if (!response.ok || !payload) {
                        const message = payload?.error ?? 'Unexpected error. Check logs.';
                        throw new Error(message);
                    }

                    responseEl.textContent = payload.response ?? JSON.stringify(payload, null, 2);
                    statusEl.textContent = 'Received response.';

                    if (payload.usage) {
                        usageWrapper.classList.remove('hidden');
                        inputTokensEl.textContent = payload.usage.promptTokens ?? 'n/a';
                        outputTokensEl.textContent = payload.usage.completionTokens ?? 'n/a';
                        const elapsedMs = Math.round(performance.now() - started);
                        latencyEl.textContent = `${elapsedMs} ms`;
                    }
                } catch (error) {
                    console.error(error);
                    errorEl.textContent = error.message;
                    statusEl.textContent = '';
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75');
                }
            });
        });
    </script>
</x-app-layout>
