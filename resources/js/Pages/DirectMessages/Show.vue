<template>
	<AppWrapper>
		<div class="messages-wrapper col p-8" ref="messagesWrapper">
			<TransitionGroup name="fade">
				<Message
					v-for="message in props.messages"
					:key="message.id"
					:message="message"
					:isMine="message.sender_id === auth.user.id"
					@vue:mounted="scrollToView"
				/>
			</TransitionGroup>
		</div>
		<div class="message-type-area flex p-8">
			<Transition name="fade">
				<span class="is-typing-message" v-if="isNyamNyamTyping"
					>Жамкає Лапкою...</span
				>
			</Transition>
			<textarea
				type="text"
				class="form-control col mr-8"
				rows="2"
				ref="textarea"
				v-model="messageForm.content"
				:disabled="messageForm.processing"
				@input="updateTyping"
			/>
			<button
				class="btn w-fit-content lh-0 ml-auto flex align-center"
				@click="submit"
				:disabled="messageForm.processing"
			>
				<IoOutlinePaw class="fs-2 mr-8" />
				Жамк
			</button>
		</div>
	</AppWrapper>
</template>

<script setup lang="ts">
import Message from '@/Components/Message.vue';
import AppWrapper from '@/Layouts/AppWrapper.vue';
import { TMessage } from '@/types/TMessage';
import { TUser } from '@/types/TUser';
import { useForm, usePage } from '@inertiajs/vue3';
import { IoOutlinePaw } from '@kalimahapps/vue-icons';
import axios from 'axios';
import { watch } from 'vue';
import { toRef } from 'vue';
import { toRefs } from 'vue';
import { onUnmounted } from 'vue';
import { onMounted } from 'vue';
import { ref } from 'vue';

const readMessages = async () => {
	await axios.post(route('directMessages.ajaxReadMessages', props.user.id));
};

const auth = usePage().props.auth;

const props = defineProps<{
	messages: TMessage[];
	user: TUser;
}>();
const messages = toRef(props.messages);

const messageForm = useForm({
	content: ''
});

const isTyping = ref(false);
const typingTimeout = ref<ReturnType<typeof setTimeout> | null>(null);
const updateTyping = () => {
	if (typingTimeout.value) {
		clearTimeout(typingTimeout.value);
	}
	isTyping.value = true;
	typingTimeout.value = setTimeout(() => {
		isTyping.value = false;
	}, 1500);
};

const isNyamNyamTyping = ref(false);

const channel = ref<ReturnType<typeof window.Echo.private> | null>(null);

onMounted(() => {
	readMessages();

	channel.value = window.Echo.private(
		`directMessages.${props.user.id + auth.user.id}`
	);
	channel.value
		?.listen('NewDirectMessage', (e: { message: TMessage }) => {
			if (e.message.sender_id !== auth.user.id) {
				props.messages.push(e.message);
				readMessages();
			}
		})
		.listen('ReadUserMessages', (e: { user: TUser }) => {
			props.messages.forEach(message => {
				message.read = true;
			});
		})
		.listenForWhisper(
			`User.${props.user.id}.typing`,
			(e: { isTyping: boolean }) => {
				isNyamNyamTyping.value = e.isTyping;
				scrollToView();
			}
		);
});
// stop listening on unmount
onUnmounted(() => {
	channel.value
		?.stopListening('NewDirectMessage')
		.stopListening('ReadUserMessages')
		.stopListening(`User.${props.user.id}.typing`);
});

// send typing status
watch(isTyping, isTyping => {
	//@ts-ignore
	channel.value.whisper(`User.${auth.user.id}.typing`, {
		isTyping
	});
});

const submit = () => {
	if (messageForm.content.length === 0) return;
	messageForm.post(route('directMessages.store', props.user.id), {
		onSuccess: () => {
			messageForm.reset();
		}
	});
};

// messages wrapper scroll to bottom
const messagesWrapper = ref<HTMLDivElement | null>(null);
const scrollToView = () => {
	if (messagesWrapper.value) {
		messagesWrapper.value.scrollTop = messagesWrapper.value.scrollHeight;
	}
};

// submit if single enter pressed
const submitIfSingleEnterPressed = (e: KeyboardEvent) => {
	if (e.key === 'Enter' && !e.shiftKey) {
		submit();
	}
};
onMounted(() => {
	window.addEventListener('keydown', submitIfSingleEnterPressed);
});
onUnmounted(() => {
	window.removeEventListener('keydown', submitIfSingleEnterPressed);
});
</script>

<style scoped lang="scss">
.messages-wrapper {
	overflow: auto;
	position: relative;
}
.message-type-area {
	position: relative;
}
.is-typing-message {
	position: absolute;
	bottom: 100%;
	left: 8px;
	background: #ffadc9;
	padding: 4px 8px;
	border-radius: 4px;
	color: #fff;
}
// transition
.fade-enter-active,
.fade-leave-active {
	transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
	opacity: 0;
}
</style>
