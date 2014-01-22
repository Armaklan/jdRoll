var debug = require('debug')('handler'),
	path = require('path'),
	md5 = require('MD5'),
	mailService = require('../mailer'),
	common = require('../common'),
	settings = require('../settings');

// Usually expects "db" as an injected dependency to manipulate the models
module.exports = function (db, logger){
	debug('setting up handlers...');

	return {
		resetUserPassword: function (req,res) {
		
			logger.info("enter resetUserPassword for %s",req.body.login);
		
			var alea = common.generateRandomString(50);
		
			var mailOptions = {
				from: settings.SMTPJDRollSender, // sender address
				to: "gajaf@hotmail.com", // list of receivers
				subject: settings.SMTPResetPasswordSubject, // Subject line
				text: settings.SMTPResetPasswordMail.replace("[alea]",alea), // plaintext body
				html: settings.SMTPResetPasswordMail.replace("[alea]",alea)// html body
			};
			
			var data  = {reinitAlea: alea,login: req.body.login};
			db.query("UPDATE user SET reinitDate = NOW(), reinitAlea = ? WHERE username = ? ",[alea,req.body.login],function(err, results) {
					
					var message = {};
					if(err || results.affectedRows == 0)
					{
						if(err)
						{
							message.message = "Une erreur interne s'est produite.";
							message.level = "danger";
						}
						else
						{
							message.message = "Login incorrect.";
							message.level = "warning";
						}
						res.status(403).json(message);
						
					}
					else
					{
						mailService.sendMail(mailOptions, function(error, response){
							if(error)
							{
								message.message = "Une erreur interne s'est produite.";
								message.level = "danger";
								res.status(403).json(message);
							}
							else
							{
								message.message = "Un mail contenant les instruction de réinitialisation a été envoyé à l'adresse configuré dans votre profil." + 
													"Si vous n'aviez pas configuré votre adresse e-mail ou si vous en avez perdu les accès, vous pouvez toujours plaidez votre cause auprès de nos admins bien aimés : contact@jdroll.org.";
								message.level = "success";
								res.status(200).json(message);
							}
						});
					}
				});
				
		},
		
		deleteUserSession: function (req, res) {
	
			req.session.destroy();
		
		},
		
		getUserSession: function (req, res) 
		{
			logger.info("enter UserGet for %s",req.session.username);
			var result = {};
			if(req.session.username)
			{
				
				result.username = req.session.username;
				result.profil = req.session.profil;
				res.status(200).json(result);
			}
			else
				res.status(403).json(result);
		
		},
		authenticateUser: function (req, res) {

			logger.info("enter UserPost for %s",req.body.login);
			db.getConnection(function(err, connection) {
				connection.release();	
			});
			db.query('SELECT * FROM user where username = ?', req.body.login,function(err, results) {
				
				if(Object.keys(results).length)
					{
						if(results[0].password == md5(req.body.password))
						{	
							req.session.username = results[0].username;
							req.session.profil = results[0].profil;
							logger.info("session username = %s",req.session.username);
							res.status(200).json(results[0]);
							
						}
						else
							res.status(403).json(results);
					}
					else
						res.status(403).json(results);	
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
