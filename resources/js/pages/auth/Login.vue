<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Form, Head, Link } from '@inertiajs/vue3';
import { LoaderCircle, Film, Github, Gamepad2 } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    </Head>

    <!-- Background with liquid glass effect -->
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 relative overflow-hidden">
        <!-- Animated background blobs -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-1/2 -left-40 w-96 h-96 bg-cyan-500/15 rounded-full blur-3xl animate-pulse delay-1000"></div>
            <div class="absolute -bottom-40 right-1/3 w-72 h-72 bg-blue-600/25 rounded-full blur-3xl animate-pulse delay-2000"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 flex min-h-screen flex-col items-center justify-center gap-6 p-4 sm:p-6 md:p-10">
            <div class="w-full max-w-md">
                <div class="flex flex-col gap-8">
                    <!-- Logo and Header -->
                    <div class="flex flex-col items-center gap-6">
                        <Link :href="route('home')" class="flex flex-col items-center gap-3 font-medium">
                            <div class="relative">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center backdrop-blur-xl shadow-lg border border-white/20">
                                    <Film class="w-7 h-7 text-white" />
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl blur opacity-60"></div>
                            </div>
                            <span class="text-2xl font-bold text-white">Watcher</span>
                        </Link>
                        <div class="space-y-2 text-center">
                            <h1 class="text-2xl font-bold text-white">Welcome back</h1>
                            <p class="text-center text-sm text-blue-100/70">Sign in to your account to continue</p>
                        </div>
                    </div>

                    <!-- Glass Form Container -->
                    <div class="bg-gradient-to-br from-white/15 via-white/10 to-white/5 backdrop-blur-2xl rounded-3xl border border-white/20 p-6 sm:p-8">
                        <div v-if="status" class="mb-6 text-sm font-medium text-center text-green-400 bg-green-500/10 border border-green-500/20 rounded-xl p-3 backdrop-blur-sm">
                            {{ status }}
                        </div>

                        <!-- Social Login Buttons -->
                        <div class="grid gap-3 mb-6">
                            <a 
                                :href="route('social.redirect', 'discord')"
                                class="group relative flex items-center justify-center gap-3 px-4 py-3 bg-indigo-600/80 hover:bg-indigo-600 rounded-2xl transition-all duration-300 border border-indigo-500/30 hover:border-indigo-400/50 backdrop-blur-sm hover:scale-[1.02]"
                            >
                                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515a.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0a12.64 12.64 0 0 0-.617-1.25a.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057a19.9 19.9 0 0 0 5.993 3.03a.078.078 0 0 0 .084-.028a14.09 14.09 0 0 0 1.226-1.994a.076.076 0 0 0-.041-.106a13.107 13.107 0 0 1-1.872-.892a.077.077 0 0 1-.008-.128a10.2 10.2 0 0 0 .372-.292a.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127a12.299 12.299 0 0 1-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028a19.839 19.839 0 0 0 6.002-3.03a.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.956-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.955-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.946 2.418-2.157 2.418z"/>
                                </svg>
                                <span class="text-white font-medium">Continue with Discord</span>
                            </a>

                            <a 
                                :href="route('social.redirect', 'github')"
                                class="group relative flex items-center justify-center gap-3 px-4 py-3 bg-gray-800/80 hover:bg-gray-800 rounded-2xl transition-all duration-300 border border-gray-700/30 hover:border-gray-600/50 backdrop-blur-sm hover:scale-[1.02]"
                            >
                                <Github class="w-5 h-5 text-white" />
                                <span class="text-white font-medium">Continue with GitHub</span>
                            </a>

                            <a 
                                :href="route('social.redirect', 'steam')"
                                class="group relative flex items-center justify-center gap-3 px-4 py-3 bg-blue-700/80 hover:bg-blue-700 rounded-2xl transition-all duration-300 border border-blue-600/30 hover:border-blue-500/50 backdrop-blur-sm hover:scale-[1.02]"
                            >
                                <Gamepad2 class="w-5 h-5 text-white" />
                                <span class="text-white font-medium">Continue with Steam</span>
                            </a>
                        </div>

                        <!-- Divider -->
                        <div class="relative mb-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-white/20"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 text-white/70 bg-gradient-to-br from-white/15 via-white/10 to-white/5 backdrop-blur-sm rounded-full">Or continue with</span>
                            </div>
                        </div>

                        <!-- Email/Username Login Form -->
                        <Form method="post" :action="route('login')" :reset-on-success="['password']" v-slot="{ errors, processing }" class="flex flex-col gap-6">
                            <div class="grid gap-6">
                                <div class="grid gap-3">
                                    <Label for="email" class="text-white/90 font-medium">Email or Username</Label>
                                    <Input
                                        id="email"
                                        type="text"
                                        name="email"
                                        required
                                        autofocus
                                        :tabindex="1"
                                        autocomplete="email"
                                        placeholder="Enter your email or username"
                                        class="bg-white/10 border-white/20 text-white placeholder:text-white/50 focus:border-blue-400/50 focus:ring-blue-400/25 backdrop-blur-sm rounded-xl h-12"
                                    />
                                    <InputError :message="errors.email" />
                                </div>

                                <div class="grid gap-3">
                                    <div class="flex items-center justify-between">
                                        <Label for="password" class="text-white/90 font-medium">Password</Label>
                                        <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm text-blue-300 hover:text-blue-200" :tabindex="5">
                                            Forgot password?
                                        </TextLink>
                                    </div>
                                    <Input
                                        id="password"
                                        type="password"
                                        name="password"
                                        required
                                        :tabindex="2"
                                        autocomplete="current-password"
                                        placeholder="Enter your password"
                                        class="bg-white/10 border-white/20 text-white placeholder:text-white/50 focus:border-blue-400/50 focus:ring-blue-400/25 backdrop-blur-sm rounded-xl h-12"
                                    />
                                    <InputError :message="errors.password" />
                                </div>

                                <div class="flex items-center justify-between">
                                    <Label for="remember" class="flex items-center space-x-3 text-white/90">
                                        <Checkbox id="remember" name="remember" :tabindex="3" class="border-white/20 bg-white/10" />
                                        <span class="text-sm">Remember me</span>
                                    </Label>
                                </div>

                                <Button 
                                    type="submit" 
                                    class="w-full mt-4 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-semibold rounded-xl transition-all duration-300 border-0" 
                                    :tabindex="4" 
                                    :disabled="processing"
                                >
                                    <LoaderCircle v-if="processing" class="w-4 h-4 animate-spin mr-2" />
                                    {{ processing ? 'Signing in...' : 'Sign In' }}
                                </Button>
                            </div>
                        </Form>
                    </div>

                    <!-- Sign Up Link -->
                    <div class="text-sm text-center text-white/70">
                        Don't have an account?
                        <TextLink :href="route('register')" class="text-blue-300 hover:text-blue-200 font-medium ml-1" :tabindex="5">Create one here</TextLink>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
