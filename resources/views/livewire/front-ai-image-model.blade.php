
@php
    $aiModelRefs = \App\Models\Setting::where('type', 'ai')
    ->whereNotNull('model_ref')
    ->distinct()
    ->pluck('model_ref');

@endphp

<div x-data="{ open: @entangle('show'),
               title: @entangle('modalTitle'),
               pre_configured_prompt: @entangle('pre_configured_prompt') ,
               prompt_name: @entangle('prompt_name') ,
               loading: @entangle('loading'),
               ai_output: @entangle('ai_output')
              }
               " x-init="window.addEventListener('openAiImageModel',  (event) => {
         open = true;
         $wire.call('open', event.detail.title, event.detail.id, event.detail.document_id);
     })"

     >
    <div x-show="open" :class="{ 'show': open }" class="modal-backdrop fade"></div>
    <div x-show="open" :class="{ 'show': open }" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document" style="min-width:80%;">
            <div class="modal-content">
                <button type="button" class="close" @click="open = false" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </button>
                <div class="modal-header">
                    <h5 class="modal-title" x-text="title"> </h5>
                </div>
                <div class="modal-body" style="max-height:60vh; overflow-y:auto;">
                    <div class="row align-items-start">
                        <!-- AI Response Section -->
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="ai-model-select" class="form-label fw-bold">Select AI Model</label>
                                <select wire:model="selectedModel" class="form-select">
                                    <option value="chatgpt" selected>chatgpt</option>
                                    <!-- @foreach ($aiModelRefs as $ref)
                                        <option value="{{ $ref }}">{{ $ref }}</option>
                                    @endforeach -->
                                </select>
                            </div>

                            <template x-if="loading">
                                <div class="text-center p-4" x-cloak>
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!loading">
                                <div class="card ai-response-container ">
                                    <label class="ai-response-label">AI Response</label>
                                    <div class="ai-response-box p-3">
                                        <div class="ai-response-text" x-html="ai_output" id="ai-output">

                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Pre-configured Prompt Section -->
                        <div class="col-md-5">
                            <div class="card p-3">
                                <label for="pre-configured" class="form-label fw-bold">
                                    Pre-configured Prompt: <span x-text="prompt_name" class="text-primary"></span>
                                </label>
                                <textarea name="pre-configured" id="pre-configured" x-text="pre_configured_prompt" x-model="pre_configured_prompt"
                                    class="form-control p-3 shadow-sm rounded ai-preconfigured-prompt-textbox"
                                    rows="6" placeholder="Enter your prompt here...">
                                </textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light ">
                    <div>

                        <button type="button" @click="$wire.regenerateResponse()" :disabled="loading" class="btn btn-primary"><span x-text="loading ? 'Hold On' : 'Regenrate'"></span> </button>
                        <button type="button" onclick="saveGeneratedImage()" @click="open = false" class="btn btn-primary"> Confirm and Save </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    window.addEventListener('runAiImageGenerator', event => {
        @this.call('sendImagePrompt');
    });

    
</script>