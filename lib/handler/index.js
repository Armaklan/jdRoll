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
		
		logger.info("enter resetUserPassword for %s",req.session.username);
		
			var alea = common.generateRandomString(50);
		
			var mailOptions = {
				from: settings.SMTPJDRollSender, // sender address
				to: "gajaf@hotmail.com", // list of receivers
				subject: settings.SMTPResetPasswordSubject, // Subject line
				text: settings.SMTPResetPasswordMail.replace("[alea]",alea), // plaintext body
				html: settings.SMTPResetPasswordMail.replace("[alea]",alea)// html body
			};
			
			mailService.sendMail(mailOptions, function(error, response){
				if(error){
					console.log(error);
				}else{
					console.log("Message sent: " + response.message);
					var rer = {};
					res.status(200).json(rer);
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
