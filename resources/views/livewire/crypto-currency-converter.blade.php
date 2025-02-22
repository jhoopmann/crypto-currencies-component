<form>

    <div class="row">
        <div class="col-lg-5 col-md-5 col-sm-12 mt-2">

            <div class="input-group">
                <select class="form-select"
                        wire:model.live="cryptoCurrencyId"
                        wire:loading.attr="disabled"
                        wire:target="{{$formTargets}}"
                >
                    @foreach($this->cryptoCurrencies as $c)
                        <option value="{{$c['id']}}"
                                @if($c['id'] === $this->cryptoCurrencyId) selected="selected" @endif
                        >
                            {{$c['name']}}
                        </option>
                    @endforeach
                </select>

                <input type="number" class="form-control" min="0"
                       wire:model.live.debounce.500ms="cryptoCurrencyAmount"
                       wire:loading.attr="disabled"
                       wire:target="{{$formTargets}}"
                />

            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-12 mt-2">
            <div class="input-group">
                <select class="form-select"
                        wire:model.live="fiatCurrencyId"
                        wire:loading.attr="disabled"
                        wire:target="{{$formTargets}}"
                >
                    @foreach($this->fiatCurrencies as $fId)
                        <option value="{{$fId}}"
                                @if($fId === $this->fiatCurrencyId) selected="selected" @endif
                        >
                            {{$fId}}
                        </option>
                    @endforeach
                </select>

                <input type="number" class="form-control" min="0"
                       wire:model.live.debounce.500ms="fiatCurrencyAmount"
                       wire:loading.attr="disabled"
                       wire:target="{{$formTargets}}"
                />
            </div>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-12 mt-2 d-flex">
            <div class="form-check mt-auto mb-auto">
                <input type="checkbox" class="form-check-input" id="liveDataInput"
                       wire:model.live="liveData"
                       wire:loading.attr="disabled"
                       wire:target="{{$formTargets}}"
                />
                <label class="form-check-label" for="liveDataInput">live</label>
            </div>
        </div>

        @error (self::class)
        <div class="col-lg-12">
            <div class="alert alert-danger mt-4">
                {{ $message}}
            </div>
        </div>
        @enderror

        <style>
            .form-select {
                max-width: fit-content;
            }
        </style>
    </div>
</form>

