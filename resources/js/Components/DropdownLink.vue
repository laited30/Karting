<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-sup-white bg-backgroud-panel transition duration-150 ease-in-out hover:bg-backgroud-button focus:bg-backgroud-button focus:outline-none"
    >
        <slot />
    </Link>
</template>