import { html } from 'lit';
import '../../components/molecules/ChatMessage';

export default {
    title: 'jdRoll/Molecules/ChatMessage',
    argTypes: {},
};

const Template = (args: any) => html`<jd-chat-message utilisateurName=${args.utilisateurName} message=${args.message} .postDate=${args.postDate}></jd-chat-message>`;

export const Message = Template.bind({});

//@ts-ignore
Message.args = {
    utilisateurName: 'MonPseudo',
    message: 'Message test histoire de voir',
    postDate: new Date()
};
