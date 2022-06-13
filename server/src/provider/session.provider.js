var PHPUnserialize = require('php-unserialize');

function SessionProvider(dependencies) {
    const connection = dependencies.connection;
    const sqlBuilder = dependencies.sqlBuilder;

    this.get = function(sessionId) {
        var sql = baseSql().where("session_id = ?", sessionId).toString();
        return connection.query(sql).then(parsePhpSession);
    };

    function parsePhpSession(rows) {
        if(!rows || rows.length === 0) return undefined;

        var data = rows[0].session_value;
        data = PHPUnserialize.unserializeSession(data);
        return data._sf2_attributes.user;
    }

    function baseSql() {
        return sqlBuilder.select()
            .from('session');
    }

}

module.exports = SessionProvider;
