<template>
	<AppWrapper>
		<div class="p-24">
			<input
				class="form-control w-100 mb-16"
				type="text"
				v-model="form.name"
				placeholder="Name"
			/>
			<div>{{ form.errors.name }}</div>
			<textarea
				class="form-control w-100 mb-16"
				v-model="form.description"
				placeholder="Description"
			></textarea>
			<div>{{ form.errors.description }}</div>
			<input
				class="form-control w-100 mb-16"
				type="file"
				@change="onFileChange"
				accept="audio/*"
			/>
			<div>{{ form.errors.file }}</div>
			<button class="btn" @click="submit">Submit</button>
		</div>
	</AppWrapper>
</template>

<script setup lang="ts">
import AppWrapper from '@/Layouts/AppWrapper.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm<{
	name: string;
	description: string;
	file: File | null;
}>({
	name: '',
	description: '',
	file: null
});

const onFileChange = (event: Event) => {
	const target = event.target as HTMLInputElement;
	const file: File = (target.files as FileList)[0];
	form.file = file;
};

const submit = () => {
	form.post(route('music.store'), {
		onFinish: () => {
			form.reset();
		}
	});
};
</script>

<style scoped></style>
