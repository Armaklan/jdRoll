function StatProvider(service) {

    const sqlBuilder = service.sqlBuilder;
    const connection = service.connection;

    this.byMonth = function() {
        return connection.query(`SELECT
            DATE_FORMAT(create_date, '%Y,%m,01') as dat,
            count(*) as cpt
            FROM posts
            WHERE posts.user_id IS NOT NULL
            GROUP BY dat;`);
    };

    this.byMonthFor = function(campagneId) {
        var sql = baseQueryStat()
            .where('posts.user_id IS NOT NULL')
            .where('campagne.id = ?', campagneId)
            .group('dat')
            .toString();

        return connection.query(sql);
    };

    this.byGame = function() {
        var sql = baseQueryGameStat()
            .where('posts.user_id IS NOT NULL')
            .where(
                sqlBuilder.expr()
                    .and('campagne.statut = 0')
                    .or('campagne.statut IS NULL')
            )
            .group('game')
            .toString();

        return connection.query(sql);
    };

    this.byUserAndGame = function(userId, beginDate) {
        var query = baseQueryGameStat()
            .where('posts.user_id = ?', userId);
        query = beginDate ? query.where('create_date > ?', beginDate) : query;
        query = query.group('game');
        return connection.query(query.toString());
    };

    this.byUser = function(userId, beginDate) {
        var query = sqlBuilder.select()
            .field('DATE_FORMAT(create_date, \'%Y-%m-%d\')', 'dat')
            .field('count(*)', 'cpt')
            .from('posts')
            .where('user_id = ?', userId);
        query = beginDate ? query.where('create_date > ?', beginDate) : query;
        query = query.group('dat');
        return connection.query(query.toString());
    };


    function baseQueryStat () {
        return addFromClause(sqlBuilder
                             .select()
                             .field('DATE_FORMAT(create_date, \'%Y,%m,01\')', 'dat')
                             .field('count(*)', 'cpt'));
    }

    function baseQueryGameStat() {
        return addFromClause(sqlBuilder.select()
                             .field('campagne.name', 'game')
                             .field('count(*)', 'cpt'));
    }

    function addFromClause(query) {
        return query
            .from('posts')
            .left_join('topics', null, "topics.id = posts.topic_id")
            .left_join('sections', null, "sections.id = topics.section_id")
            .left_join('campagne', null, "campagne.id = sections.campagne_id");
    }
}

module.exports = StatProvider;
