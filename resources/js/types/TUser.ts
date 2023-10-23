import { TMessage } from './TMessage';

export type TCurrentUser = {
	id: number;
	name: string;
};

export type TUser = {
	id: number;
	name: string;
	current_user_unread_messages_count?: number;
	currentUserMessages?: TMessage[];
};
