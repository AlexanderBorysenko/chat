<template>
	<div
		class="message"
		:class="{ 'is-mine': isMine, 'p-8': message.type === 'text' }"
	>
		<button
			class="btn ml-auto w-100 text-center flex align-center justify-center"
			v-if="message.type === 'image' || message.type === 'video'"
			@click="isContentVisible = !isContentVisible"
		>
			{{ isContentVisible ? 'Сховати Моську' : 'Показати Моську' }}
		</button>
		<img
			:src="`/uploads?filename=${message.content}`"
			v-if="isContentVisible && message.type === 'image'"
			class="lh-0"
		/>
		<video
			:src="`/uploads?filename=${message.content}`"
			v-if="isContentVisible && message.type === 'video'"
			class="lh-0"
			controls
			muted
			playsinline
		></video>
		<div v-if="message.type === 'text'" v-html="message.content"></div>
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
