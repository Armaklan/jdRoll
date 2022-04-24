module.exports = function(sessionProvider) {
    return function(req, res, next) {
        if(req.cookies && req.cookies.PHPSESSID) {
            sessionProvider.get(req.cookies.PHPSESSID).then((data) => {
                req.phpSession = data;
                next();
            }).catch(() => {
                next();
            });
        } else {
            next();
        }
    };
};
