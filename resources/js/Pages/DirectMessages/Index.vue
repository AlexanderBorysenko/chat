<template>
	<AppWrapper>
		<div class="">
			<UserDirectMessagesCard
				v-for="user in props.users"
				:key="user.id"
				:user="user"
			/>
		</div>
	</AppWrapper>
</template>

<script setup lang="ts">
import UserDirectMessagesCard from '@/Components/UserDirectMessagesCard.vue';
import AppWrapper from '@/Layouts/AppWrapper.vue';
import { TUser } from '@/types/TUser';
const props = defineProps<{
	users: TUser[];
}>();
</script>

<style scoped></style>
