<template>
	<div
		class="message"
		v-if="message.type === 'media'"
		:class="{ 'is-mine': isMine }"
	>
		<button
			class="btn ml-auto w-100 text-center flex align-center justify-center"
			@click="isContentVisible = !isContentVisible"
		>
			{{ isContentVisible ? 'Сховати' : 'Показати' }}
		</button>
		<div
			v-html="message.content"
			v-if="isContentVisible"
			class="lh-0"
		></div>
		<p class="date" v-if="!isMine">
			{{ new Date(message.created_at).toLocaleString() }}
		</p>
		<p class="date" v-if="isMine">
			{{ message.read ? 'Прочитано' : 'Не прочитано' }}
		</p>
	</div>
	<div class="message pv-8 ph-12" v-else :class="{ 'is-mine': isMine }">
		<div v-html="message.content"></div>
		<p class="date" v-if="!isMine">
			{{ new Date(message.created_at).toLocaleString() }}
		</p>
		<p class="date" v-if="isMine">
			{{ message.read ? 'Прочитано' : 'Не прочитано' }}
		</p>
	</div>
</template>

<script setup lang="ts">
import { TMessage } from '@/types/TMessage';
import { ref } from 'vue';

const props = defineProps<{
	message: TMessage;
	isMine: boolean;
}>();

const isContentVisible = ref(false);
</script>

<style scoped lang="scss">
.message {
	width: fit-content;
	max-width: 450px;
	background: rgba($color: #fff, $alpha: 0.8);
	border-radius: 4px;
	position: relative;
	padding-bottom: 16px;
	line-height: 130%;
	&:not(:last-child) {
		margin-bottom: 8px;
	}
	&:not(.is-mine) {
		padding-bottom: 2px;
	}
}
.is-mine {
	// background: rgba(255, 225, 250, 0.7);
	// color: rgba($color: #3f2c2c, $alpha: 0.5);
	margin-left: auto;
	padding-bottom: 8px;
	text-align: right;
	&:not(:last-child) {
		.date {
			display: none;
		}
	}
	.date {
		color: #3f2c2c;
	}
}
.date {
	font-size: 10px;
	text-align: right;
	color: rgba($color: #3f2c2c, $alpha: 0.3);
}
</style>
