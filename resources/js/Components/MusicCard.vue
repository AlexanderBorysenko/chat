<template>
	<div class="music-card">
		<!-- music card (title, expand description, music player(use music.path), if music.user_id === auth.user.id add route('music.delete') button) -->
		<h3 class="title" v-if="music.name">{{ music.name }}</h3>
		<p class="description" v-if="music.description">
			{{ music.description }}
		</p>
		<div class="flex align-center gap-1">
			<audio class="music-player" controls>
				<source :src="music.url" type="audio/mp3" />
			</audio>
			<button
				class="btn"
				v-if="music.user_id === auth.user.id"
				@click="onDelete"
			>
				<IoSharpTrashBin class="fs-2" />
			</button>
		</div>
	</div>
</template>

<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { IoSharpTrashBin } from '@kalimahapps/vue-icons';

const { auth } = usePage().props;
const props = defineProps<{
	music: TMusic;
}>();
const onDelete = () => {
	if (confirm('Are you sure?')) {
		router.delete(route('music.delete', props.music.id));
	}
};
</script>

<style scoped lang="scss">
.music-card {
	background: #fff;
	border-radius: 4px;
	padding: 8px 16px;
}
.title {
	font-size: 1.5rem;
	margin-bottom: 8px;
}
.description {
	margin-bottom: 8px;
}
.music-player {
	flex: 1;
	height: 100%;
}
</style>
