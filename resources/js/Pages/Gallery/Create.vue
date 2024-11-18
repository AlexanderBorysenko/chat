<template>
	<AppWrapper>
		<div class="p-24">
			<input
				class="form-control w-100 mb-16"
				type="file"
				@change="onFileChange"
				accept="image/*"
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
	file: File | null;
}>({
	file: null
});

const onFileChange = (event: Event) => {
	const target = event.target as HTMLInputElement;
	const file: File = (target.files as FileList)[0];
	form.file = file;
};

const submit = () => {
	form.post(route('gallery.store'), {
		onFinish: () => {
			form.reset();
		}
	});
};
</script>

<style scoped></style>
