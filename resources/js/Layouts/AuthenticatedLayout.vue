<script setup>
// Import reactive core from Vue
import { ref } from 'vue';
// Import custom layout elements and components
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
// Import Inertia routing link component
import { Link } from '@inertiajs/vue3';

// State to track mobile menu dropdown visibility
const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <!-- Main viewport wrapper with global dark background and text styling -->
        <div class="min-h-screen bg-backgroud text-sup-white">
            
            <!-- Header navigation bar panel with subtle bottom boundary line -->
            <nav class="border-b border-backgroud-button bg-backgroud-panel">
                
                <!-- Content boundary container with responsive horizontal padding -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    
                    <!-- Row structure separating the branding area from profile controls -->
                    <div class="flex h-16 justify-between">
                        
                        <!-- Left flex container for logo and main desktop navigation links -->
                        <div class="flex">
                            
                            <!-- Core logo container aligned vertically -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('home')">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-sup-white" />
                                </Link>
                            </div>

                            <!-- Desktop layout menu navigation links (hidden on smaller displays) -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex text-white">
                                <NavLink
                                    :href="route('home')"
                                    :active="route().current('home')"
                                >
                                    Home
                                </NavLink>
                            </div>
                        </div>

                        <!-- Right flex container holding desktop user settings dropdown dropdown -->
                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            
                            <!-- Positioning element for profile action menu -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    
                                    <!-- Clickable area that triggers option box open/close actions -->
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-backgroud-button px-3 py-2 text-sup-white font-medium leading-4 transition duration-150 ease-in-out hover:text-white focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <!-- Inline vector arrow icon for button decoration -->
                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4 text-sup-white"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <!-- Rendered box containing user profile interaction links -->
                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Mobile viewport menu trigger button (hidden on desktop screens) -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-white-400 transition duration-150 ease-in-out hover:bg-backgroud-button hover:text-sup-white focus:bg-backgroud-button focus:text-sup-white focus:outline-none"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <!-- Three-bar hamburger menu vector graphic segment -->
                                    <path
                                        :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <!-- Close 'X' symbol vector graphic segment -->
                                    <path
                                        :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Toggled mobile menu element drawer container -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden bg-backgroud-panel border-t border-backgroud-button">
                    
                    <!-- Main layout route items list for touchscreen viewports -->
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('home')" :active="route().current('home')">
                            Home
                        </ResponsiveNavLink>
                    </div>

                    <!-- Lower settings area block for user credentials display -->
                    <div class="border-t border-backgroud-button pb-1 pt-4">
                        
                        <!-- Text wrapper block showing user name and email values -->
                        <div class="px-4">
                            <div class="text-sup-white font-medium">{{ $page.props.auth.user.name }}</div>
                            <div class="text-white-400 font-medium text-sm">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <!-- Core mobile control button actions links stack -->
                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Optional secondary layout header container row for specific section page titles -->
            <header class="bg-backgroud-panel border-b border-backgroud-button shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 text-sup-white">
                    <slot name="header" />
                </div>
            </header>

            <!-- Core dynamic view section where inner template page data injects -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>