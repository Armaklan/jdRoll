var Message = require('../../src/model/message.model.js');
var expect = require('chai').expect;

describe('Message', function() {
    const ID = 1;
    const FROM = 'vador';
    const TO = 'luke';
    const TEXT = 'Je suis ton père !';

    describe('#constructor()', function() {
        it('should build from Event message', function() {
           var msg = new Message({
               id: 1,
               from: FROM,
               text: TEXT
           });
           expect(msg).to.be.an.instanceof(Message);
            expect(msg.id).to.equal(ID);
            expect(msg.from).to.equal(FROM);
            expect(msg.text).to.equal(TEXT);
            expect(msg.to).to.be.undefined;
            expect(msg.private).to.be.false;
        });

        it('should build private from Event message', function() {
            var msg = new Message({
                id: 1,
                from: FROM,
                to: TO,
                text: TEXT
            });
            expect(msg).to.be.an.instanceof(Message);
            expect(msg.id).to.equal(ID);
            expect(msg.from).to.equal(FROM);
            expect(msg.text).to.equal(TEXT);
            expect(msg.to).to.equal(TO);
            expect(msg.private).to.be.true;
        });

        it('should build from Database row', function() {
            var msg = new Message({
                id: 1,
                username: FROM,
                message: TEXT
            });
            expect(msg).to.be.an.instanceof(Message);
            expect(msg.id).to.equal(ID);
            expect(msg.from).to.equal(FROM);
            expect(msg.text).to.equal(TEXT);
            expect(msg.to).to.be.undefined;
            expect(msg.private).to.be.false;
        });

        it('should build private from Database row', function() {
            var msg = new Message({
                id: 1,
                username: FROM,
                to_username: TO,
                message: TEXT
            });
            expect(msg).to.be.an.instanceof(Message);
            expect(msg.id).to.equal(ID);
            expect(msg.from).to.equal(FROM);
            expect(msg.text).to.equal(TEXT);
            expect(msg.to).to.equal(TO);
            expect(msg.private).to.be.true;
        });

        it('should transform /me to action');

        it('should strip tags');

        it('should interpret link');

        it('should interpret smiley');
    });
});

