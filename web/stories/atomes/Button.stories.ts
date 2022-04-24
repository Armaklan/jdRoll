import { html } from 'lit';
import '../../components/atomes/Button';

export default {
  title: 'jdRoll/Atomes/Button',
  argTypes: {},
};

const Template = (args: any) => {
  console.log('renew template', args);
  return html`<jd-button label=${args.label}></jd-button>`;
}

export const Primary = Template.bind({});

//@ts-ignore
Primary.args = {
  label: 'Button',
};