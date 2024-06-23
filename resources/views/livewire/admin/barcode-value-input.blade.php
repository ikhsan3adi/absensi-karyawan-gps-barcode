<div class="flex items-start gap-3">
  <div class="w-full">
    <x-input name="value" id="value" class="mt-1 block w-full" type="text" placeholder="Kode Barcode"
      wire:model="value" />
    @error('value')
      <x-input-error for="value" class="mt-2" message="{{ $message }}" />
    @enderror
  </div>
  <x-button type="button" wire:click="generate" class="mt-2">{{ __('Generate') }}</x-button>
</div>
