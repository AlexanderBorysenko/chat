<template>
	<AppWrapper>
		<div class="messages-wrapper col p-8" ref="messagesWrapper">
			<TransitionGroup name="fade">
				<Message
					v-for="message in props.messages"
					:key="message.id"
					:message="message"
					:isMine="message.sender_id === auth.user.id"
				/>
			</TransitionGroup>
		</div>
		<div class="message-type-area flex p-8">
			<Transition name="fade">
				<span class="is-typing-message" v-if="isNyamNyamTyping"
					>Жамкає Лапкою...</span
				>
			</Transition>
			<input
				type="file"
				class="btn hidden"
				ref="mediaFileInput"
				:disabled="mediaFileForm.processing"
				@change="onMediaFileInputChange"
			/>
			<button
				class="btn lh-0 mr-8"
				@click="mediaFileInput?.click()"
				:disabled="mediaFileForm.processing"
			>
				<BxCamera class="fs-2" />
			</button>
			<textarea
				type="text"
				class="form-control col mr-8"
				rows="2"
				ref="messageTextarea"
				v-model="messageForm.content"
				:disabled="messageForm.processing"
				@input="updateTyping"
			/>
			<button
				class="btn w-fit-content lh-0 ml-auto flex align-center"
				@click="submit"
				:disabled="messageForm.processing"
			>
				<IoOutlinePaw class="fs-2" />
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
import { BxCamera } from '@kalimahapps/vue-icons';
import axios from 'axios';
import { watch } from 'vue';
import { toRef } from 'vue';
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

const messagesWrapper = ref<HTMLDivElement | null>(null);
const scrollToView = () => {
	setTimeout(() => {
		if (messagesWrapper.value) {
			messagesWrapper.value.scrollTop =
				messagesWrapper.value.scrollHeight;
		}
	}, 100);
};

const messageTextarea = ref<HTMLTextAreaElement | null>(null);
const submit = () => {
	if (messageForm.content.length === 0) return;
	messageForm.post(route('directMessages.store', props.user.id), {
		onSuccess: () => {
			messageForm.reset();
		},
		onFinish: () => {
			messageTextarea.value?.focus();
		}
	});
};
onMounted(() => {
	messageTextarea.value?.focus();
	scrollToView();
});

const mediaFileInput = ref<HTMLInputElement | null>(null);
const mediaFileForm = useForm<{
	file: File | null;
}>({
	file: null
});
const onMediaFileInputChange = (e: Event) => {
	const target = e.target as HTMLInputElement;
	if (target.files) {
		mediaFileForm.file = target.files[0];
	}
	submitMediaFile();
};

// following code will listen to paste event on textarea and will put clipboard file into mediaFileForm.file if there is a file in clipboard and submit it
const onPaste = (e: ClipboardEvent) => {
	const items = e.clipboardData?.items;
	if (!items) return;
	for (let i = 0; i < items.length; i++) {
		if (items[i].type.indexOf('image') === -1) continue;
		const file = items[i].getAsFile();
		if (!file) continue;
		mediaFileForm.file = file;
		submitMediaFile();
	}
};

onMounted(() => {
	messageTextarea.value?.addEventListener('paste', onPaste);
});

const submitMediaFile = () => {
	if (!mediaFileForm.file) return;
	mediaFileForm.post(
		route('directMessages.storeMediaFileMessage', props.user.id),
		{
			onSuccess: () => {
				mediaFileForm.reset();
			},
			onError: data => {
				alert(data.file);
			}
		}
	);
};

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

const subscribeToChannel = () => {
	console.log('Підписуюсь на канал');
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
		.listen(
			'ReadUserMessages',
			(e: { user: TUser; readingUser: TUser }) => {
				props.messages.forEach(message => {
					if (e.readingUser.id === props.user.id) {
						message.read = true;
					}
				});
			}
		)
		.listenForWhisper(
			`User.${props.user.id}.typing`,
			(e: { isTyping: boolean }) => {
				isNyamNyamTyping.value = e.isTyping;
			}
		);
};

onMounted(() => {
	readMessages();
	subscribeToChannel();

	// handle channel disconnect
	channel.value?.error(() => {
		console.log('Сокет заглючив, перезавантажуюсь...');
		try {
			subscribeToChannel();
		} catch (e) {
			console.log('Не вдалося перезавантажити сокет');
			setTimeout(() => {
				subscribeToChannel();
			}, 2000);
		}
	});
});

onUnmounted(() => {
	channel.value
		?.stopListening('NewDirectMessage')
		.stopListening('ReadUserMessages')
		.stopListening(`User.${props.user.id}.typing`);

	channel.value = null;
});

// send typing status
watch(isTyping, isTyping => {
	//@ts-ignore
	channel.value.whisper(`User.${auth.user.id}.typing`, {
		isTyping
	});
});

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
