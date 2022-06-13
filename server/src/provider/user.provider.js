"use strict";

var User = require('../model/user.model.js');

function UserProvider(service) {

    const sqlBuilder = service.sqlBuilder;
    const connection = service.connection;


    this.all = function() {
        var sql = baseSql()
            .where("user.profil >= ?", 0)
            .where("time > DATE_SUB(now(), INTERVAL 1 MONTH)")
            .order("user.username")
            .toString();
        return connection.query(sql).then(rowsMapper);
    };

    this.connected = function() {
        var sql = baseSql()
            .where("time > DATE_SUB(now(), INTERVAL 5 MINUTE)")
            .order("user.profil", false)
            .order("user.username")
            .toString();
        return connection.query(sql).then(rowsMapper);
    };

    function baseSql() {
        return sqlBuilder
            .select("user.*")
            .from("user")
            .join("last_action", null, "last_action.user_id = user.id");
    }

    function rowsMapper(rows) {
        return rows.map(rowMapper);
    }

    function rowMapper(row) {
        return new User(row);
    }
}

module.exports = UserProvider;
