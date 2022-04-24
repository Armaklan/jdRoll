"use strict";

class User {

  constructor(data) {
    this.id = data.id;
    this.username = data.username;
    this.profil = data.profil;
  }
}

module.exports = User;
