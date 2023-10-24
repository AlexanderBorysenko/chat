<template>
	<div class="message pv-8 ph-12" :class="{ 'is-mine': isMine }">
		<div v-html="message.content"></div>
		<p class="date" v-if="!isMine">
			{{ new Date(message.created_at).toLocaleString() }} -
		</p>
		<p class="date" v-if="isMine">
			{{ message.read ? 'Прочитано' : 'Не прочитано' }}
		</p>
	</div>
</template>

<script setup lang="ts">
import { TMessage } from '@/types/TMessage';

const props = defineProps<{
	message: TMessage;
	isMine: boolean;
}>();
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
	opacity: 0.4;
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
