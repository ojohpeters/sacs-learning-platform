@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-300 px-3.5 py-2.5 text-gray-900 shadow-sm placeholder-gray-400 focus:border-accent focus:ring-1 focus:ring-accent transition']) }}>
