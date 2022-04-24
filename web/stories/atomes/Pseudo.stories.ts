import { html } from 'lit';
import '../../components/atomes/Pseudo';

export default {
  title: 'jdRoll/Atomes/Pseudo',
  argTypes: {},
};

const Template = (args: any) => html`<jd-pseudo name=${args.name} statut=${args.statut}></jd-pseudo>`;

export const Utilisateur = Template.bind({});

//@ts-ignore
Utilisateur.args = {
  name: 'MonPseudo',
  statut: 0
};


export const UtilisateurPrestige = Template.bind({});

//@ts-ignore
UtilisateurPrestige.args = {
  name: 'MonPseudo',
  statut: 1
};

export const Admin = Template.bind({});

//@ts-ignore
Admin.args = {
  name: 'MonPseudo',
  statut: 2
};
