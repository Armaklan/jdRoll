var feedbackService = (function(){

    var component = {};

    var baseUrl = BASE_PATH + "/feedback/";


    var getUrlToUpdate = function(id) {
        return baseUrl + id;
    };

    var getUrlToVote = function(id) {
        return baseUrl + id + '/vote';
    };

    var setFeedbackMsg = function(msg) {
        $("#feedbacksMsg").html(msg);
    };

    var closeFeedback = function(id) {
        return $.ajax({
            type: "DELETE",
            url: getUrlToUpdate(id)
        });
    };

    var voteFeedback = function(id, score) {
        return $.ajax({
            type: "POST",
            url: getUrlToVote(id),
            data: {
                score: score
            }
        });
    };
    component.close = function(id) {
        bootbox.confirm("Fermer ce feedback ?", function(confirmed) {
          if(confirmed) {
            setFeedbackMsg('');
            closeFeedback(id).
            done(function(){
                setFeedbackMsg('<div class="alert alert-success">Feedback modifié avec succès</div>');
                $('#feedback' + id).css('display', 'none');
            });
          }
        });
    };

    component.voteUp = function(id) {
        setFeedbackMsg('');
        voteFeedback(id, 1).
        done(function(){
            setFeedbackMsg('<div class="alert alert-success">Vote enregistré avec succès</div>');
            $('#feedbackVote' + id).css('display', 'none');
        }).
        fail(function(){
            setFeedbackMsg('<div class="alert alert-danger">Vote non enregistré</div>');
        });
    };

    component.voteDown = function(id) {
        setFeedbackMsg('');
        voteFeedback(id, -1).
        done(function(){
            setFeedbackMsg('<div class="alert alert-success">Vote enregistré avec succès</div>');
            $('#feedbackVote' + id).css('display', 'none');
        }).
        fail(function(){
            setFeedbackMsg('<div class="alert alert-danger">Vote non enregistré</div>');
        });
    };


    return component;
})();
