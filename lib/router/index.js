var debug = require('debug')('router'),
	impl = require('implementjs');

// Two dependencies, an Express HTTP server and a handler
module.exports = function (server, handler) {
	debug('setting up routes...');

	// Validate handler's interface
	impl.implements(handler, {renderIndex: impl.F});
	
	server.post('/user', function(req,res) {
	
		console.log("enter /User with post verb for %s",req.query.action);
		if(req.query.action == "authentication")
			handler.authenticateUser(req,res);
		if(req.query.action == "resetPassword")
			handler.resetUserPassword(req,res);
	});
	
	server.get('/user', function(req,res) {
	
		console.log("enter user with get verb for %s",req.query.action);
		if(req.query.action == "getUserSession")
			handler.getUserSession(req,res);
	});
	
	server.delete('/user', function(req,res) {
	
		console.log("enter user with delete verb for %s",req.query.prop);
		if(req.query.prop == "session")
			handler.deleteUserSession(req,res);
	});
	
};
