var debug = require('debug')('router'),
	impl = require('implementjs');

// Two dependencies, an Express HTTP server and a handler
module.exports = function (server, handler) {
	debug('setting up routes...');

	// Validate handler's interface
	impl.implements(handler, {renderIndex: impl.F});
	
	//server.get('/api/test', handler.renderIndex);
	server.post('/user', handler.UserPost);
	server.get('/user', handler.UserGet);
	server.delete('/user', handler.UserDelete);
	
};
