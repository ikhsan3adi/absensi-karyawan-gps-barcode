@props(['type' => 'info'])
@php
  $types = [
      'info' => 'bg-blue-200 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
      'success' => 'bg-green-200 text-green-800 dark:bg-green-800 dark:text-green-100',
      'warning' => 'bg-yellow-200 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
      'danger' => 'bg-red-200 text-red-800 dark:bg-red-800 dark:text-red-100',
      'disabled' => 'bg-gray-200 text-gray-800 dark:bg-slate-700 dark:text-gray-100',
  ];
@endphp
<span
  {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-gray-100 text-gray-800 ' . $types[$type]]) }}>
  {{ $slot }}
</span>
