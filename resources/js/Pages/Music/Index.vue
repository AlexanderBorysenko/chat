<template>
	<AppWrapper>
		<div class="p-16" style="overflow: auto">
			<MusicCard
				v-for="music in props.music"
				:key="music.id"
				:music="music"
				class="mb-16"
			/>
		</div>
	</AppWrapper>
</template>

<script setup lang="ts">
import MusicCard from '@/Components/MusicCard.vue';
import AppWrapper from '@/Layouts/AppWrapper.vue';
const props = defineProps<{
	music: TMusic[];
}>();
console.log(props.music);
</script>

<style scoped></style>
