var debug = require('debug')('db'),
	mysql = require('mysql');

var pool  = mysql.createPool({
  host     : 'localhost',
  user     : 'root',
  password : '',
  database : 'jdRoll'
});

pool.on('connection', function(connection) {
  console.log('Mysql connected.');
});

module.exports = pool;
