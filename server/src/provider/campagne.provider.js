"use strict";

var Campagne = require('../model/campagne.model');

function CampagneProvider(service) {
  
    const sqlBuilder = service.sqlBuilder;
    const connection = service.connection;

    this.get = function(id) {
      var sql =  sqlBuilder
        .select('campagne.*')
        .from("campagne")
        .where("campagne.id = ?", id)
        .toString();

      return connection
        .query(sql)
        .then(rowMapper);
        
    };

    function rowMapper(rows) {
      if(rows) {
        return new Campagne(rows[0]);
      }
      return undefined;
    }
}

module.exports = CampagneProvider;
