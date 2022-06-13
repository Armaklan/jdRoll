var Message = require('../model/message.model.js');

function ChatProvider(connection) {
    this.saveMessage = function(msg) {
        return connection.query("INSERT INTO chat (message, username, to_username) VALUES (?,?,?)", [msg.text, msg.from, msg.to]).then((result) => result.insertId);
    };

    this.deleteMessage = function(msg) {
        return connection.query("DELETE FROM chat WHERE ID = ?", [msg.id]);
    };

    this.getLastMessage = function(username) {
        return connection.query("SELECT * FROM chat WHERE to_username = '' OR to_username = ? OR username = ? ORDER BY time DESC LIMIT 0, 100", [username, username]).then((rows) => {
            rows.reverse();
            return rows.map((r) => new Message(r));
        });
    };

}

module.exports = ChatProvider;
