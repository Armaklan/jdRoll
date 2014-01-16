var debug = require('debug')('handler');

// Usually expects "db" as an injected dependency to manipulate the models
module.exports = function (db, logger) {
	debug('setting up handlers...');

	return {
		renderIndex: function (req, res) {
			logger.info("renderIndex API");
			db.getConnection(function(err, connection) {
				connection.release();	
			});
			var toto = ['toto', 'titi']
			res.json(toto);
		}
	};
};
