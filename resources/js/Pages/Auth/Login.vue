<script setup lang="ts">
import AppWrapper from '@/Layouts/AppWrapper.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{}>();

const form = useForm({
	name: '',
	password: '',
	remember: false
});

const submit = () => {
	form.post(route('login'), {
		onFinish: () => {
			form.reset('password');
		}
	});
};
</script>

<template>
	<Head title="Log in" />
	<div class="flex justify-center align-center h-100vh">
		<form @submit.prevent="submit" class="form ui-panel p-24">
			<div class="mb-16">
				<input
					class="form-control w-100"
					v-model="form.name"
					required
					autofocus
				/>
			</div>
			<div class="mb-16">
				<input
					type="password"
					class="form-control w-100"
					v-model="form.password"
					required
				/>
			</div>

			<div class="flex items-center justify-end mt-4">
				<button class="btn w-100" :disabled="form.processing">
					Log in
				</button>
			</div>
		</form>
	</div>
</template>

<style scoped lang="scss"></style>
