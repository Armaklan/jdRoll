function UserHandler(app, userProvider) {
    app.get('/apiv2/users/connected', function(req, res) {
        userProvider.connected().then((users) => {
            res.send(users);
        });
    });

    app.get('/apiv2/users', function(req, res) {
        userProvider.all().then((users) => {
            res.send(users);
        });
    });
}

module.exports = UserHandler;
