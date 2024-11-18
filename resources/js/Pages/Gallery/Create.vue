<template>
	<AppWrapper>
		<div class="p-24">
			<input
				class="form-control w-100 mb-16"
				type="file"
				@change="onFileChange"
				accept="image/*"
				multiple
			/>
			<div>{{ form.errors.files }}</div>
			<button class="btn" @click="submit">Submit</button>
		</div>
	</AppWrapper>
</template>

<script setup lang="ts">
import AppWrapper from '@/Layouts/AppWrapper.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm<{
	files: File[];
}>({
	files: []
});

const onFileChange = (event: Event) => {
	const target = event.target as HTMLInputElement;
	const files: FileList = target.files as FileList;
	form.files = Array.from(files);
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
