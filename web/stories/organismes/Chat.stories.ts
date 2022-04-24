import { html } from 'lit';
import '../../components/organismes/Chat';

export default {
    title: 'jdRoll/Organismes',
    argTypes: {},
};

const Template = (args: any) => html`<jd-chat .messages=${args.messages}></jd-chat>`;

export const Chat = Template.bind({});

//@ts-ignore
Chat.args = {
    messages: [
        {from: 'Yoda', text: `Fais le, ou ne le fais pas, il n'y a pas d'essai`, time: new Date()},
        {from: 'Obi-Wan', text: `Tout est une question de points de vue`, time: new Date()},
        {from: 'Yoda', text: `Plus rapide, plus facile, plus séduisant, mais pas plus fort !`, time: new Date()}
    ]
};
