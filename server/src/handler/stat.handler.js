function StatHandler(app, statProvider) {
    app.get('/apiv2/stats/bymonth', function(req, res) {
        statProvider
            .byMonth()
            .then((rows) => res.send(rows));
    });

    app.get('/apiv2/stats/bymonth/:id', function(req, res) {
        statProvider
            .byMonthFor(req.params.id)
            .then((rows) => res.send(rows));
    });

    app.get('/apiv2/stats/bygame', function(req, res) {
        statProvider
            .byGame()
            .then((rows) => res.send(rows));
    });

    app.get('/apiv2/stats/my/byday', function(req, res) {
        if(!req.phpSession) emptyResponse(res);
        else statProvider
            .byUser(req.phpSession.id, req.query.beginDate)
            .then((rows) => res.send(rows));
    });

    app.get('/apiv2/stats/my/bygame', function(req, res) {
        if(!req.phpSession) emptyResponse(res);
        else statProvider
            .byUserAndGame(req.phpSession.id, req.query.beginDate)
            .then((rows) => res.send(rows));
    });

    function emptyResponse(response) {
        return response.send([]);
    }

}

module.exports = StatHandler;
