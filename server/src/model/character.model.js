"use strict";

class Character {

  constructor(data) {
    this.id = data.id;
    this.userId = data.user_id;
    this.campagneId = data.campagne_id;
    this.name = data.name;
    this.concept = data.concept;
    this.avatar = data.avatar;
    this.publicDescription = data.publicDescription;
    this.privateDescription = data.privateDescription;
    this.technical = data.technical;
    this.statut = data.statut;
    this.catId = data.cat_id;
    this.perso_fields = data.perso_fields;
    this.widgets = data.widgets;
    this.thumbnails = "/files/thumbnails/perso_" + this.id + ".png"; 
  }

}

module.exports = Character;
