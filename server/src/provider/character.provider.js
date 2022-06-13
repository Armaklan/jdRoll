"use strict";

var Character = require('../model/character.model.js');

function CharacterProvider(service) {
  
    const sqlBuilder = service.sqlBuilder;
    const connection = service.connection;

    this.nameWith = function(campagneId, searchText, publicOnly) {
      var query = baseSql()
        .where("personnages.campagne_id = ?", campagneId)
        .where("UPPER(personnages.name) like ?", "%" + searchText.toUpperCase() + "%" );

      if(publicOnly) {
        query = query.where("statut = ?", 0)
      }

      var sql = query.limit(5).toString();

      return connection
        .query(sql)
        .then(rowsMapper);
    }

    function baseSql() {
      return sqlBuilder
        .select("personnages.*")
        .from("personnages");
    }

    function rowsMapper(rows) {
      return rows.map(rowMapper);
    }

    function rowMapper(row) {
      return new Character(row);
    }
  
}

module.exports = CharacterProvider;
