var User = require('../../src/model/user.model.js');
var expect = require('chai').expect;

describe('User', function() {
  const ID = 1;
  const USERNAME = 'Vador';
  const PROFIL = 1;

  describe('#constructor()', function() {
    it('should build object', function() {
      var user = new User({
        id: ID,
        username: USERNAME,
        profil: PROFIL
      });
      expect(user).to.instanceof(User);
      expect(user.id).to.equal(ID);
      expect(user.username).to.equal(USERNAME);
      expect(user.profil).to.equal(PROFIL);
    });
  });
});
