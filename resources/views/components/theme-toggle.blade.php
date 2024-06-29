<div @keydown.window.tab="toggle" {{ $attributes->merge(['class' => 'flex items-center justify-center space-x-2']) }}>
  <input id="theme-switcher" type="checkbox" name="switch" class="hidden" :checked="$store.darkMode.on">

  <button type="button" @click="$store.darkMode.toggle()"
    class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:outline-none dark:hover:bg-gray-700 dark:hover:text-gray-300">
    <x-heroicon-o-moon class="h-5 w-5" x-show="$store.darkMode.on" />
    <x-heroicon-o-sun class="h-5 w-5" x-show="!$store.darkMode.on" />
  </button>
</div>
