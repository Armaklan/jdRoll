function CharacterHandler(app, characterProvider, campagneProvider) {
    app.get('/apiv2/character/:id', function(req, res) {
        campagneProvider.get(req.params.id).then(function(campagne) {
          var publicOnly = (campagne.mj != req.phpSession.id);
          characterProvider.nameWith(req.params.id, req.query.text, publicOnly).then((characters) => {
            res.send(characters);
          });
        },function(err) {
          res.send(err);
        });
    });
}

module.exports = CharacterHandler;
