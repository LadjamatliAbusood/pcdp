<script setup>
document.title = "FOIX PCDP - Login";

import { ref } from "vue";
import axios from "axios";
import { useForm } from "@inertiajs/vue3";
import Quotes from "@/constant/Quotes";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import PasswordField from "@/Components/PasswordField.vue";
import TextInputField from "@/Components/TextInputField.vue";
import { Inertia } from "@inertiajs/inertia";
import InputText from "primevue/inputtext";

defineOptions({ layout: null });

const generalError = ref("");
const loading = ref(false);
const quote = Quotes[Math.floor(Math.random() * Quotes.length)];

const form = useForm({
    email: null,
    password: null,
});

const submit = () => {
    form.post("/login", {
        onError: () => {},
        onSuccess: () => {},
    });
};
</script>

<template>
    <main class="flex flex-col md:flex-row h-screen w-screen">
        <!-- Login Form -->
        <section
            class="flex flex-col justify-center items-center w-full md:w-1/2 bg-white px-6 md:px-16 relative"
        >
            <div class="absolute top-4 left-4 md:top-6 md:left-6">
                <!-- <img
                    src="/storage/imgs/dswd_bagong_pilipinas_logo.svg"
                    class="h-10 md:h-14"
                /> -->
            </div>

            <div class="flex flex-col items-center mt-20 md:mt-10 w-full">
                <h1
                    class="text-2xl md:text-3xl font-bold text-center mb-6 text-gray-800"
                >
                    Welcome to FO IX's<br />PCDP Profiling System
                </h1>

                <form @submit.prevent="submit" class="w-full max-w-sm">
                    <TextInputField
                        name="Email"
                        type="email"
                        v-model="form.email"
                        :message="form.errors.email"
                    />

                    <PasswordField
                        name="Password"
                        v-model="form.password"
                        :message="form.errors.password"
                    />

                    <div class="flex justify-between mb-5 text-sm mt-2">
                        <label class="flex items-center space-x-2">
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="rounded border-gray-300"
                            />
                            <span>Remember Me</span>
                        </label>
                    </div>

                    <PrimaryButton
                        :label="loading ? 'Logging in...' : 'Login'"
                        :loading="loading"
                        class="mt-2 w-full"
                    />
                </form>
            </div>
        </section>

        <!-- Right Image/Quote -->
        <section
            class="hidden md:flex items-center justify-center w-1/2 h-full bg-white p-5"
        >
            <div
                class="relative w-full h-[80%] rounded-2xl overflow-hidden shadow-lg"
            >
                <!-- <img
                    src="/public/storage/imgs/bg-dswd.jpg"
                    class="object-cover w-full h-full brightness-50"
                /> -->
                <div class="absolute bottom-10 left-10 text-white">
                    <p class="italic text-2xl">{{ quote.qoute }}</p>
                    <p class="text-lg mt-2">– {{ quote.author }}</p>
                </div>
            </div>
        </section>
    </main>
</template>
