var debug = require('debug')('handler'),
	path = require('path'),
	md5 = require('MD5');

// Usually expects "db" as an injected dependency to manipulate the models
module.exports = function (db, logger){
	debug('setting up handlers...');

	return {
	UserDelete: function (req, res) {
		//TODO : Check querystring
		logger.info("enter UserDelete for %s",req.session.username);
         req.session.destroy();
		
		},
		UserGet: function (req, res) {
		//TODO :  CheckQueryString
		logger.info("enter UserGet for %s",req.session.username);
		var result = {};
          result.username = req.session.username;
          result.profil = req.session.profil;
		  res.json(result);
		
		},
		UserPost: function (req, res) {

			logger.info("enter UserPost for %s",req.body.login);
			db.getConnection(function(err, connection) {
				connection.release();	
			});
			db.query('SELECT * FROM user where username = ?', req.body.login,function(err, results) {
				if(err)
				{
					logger.info("query KO");
					logger.info(err);
					
				}
				else	
				{
					if(Object.keys(results).length)
					{
						logger.info("query OK %s %s",results[0].password,req.body.password);
						if(results[0].password == md5(req.body.password))
						{
							req.session.username = results[0].username;
							req.session.profil = results[0].profil;
							logger.info("session username = %s",req.session.username);
							res.json(results);
							
						}
						else
						{
							var result = [{}];
							res.json(result);
							
						
						}
					}
					else
					{
						var result = [{}];
						res.json(result);
							
						
					}
					
				}
			});
			
			
		},
		renderIndex: function (req, res) {

			logger.info("renderIndex API");
			db.getConnection(function(err, connection) {
				connection.release();	
			});
			
			
		}
	};
};
