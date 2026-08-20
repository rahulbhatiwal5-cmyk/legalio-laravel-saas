<div>
    @foreach ( $attachedPrompts as $attached_prompt)
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="row top-info-div mb-3">
                <div class="col-md-12">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for=""> <b>Resouce ID: </b> </label>
                            <p> {{ $attached_prompt->resource_id}}</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <label for=""> <b>Prompt Name: </b> </label>
                            <p> {{ $attached_prompt->prompt->name ?? "Not found" }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="form-group" >
                <div class="d-flex justify-content-between align-items-start">
                    <div class="">
                        <img src="{{ asset($attached_prompt->frontend_img_path) }}" alt="Document"
                            style="width: 100%; display: block; height:250px ">
                    </div>
                    <div class="" style="flex: 0 0 50%; padding:0px 10px;">
                        <label for=""><b>Select Prompt</b></label>
                        <select class="form-control mt-2 prompt-select"
                            wire:model="selectedPrompts.{{ $attached_prompt->id }}"
                            wire:change="savePromptSelection({{ $attached_prompt->resource_id }}, $event.target.value)">
                            <option value="" disabled>Select Prompt</option>
                            @foreach ($prompts as $prompt)
                            <option value="{{ $prompt->id }}" {{ $attached_prompt->prompt_id == $prompt->id ?
                                'selected' : '' }} >{{ $prompt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endforeach
</div>
