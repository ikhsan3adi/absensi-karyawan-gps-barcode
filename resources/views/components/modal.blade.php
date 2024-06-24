@props(['id', 'maxWidth', 'onclose' => null])

@php
  $id = $id ?? md5($attributes->wire('model'));

  $maxWidth = [
      'sm' => 'sm:max-w-sm',
      'md' => 'sm:max-w-md',
      'lg' => 'sm:max-w-lg',
      'xl' => 'sm:max-w-xl',
      '2xl' => 'sm:max-w-2xl',
  ][$maxWidth ?? '2xl'];
@endphp

<div x-data="{ show: @entangle($attributes->wire('model')) }" x-on:close.stop="show = false; {{ $onclose }}"
  x-on:keydown.escape.window="show = false; {{ $onclose }}" x-show="show" id="{{ $id }}"
  class="jetstream-modal fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0" style="display: none;">
  <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false; {{ $onclose }}"
    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900"></div>
  </div>

  <div x-show="show"
    class="{{ $maxWidth }} mb-6 transform overflow-hidden rounded-lg bg-white shadow-xl transition-all dark:bg-gray-800 sm:mx-auto sm:w-full"
    x-trap.inert.noscroll="show" x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
    {{ $slot }}
  </div>
</div>
