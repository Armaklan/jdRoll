var nodemailer = require('nodemailer'),
	settings   = require('./settings');

var smtpTransport = nodemailer.createTransport("SMTP",{
				service: settings.SMTPService,
				auth: {
					user: settings.SMTPLogin,
					pass: settings.SMTPPassword
				}
			});
			

module.exports = smtpTransport;