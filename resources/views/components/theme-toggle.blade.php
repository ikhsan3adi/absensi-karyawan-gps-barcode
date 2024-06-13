<div x-data="window.themeSwitcher()" x-init="switchTheme()" @keydown.window.tab="switchOn = false"
  {{ $attributes->merge(['class' => 'flex items-center justify-center space-x-2']) }}>
  <input id="theme-switcher" type="checkbox" name="switch" class="hidden" :checked="switchOn">

  <button type="button" @click="switchOn = !switchOn; switchTheme()"
    class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:outline-none dark:hover:bg-gray-700 dark:hover:text-gray-300">
    <x-heroicon-o-moon x-show="switchOn" class="h-5 w-5" />
    <x-heroicon-o-sun x-show="!switchOn" class="h-5 w-5" />
  </button>
</div>
