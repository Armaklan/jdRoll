var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var mysql = require('mysql');
var squel = require('squel').useFlavour('mysql');
var Message = require('./model/message.model.js');
var cookieParser = require('cookie-parser');
var phpSessionMiddleware = require('./middleware/phpsession.middleware.js');

function runApp(config) {
    var connection = connectDb(config.database);
    var services = declareProvider(connection);
    declareMiddleware(app, services);
    declareHandler(app, io, services);
    http.listen(5000, function() {
        console.log('listening on *:5000');
    });
}

function connectDb(dbConfig) {
    var mysqlConnection = mysql.createPool(dbConfig);
    return new (require('./database/connection.js'))(mysqlConnection);
}

function declareProvider(connection) {
    var providerDependencies = {
        connection: connection,
        sqlBuilder: squel
    };
    var services = {};
    services.chatProvider = importProvider('./provider/chat.provider.js', connection);
    services.userProvider = importProvider('./provider/user.provider', providerDependencies);
    services.sessionProvider = importProvider('./provider/session.provider', providerDependencies);
    services.statProvider = importProvider('./provider/stat.provider.js', providerDependencies);
    services.characterProvider = importProvider('./provider/character.provider.js', providerDependencies);
    services.campagneProvider = importProvider('./provider/campagne.provider.js', providerDependencies);
    return services;
}

function declareHandler(app, io, services) {
    require('./handler/user.handler.js')(app, services.userProvider);
    require('./handler/socket.handler.js')(io, services.chatProvider);
    require('./handler/stat.handler.js')(app, services.statProvider);
    require('./handler/character.handler')(app, services.characterProvider, services.campagneProvider);
}

function declareMiddleware(app, services) {
    app.use(cookieParser());
    app.use(phpSessionMiddleware(services.sessionProvider));
}

function importProvider(path, dependencies) {
    var Provider = require(path);
    return new Provider(dependencies);
}

module.exports = runApp;
