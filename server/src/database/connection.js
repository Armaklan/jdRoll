function DatabaseConnection(mysqlConnection) {
    this.query = function(query, options) {
        var promise = new Promise((resolve, reject) => {
            mysqlConnection.query(query, options, (err, rows) => {
                if(!err) resolve(rows);
                else reject(err);
            });
        });
        return promise;
    };
}

module.exports = DatabaseConnection;
