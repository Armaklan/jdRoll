import { html } from 'lit';
import '../../components/atomes/Button';

export default {
  title: 'jdRoll/Atomes/Button',
  argTypes: {},
};

// More on component templates: https://storybook.js.org/docs/web-components/writing-stories/introduction#using-args
const Template = (args) => html`<jd-button label=${args.label}></jd-button>`;

export const Primary = Template.bind({});
// More on args: https://storybook.js.org/docs/web-components/writing-stories/args
Primary.args = {
  label: 'Button',
};